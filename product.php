<?php
class OptimizedProductFilter {
    private $mysqli;
    private $fieldTypeMap = [
        'id' => 'int',
        'user_id' => 'int',
        'Return_days' => 'int',
        'min_qty' => 'int',
        'refundable' => 'int',
        'unit_price' => 'float',
        'purchase_price' => 'float',
        'tax' => 'float',
        'discount' => 'float',
        'current_stock' => 'int',
        'free_shipping' => 'int',
        'free_delivery' => 'int',
        'minimum_order_qty' => 'int',
        'status' => 'int',
        'shipping_cost' => 'float',
        'multiply_qty' => 'int',
        'available_instant_delivery' => 'int',
        'final_unit_price' => 'float',
        'total_orders' => 'int',
        'avg_rating' => 'float'
    ];

    public function __construct() {
        $this->initializeDatabase();
    }

    private function initializeDatabase() {
        $host = 'localhost';
        $dbname = 'c2int';
        $user = 'c2int';
        $password = '4Ua#Aa4TBqhj';

        $this->mysqli = new mysqli($host, $user, $password, $dbname);
        
        if ($this->mysqli->connect_errno) {
            $this->sendErrorResponse("Database connection failed: " . $this->mysqli->connect_error);
        }

        $this->mysqli->set_charset("utf8mb4");
    }

    public function handleRequest() {
        try {
            $params = $this->sanitizeInputs();
            
            if ($params['showFiltersOptions']) {
                $response = $this->getFiltersOnly($params);
            } else {
                $response = $this->getProducts($params);
            }
            
            $this->sendJsonResponse($response);
        } catch (Exception $e) {
            $this->sendErrorResponse($e->getMessage());
        } finally {
            $this->mysqli->close();
        }
    }

    private function sanitizeInputs() {
        return [
            'limit' => max(1, min(100, (int)($_POST['limit'] ?? 10))),
            'page' => max(1, (int)($_POST['page'] ?? 1)),
            'showFiltersOptions' => (bool)($_POST['showFiltersOptions'] ?? false),
            'categoryIds' => $this->sanitizeCommaSeparated($_POST['categoryIds'] ?? ''),
            'subCategoryIds' => $this->sanitizeCommaSeparated($_POST['subCategoryIds'] ?? ''),
            'subSubCategoryIds' => $this->sanitizeCommaSeparated($_POST['subSubCategoryIds'] ?? ''),
            'brandIds' => $this->sanitizeCommaSeparated($_POST['brandIds'] ?? ''),
            'sellerIds' => $this->sanitizeCommaSeparated($_POST['sellerIds'] ?? ''),
            'searchTerm' => trim($_POST['search'] ?? ''),
            'colorIds' => $this->sanitizeCommaSeparated($_POST['colors'] ?? ''),
            'specifications' => $this->sanitizeCommaSeparated($_POST['specifications'] ?? ''),
            'technicalSpecifications' => $this->sanitizeCommaSeparated($_POST['technicalSpecifications'] ?? ''),
            'isFeatured' => (bool)($_POST['isFeatured'] ?? false),
            'isFreeShipping' => (bool)($_POST['isFreeShipping'] ?? false),
            'isDealOfTheDay' => (bool)($_POST['isDealOfTheDay'] ?? false),
            'discountPercentageFilter' => !empty($_POST['discount']) ? (int)$_POST['discount'] : null,
            'material' => $this->sanitizeCommaSeparated($_POST['material'] ?? ''),
            'otherColors' => $this->sanitizeCommaSeparated($_POST['otherColors'] ?? ''),
            'size' => $this->sanitizeCommaSeparated($_POST['size'] ?? ''),
            'type' => $this->sanitizeCommaSeparated($_POST['type'] ?? ''),
            'minimumPrice' => !empty($_POST['minimumPrice']) ? (float)$_POST['minimumPrice'] : null,
            'maximumPrice' => !empty($_POST['maximumPrice']) ? (float)$_POST['maximumPrice'] : null,
            'rating' => !empty($_POST['rating']) ? (int)$_POST['rating'] : null,
            'sortOrder' => $_POST['sortOrder'] ?? ''
        ];
    }

    private function sanitizeCommaSeparated($input) {
        if (empty($input)) return '';
        $items = explode(',', $input);
        $sanitized = array_filter(array_map('trim', $items), function($item) {
            return !empty($item) && (is_numeric($item) || strlen($item) <= 100);
        });
        return implode(',', array_map([$this->mysqli, 'real_escape_string'], $sanitized));
    }

    private function getProducts($params) {
        $productData = $this->getFilteredProductIds($params);
        
        if (empty($productData)) {
            return ['total_size' => 0, 'products' => [], 'filters' => []];
        }

        $products = $this->enrichProductData($productData);
        $totalCount = $this->getTotalCount($params);

        return [
            'total_size' => $totalCount,
            'products' => $products,
            'filters' => []
        ];
    }

    private function getFilteredProductIds($params) {
        $query = "
            SELECT p.id, p.name, p.slug, p.images, p.colors, p.choice_options, p.variation,
                   p.category_ids, p.color_image, p.attributes,
                   sku.variant_mrp, sku.discount, sku.discount_type,
                   sku.thumbnail_image, sku.image, sku.listed_price,
                   CASE
                       WHEN sku.discount IS NULL THEN CAST(sku.variant_mrp AS DECIMAL(10, 2))
                       WHEN (sku.discount_type = 'percent' OR sku.discount_type = 'percentage') THEN 
                           CAST(ROUND(sku.variant_mrp * (1 - (CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10,2)) / 100)), 2) AS DECIMAL(10, 2))
                       WHEN (sku.discount_type = 'flat' OR sku.discount_type = 'amount') THEN 
                           CAST(ROUND(sku.variant_mrp - CAST(sku.discount AS DECIMAL(10, 2)), 2) AS DECIMAL(10, 2))
                       ELSE CAST(sku.variant_mrp AS DECIMAL(10, 2))
                   END AS final_unit_price
            FROM products p
            INNER JOIN sku_product_new sku ON p.id = sku.product_id
            INNER JOIN sellers seller ON p.user_id = seller.id
            INNER JOIN shops shop ON seller.id = shop.seller_id
        ";

        $whereConditions = $this->buildOptimizedWhereConditions($params);
        
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $orderClause = $this->getOptimizedOrderClause($params);
        if ($orderClause) {
            $query .= " ORDER BY $orderClause";
        } 

        $offset = ($params['page'] - 1) * $params['limit'];
        $query .= " LIMIT {$params['limit']} OFFSET $offset";

        $result = $this->mysqli->query($query);
        if (!$result) {
            throw new Exception("Query failed: " . $this->mysqli->error);
        }

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $this->processBasicProductRow($row);
        }
        $result->close();

        return $products;
    }

    private function buildOptimizedWhereConditions($params) {
        $conditions = [
            'p.status = 1',
            'p.request_status = 1',
            'sku.variant_mrp IS NOT NULL',
            'seller.status = "approved"',
            'shop.vacation_status = 0',
            'shop.temporary_close = 0'
        ];

        if (!empty($params['subSubCategoryIds'])) {
            $conditions[] = "p.sub_sub_category_id IN ({$params['subSubCategoryIds']})";
        } elseif (!empty($params['subCategoryIds'])) {
            $conditions[] = "p.sub_category_id IN ({$params['subCategoryIds']})";
        } elseif (!empty($params['categoryIds'])) {
            $conditions[] = "p.category_id IN ({$params['categoryIds']})";
        }

        if (!empty($params['brandIds'])) {
            $conditions[] = "p.brand_id IN ({$params['brandIds']})";
        }

        if (!empty($params['sellerIds'])) {
            $conditions[] = "p.user_id IN ({$params['sellerIds']})";
        }

        if ($params['isFeatured']) {
            $conditions[] = "p.featured = 1";
        }

        if ($params['isFreeShipping']) {
            $conditions[] = "p.free_delivery = 1";
        }

        if ($params['minimumPrice'] !== null) {
            $conditions[] = "sku.variant_mrp >= {$params['minimumPrice']}";
        }

        if ($params['maximumPrice'] !== null) {
            $conditions[] = "sku.variant_mrp <= {$params['maximumPrice']}";
        }

        if (!empty($params['searchTerm'])) {
            $searchTerm = $this->mysqli->real_escape_string($params['searchTerm']);
            $conditions[] = "(p.name LIKE '%$searchTerm%' OR p.slug LIKE '%$searchTerm%')";
        }

        $this->addJsonFilters($conditions, $params);

        if ($params['isDealOfTheDay']) {
            $conditions[] = "EXISTS (
                SELECT 1 FROM deal_of_the_days d 
                WHERE d.product_id = p.id 
                AND d.status = 1 
                AND d.start_date_time <= NOW() 
                AND d.expire_date_time >= NOW()
            )";
        }

        if ($params['sortOrder'] === 'discount') {
            $conditions[] = "sku.discount IS NOT NULL AND sku.discount > 0";
        }

        return $conditions;
    }

    private function addJsonFilters(&$conditions, $params) {
        if (!empty($params['colorIds'])) {
            $colorArray = explode(',', $params['colorIds']);
            $colorConditions = [];
            
            foreach ($colorArray as $color) {
                $color = trim($color);
                if (!empty($color)) {
                    $colorEscaped = $this->mysqli->real_escape_string($color);
                    $colorConditions[] = "JSON_CONTAINS(p.colors, '\"$colorEscaped\"')";
                }
            }
            
            if (!empty($colorConditions)) {
                $conditions[] = '(' . implode(' OR ', $colorConditions) . ')';
            }
        }

        $choiceFilters = [
            'material' => 'Material',
            'otherColors' => 'Colour',
            'size' => 'Size',
            'type' => 'Type'
        ];
        
        foreach ($choiceFilters as $param => $title) {
            if (!empty($params[$param])) {
                $items = explode(',', $params[$param]);
                $itemConditions = [];
                
                foreach ($items as $item) {
                    $item = trim($item);
                    if (!empty($item)) {
                        $itemEscaped = $this->mysqli->real_escape_string($item);
                        $itemConditions[] = "JSON_CONTAINS(p.choice_options, '{\"title\": \"$title\", \"options\": [\"$itemEscaped\"]}')";
                    }
                }
                
                if (!empty($itemConditions)) {
                    $conditions[] = '(' . implode(' OR ', $itemConditions) . ')';
                }
            }
        }
    }

    private function getOptimizedOrderClause($params) {
        $orderMap = [
            'priceHigh' => 'final_unit_price DESC',
            'priceLow' => 'final_unit_price ASC',
            'discount' => 'CASE 
                WHEN sku.discount_type = "percent" THEN CAST(REPLACE(sku.discount, "%", "") AS DECIMAL(5,2)) 
                WHEN sku.discount_type = "flat" THEN (CAST(sku.discount AS DECIMAL(10,2)) / sku.variant_mrp) * 100
                ELSE 0 
            END DESC',
            'newArrivals' => 'p.created_at DESC',
            'bestSelling' => 'p.id DESC',
            'topProducts' => 'p.id DESC'
        ];

        return $orderMap[$params['sortOrder']] ?? 'RAND()';
    }

    private function processBasicProductRow($row) {
        $jsonFields = ['images', 'category_ids', 'color_image', 'choice_options', 'variation', 'colors', 'attributes'];
        
        foreach ($jsonFields as $field) {
            if (isset($row[$field]) && is_string($row[$field])) {
                $decoded = json_decode($row[$field], true);
                $row[$field] = is_array($decoded) ? $decoded : [];
            }
        }

        if (!empty($row['image']) && is_string($row['image'])) {
            $skuImages = json_decode($row['image'], true);
            if (is_array($skuImages)) {
                $row['images'] = $skuImages;
            }
        }

        $row['thumbnail'] = $row['thumbnail_image'] ?? ($row['images'][0] ?? null);
        $row['unit_price'] = (float)$row['variant_mrp'];
        $row['discount'] = isset($row['discount']) ? (int)$row['discount'] : 0;
        $row['slug'] = (string)($row['slug'] ?? '');

        foreach ($this->fieldTypeMap as $field => $type) {
            if (isset($row[$field])) {
                $row[$field] = $type === 'int' ? (int)$row[$field] : (float)$row[$field];
            }
        }

        return $row;
    }

    private function enrichProductData($products) {
        if (empty($products)) return [];

        $productIds = array_column($products, 'id');
        $idsList = implode(',', array_map('intval', $productIds));
        $additionalData = $this->getBatchAdditionalData($idsList);

        foreach ($products as &$product) {
            $productId = $product['id'];
            
            $product['total_orders'] = isset($additionalData['orders'][$productId]) 
                ? (int)$additionalData['orders'][$productId] : 0;
            
            $product['avg_rating'] = isset($additionalData['ratings'][$productId]) 
                ? (float)$additionalData['ratings'][$productId] : 0.0;

            if (isset($additionalData['deals'][$productId])) {
                $deal = $additionalData['deals'][$productId];
                $product['deal_status'] = (int)$deal['status'];
                $product['start_date_time'] = $deal['start_date_time'];
                $product['expire_date_time'] = $deal['expire_date_time'];
            } else {
                $product['deal_status'] = 0;
                $product['start_date_time'] = null;
                $product['expire_date_time'] = null;
            }

            if (isset($additionalData['specs'][$productId])) {
                $spec = $additionalData['specs'][$productId];
                $product['specification'] = $spec['specification'];
                $product['key_features'] = $spec['key_features'];
                $product['technical_specification'] = $spec['technical_specification'];
            }

            if (isset($additionalData['tags'][$productId])) {
                $product['tags'] = $additionalData['tags'][$productId];
            }
        }
        return $products;
    }

    private function getBatchAdditionalData($idsList) {
        $data = ['orders' => [], 'ratings' => [], 'deals' => [], 'specs' => [], 'tags' => []];

        $queries = [
            'orders' => "SELECT product_id, COUNT(*) as total_orders FROM order_details WHERE product_id IN ($idsList) GROUP BY product_id",
            'ratings' => "SELECT product_id, ROUND(AVG(rating), 2) as avg_rating FROM reviews WHERE product_id IN ($idsList) AND status = 1 GROUP BY product_id",
            'deals' => "SELECT product_id, status, start_date_time, expire_date_time FROM deal_of_the_days WHERE product_id IN ($idsList)",
            'specs' => "SELECT product_id, specification, key_features, technical_specification FROM key_specification_values WHERE product_id IN ($idsList)",
            'tags' => "SELECT pt.product_id, GROUP_CONCAT(t.tag) as tags FROM product_tag pt JOIN tags t ON pt.tag_id = t.id WHERE pt.product_id IN ($idsList) GROUP BY pt.product_id"
        ];

        foreach ($queries as $key => $query) {
            $result = $this->mysqli->query($query);
            while ($row = $result->fetch_assoc()) {
                if ($key === 'deals') {
                    $data[$key][$row['product_id']] = $row;
                } else {
                    $data[$key][$row['product_id']] = $key === 'orders' ? $row['total_orders'] : 
                        ($key === 'ratings' ? $row['avg_rating'] : 
                        ($key === 'tags' ? $row['tags'] : $row));
                }
            }
            $result->close();
        }

        return $data;
    }

    private function getTotalCount($params) {
        $countQuery = "
            SELECT COUNT(DISTINCT p.id) as total
            FROM products p
            INNER JOIN sku_product_new sku ON p.id = sku.product_id
            INNER JOIN sellers seller ON p.user_id = seller.id
            INNER JOIN shops shop ON seller.id = shop.seller_id
        ";

        $whereConditions = $this->buildOptimizedWhereConditions($params);
        
        if (!empty($whereConditions)) {
            $countQuery .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $result = $this->mysqli->query($countQuery);
        $row = $result->fetch_assoc();
        $result->close();
        
        return (int)$row['total'];
    }

    // **COMPLETE FILTERS IMPLEMENTATION**
    private function getFiltersOnly($params) {
        $filters = [
            'categories' => [],
            'brands' => [],
            'colors' => [],
            'materials' => [],
            'sizes' => [],
            'types' => [],
            'otherColors' => [],
            'free_delivery' => 0,
            'minPrice' => 0,
            'maxPrice' => 0,
            'specifications' => [],
            'technical_specifications' => []
        ];

        // Build base query for filters
        $query = "
            SELECT p.*, ksv.specification, ksv.key_features, ksv.technical_specification,
                   c.specification as specs, c.technical_specification as techs, c.key_features as keyf,
                   CASE
                       WHEN sku.discount IS NULL THEN CAST(sku.variant_mrp AS DECIMAL(10, 2))
                       WHEN sku.discount_type = 'percent' THEN 
                           CAST(ROUND(sku.variant_mrp * (1 - (CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10,2)) / 100)), 2) AS DECIMAL(10, 2))
                       WHEN sku.discount_type = 'flat' THEN 
                           CAST(ROUND(sku.variant_mrp - CAST(sku.discount AS DECIMAL(10, 2)), 2) AS DECIMAL(10, 2))
                       ELSE CAST(sku.variant_mrp AS DECIMAL(10, 2))
                   END AS final_unit_price
            FROM products p
            INNER JOIN sku_product_new sku ON p.id = sku.product_id
            LEFT JOIN key_specification_values as ksv ON ksv.product_id = p.id
            LEFT JOIN categories as c ON c.id = p.sub_sub_category_id
            LEFT JOIN sellers seller ON p.user_id = seller.id
            LEFT JOIN shops shop ON seller.id = shop.seller_id
        ";

        $whereConditions = $this->buildOptimizedWhereConditions($params);
        
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(' AND ', $whereConditions);
        }

        $query .= " GROUP BY p.id";

        // Get price range
        $priceQuery = "SELECT MIN(final_unit_price) AS minPrice, MAX(final_unit_price) AS maxPrice FROM ($query) AS priced_products";
        $priceResult = $this->mysqli->query($priceQuery);
        if ($priceResult) {
            $priceRow = $priceResult->fetch_assoc();
            $filters['minPrice'] = (float) ($priceRow['minPrice'] ?? 0);
            $filters['maxPrice'] = (float) ($priceRow['maxPrice'] ?? 0);
            $priceResult->close();
        }

        // Get filter options
        $filterQuery = "SELECT DISTINCT category_id, sub_category_id, sub_sub_category_id, brand_id, colors, 
                        choice_options, id, discount, discount_type, unit_price, free_delivery,
                        specification, key_features, technical_specification, specs, keyf, techs 
                        FROM ($query) as filtered_products";

        $filterResult = $this->mysqli->query($filterQuery);
        
        if ($filterResult) {
            while ($filterRow = $filterResult->fetch_assoc()) {
                $this->processFilterRow($filterRow, $filters, $params);
            }
            $filterResult->close();
        }

        return [
            'total_size' => 0,
            'products' => [],
            'filters' => $filters
        ];
    }

    private function processFilterRow($filterRow, &$filters, $params) {
        // Process categories
        $this->processCategoryFilters($filterRow, $filters);
        
        // Process brands
        $this->processBrandFilters($filterRow, $filters);
        
        // Process colors
        $this->processColorFilters($filterRow, $filters);
        
        // Process choice options
        $this->processChoiceOptionFilters($filterRow, $filters);
        
        // Process specifications
        $this->processSpecificationFilters($filterRow, $filters, $params);
        
        // Process free delivery
        if ((int) $filterRow['free_delivery'] === 1) {
            $filters['free_delivery'] = 1;
        }
    }

    private function processCategoryFilters($filterRow, &$filters) {
        // Process main category
        if (!is_null($filterRow['category_id']) && !in_array($filterRow['category_id'], array_column($filters['categories'], 'id'))) {
            $categoryId = $filterRow['category_id'];
            $categoryQuery = "SELECT * FROM categories WHERE id = $categoryId";
            $categoryResult = $this->mysqli->query($categoryQuery);

            if ($categoryResult && $categoryResult->num_rows > 0) {
                $categoryData = $categoryResult->fetch_assoc();
                if ($categoryData) {
                    $categoryData['id'] = (int) $categoryData['id'];
                    $categoryData['parent_id'] = (int) $categoryData['parent_id'];
                    $categoryData['sub_parent_id'] = (int) $categoryData['sub_parent_id'];
                    $categoryData['position'] = (int) $categoryData['position'];
                    $categoryData['home_status'] = (int) $categoryData['home_status'];
                    $categoryData['priority'] = (int) $categoryData['priority'];
                    $categoryData['children'] = [];
                    $filters['categories'][] = $categoryData;
                }
                $categoryResult->close();
            }
        }

        // Process subcategory
        if (!is_null($filterRow['sub_category_id'])) {
            $categoryIndex = array_search($filterRow['category_id'], array_column($filters['categories'], 'id'));
            if ($categoryIndex !== false) {
                $category = &$filters['categories'][$categoryIndex];
                if (!in_array($filterRow['sub_category_id'], array_column($category['children'], 'id'))) {
                    $subCategoryId = $filterRow['sub_category_id'];
                    $subCategoryQuery = "SELECT * FROM categories WHERE id = $subCategoryId";
                    $subCategoryResult = $this->mysqli->query($subCategoryQuery);

                    if ($subCategoryResult && $subCategoryResult->num_rows > 0) {
                        $subCategoryData = $subCategoryResult->fetch_assoc();
                        if ($subCategoryData) {
                            $subCategoryData['id'] = (int) $subCategoryData['id'];
                            $subCategoryData['parent_id'] = (int) $subCategoryData['parent_id'];
                            $subCategoryData['sub_parent_id'] = (int) $subCategoryData['sub_parent_id'];
                            $subCategoryData['position'] = (int) $subCategoryData['position'];
                            $subCategoryData['home_status'] = (int) $subCategoryData['home_status'];
                            $subCategoryData['priority'] = (int) $subCategoryData['priority'];
                            $subCategoryData['children'] = [];
                            $category['children'][] = $subCategoryData;
                        }
                        $subCategoryResult->close();
                    }
                }
            }
        }

        // Process sub-sub-category
        if (!is_null($filterRow['sub_sub_category_id'])) {
            $categoryIndex = array_search($filterRow['category_id'], array_column($filters['categories'], 'id'));
            if ($categoryIndex !== false) {
                $category = &$filters['categories'][$categoryIndex];
                $subCategoryIndex = array_search($filterRow['sub_category_id'], array_column($category['children'], 'id'));
                if ($subCategoryIndex !== false) {
                    $subCategory = &$category['children'][$subCategoryIndex];
                    if (!in_array($filterRow['sub_sub_category_id'], array_column($subCategory['children'], 'id'))) {
                        $subSubCategoryId = $filterRow['sub_sub_category_id'];
                        $subSubCategoryQuery = "SELECT * FROM categories WHERE id = $subSubCategoryId";
                        $subSubCategoryResult = $this->mysqli->query($subSubCategoryQuery);

                        if ($subSubCategoryResult && $subSubCategoryResult->num_rows > 0) {
                            $subSubCategoryData = $subSubCategoryResult->fetch_assoc();
                            if ($subSubCategoryData) {
                                $subSubCategoryData['id'] = (int) $subSubCategoryData['id'];
                                $subSubCategoryData['parent_id'] = (int) $subSubCategoryData['parent_id'];
                                $subSubCategoryData['sub_parent_id'] = (int) $subSubCategoryData['sub_parent_id'];
                                $subSubCategoryData['position'] = (int) $subSubCategoryData['position'];
                                $subSubCategoryData['home_status'] = (int) $subSubCategoryData['home_status'];
                                $subSubCategoryData['priority'] = (int) $subSubCategoryData['priority'];
                                $subCategory['children'][] = $subSubCategoryData;
                            }
                            $subSubCategoryResult->close();
                        }
                    }
                }
            }
        }
    }

    private function processBrandFilters($filterRow, &$filters) {
        if (!is_null($filterRow['brand_id']) && !in_array($filterRow['brand_id'], array_column($filters['brands'], 'id'))) {
            $brandId = $filterRow['brand_id'];
            $brandQuery = "SELECT * FROM brands WHERE id = $brandId";
            $brandResult = $this->mysqli->query($brandQuery);

            if ($brandResult && $brandResult->num_rows > 0) {
                $brandData = $brandResult->fetch_assoc();
                if ($brandData) {
                    $brandData['id'] = (int) $brandData['id'];
                    $brandData['status'] = (int) $brandData['status'];
                    $filters['brands'][] = $brandData;
                }
                $brandResult->close();
            }
        }
    }

    private function processColorFilters($filterRow, &$filters) {
        if (!is_null($filterRow['colors']) && !empty($filterRow['colors'])) {
            $colorArray = json_decode($filterRow['colors'], true);
            if (is_array($colorArray)) {
                foreach ($colorArray as $color) {
                    if (!in_array($color, $filters['colors'])) {
                        $filters['colors'][] = $color;
                    }
                }
            }
        }
    }

    private function processChoiceOptionFilters($filterRow, &$filters) {
        if (!empty($filterRow['choice_options'])) {
            $choiceOptions = json_decode($filterRow['choice_options'], true);
            if (is_array($choiceOptions)) {
                foreach ($choiceOptions as $option) {
                    if ($option['title'] == 'Material') {
                        foreach ($option['options'] as $opt) {
                            if (!in_array($opt, $filters['materials'])) {
                                $filters['materials'][] = $opt;
                            }
                        }
                    } elseif ($option['title'] == 'Size') {
                        foreach ($option['options'] as $opt) {
                            if (!in_array($opt, $filters['sizes'])) {
                                $filters['sizes'][] = $opt;
                            }
                        }
                    } elseif ($option['title'] == 'Type') {
                        foreach ($option['options'] as $opt) {
                            if (!in_array($opt, $filters['types'])) {
                                $filters['types'][] = $opt;
                            }
                        }
                    } elseif ($option['title'] == 'Colour') {
                        foreach ($option['options'] as $opt) {
                            if (!in_array($opt, $filters['otherColors'])) {
                                $filters['otherColors'][] = $opt;
                            }
                        }
                    }
                }
            }
        }
    }

    private function processSpecificationFilters($filterRow, &$filters, $params) {
        if (!empty($params['subSubCategoryIds'])) {
            // SPECIFICATION
            $specValues = json_decode($filterRow['specification'] ?? '[]', true);
            $specKeys = isset($filterRow['specs']) ? explode(',', $filterRow['specs']) : [];
            
            if (count($specKeys) === count($specValues) && is_array($specValues)) {
                $combined = array_combine($specKeys, $specValues);
                foreach ($combined as $key => $value) {
                    $key = trim($key);
                    if (empty($value) || strtolower(trim($value)) === 'n/a') continue;

                    if (!isset($filters['specifications'][$key])) {
                        $filters['specifications'][$key] = [];
                    }

                    if (!in_array($value, $filters['specifications'][$key])) {
                        $filters['specifications'][$key][] = $value;
                    }
                }
            }
            
            // TECHNICAL SPECIFICATION
            $techValues = json_decode($filterRow['technical_specification'] ?? '[]', true);
            $techKeys = isset($filterRow['techs']) ? explode(',', $filterRow['techs']) : [];
            
            if (count($techKeys) === count($techValues) && is_array($techValues)) {
                $combined = array_combine($techKeys, $techValues);
                foreach ($combined as $key => $value) {
                    $key = trim($key);
                    if (empty($value) || strtolower(trim($value)) === 'n/a') continue;

                    if (!isset($filters['technical_specifications'][$key])) {
                        $filters['technical_specifications'][$key] = [];
                    }

                    if (!in_array($value, $filters['technical_specifications'][$key])) {
                        $filters['technical_specifications'][$key][] = $value;
                    }
                }
            }
        }
    }

    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
    }

    private function sendErrorResponse($message) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => $message]);
        exit;
    }
}

// Usage
$productFilter = new OptimizedProductFilter();
$productFilter->handleRequest();
?>
