<?php
$host = 'localhost';
$dbname = 'c2int';
$user = 'c2int';
$password = '4Ua#Aa4TBqhj';

// Create connection
$mysqli = new mysqli($host, $user, $password, $dbname);

if ($mysqli->connect_errno) {
    echo json_encode(["error" => "Failed to connect to MySQL: " . $mysqli->connect_error]);
    exit();
}

$products = [];
$totalProducts = 0;
$filters = [];

// Read limit, offset and showFiltersOptions
$limit = isset($_POST['limit']) ? (int) $_POST['limit'] : 10;
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
// Ensure valid page number
if ($page < 1) {
    $page = 1;
}
// Calculate offset
$offset = ($page - 1) * $limit;
$showFiltersOptions = isset($_POST['showFiltersOptions']) ? (bool) $_POST['showFiltersOptions'] : 0;

// categoryIds, brandIds, search, and new filter parameters from POST data
$categoryIds = isset($_POST['categoryIds']) ? $_POST['categoryIds'] : '';
$subCategoryIds = isset($_POST['subCategoryIds']) ? $_POST['subCategoryIds'] : '';
$subSubCategoryIds = isset($_POST['subSubCategoryIds']) ? $_POST['subSubCategoryIds'] : '';
$brandIds = isset($_POST['brandIds']) ? $_POST['brandIds'] : '';
$sellerIds = isset($_POST['sellerIds']) ? $_POST['sellerIds'] : '';
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';
$colorIds = isset($_POST['colors']) ? $_POST['colors'] : '';
$specifications = isset($_POST['specifications']) ? $_POST['specifications'] : '';
$technicalSpecifications = isset($_POST['technicalSpecifications']) ? $_POST['technicalSpecifications'] : '';

// Read isFreeShipping and discountPercentageFilter POST data
$isFeatured = isset($_POST['isFeatured']) ? (bool) $_POST['isFeatured'] : false;
$isFreeShipping = isset($_POST['isFreeShipping']) ? (bool) $_POST['isFreeShipping'] : false;
$isDealOfTheDay = isset($_POST['isDealOfTheDay']) ? (bool) $_POST['isDealOfTheDay'] : false;
$discountPercentageFilter = !empty($_POST['discount']) ? intval($_POST['discount']) : null;

// New filter parameters for Material, Colour, Size, and Type
$material = isset($_POST['material']) ? $_POST['material'] : '';
$otherColors = isset($_POST['otherColors']) ? $_POST['otherColors'] : '';
$size = isset($_POST['size']) ? $_POST['size'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';  // New filter for Type

// Read price filters and sorting
$minimumPrice = isset($_POST['minimumPrice']) ? (float) $_POST['minimumPrice'] : null;
$maximumPrice = isset($_POST['maximumPrice']) ? (float) $_POST['maximumPrice'] : null;
$rating = isset($_POST['rating']) ? (int) $_POST['rating'] : null;
$sortOrder = isset($_POST['sortOrder']) ? $_POST['sortOrder'] : '';

// Initialize query for products joining with order_details, reviews and deals_of_the_day table
$query = "SELECT p.*, ksv.specification, ksv.key_features, ksv.technical_specification,
           c.specification as specs, c.technical_specification as techs, c.key_features as keyf,
           COALESCE(od.total_orders, 0) AS total_orders,
           COALESCE(r.avg_rating, 0) AS avg_rating,
           deal.status AS deal_status,
           deal.start_date_time AS start_date_time,
           deal.expire_date_time AS expire_date_time,
            CASE
            WHEN sku.discount IS NULL THEN CAST(sku.variant_mrp AS DECIMAL(10, 2))
            WHEN sku.discount_type = 'percent' THEN CAST(ROUND(sku.variant_mrp * (1 - (CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10,
            2)) / 100)), 2) AS DECIMAL(10, 2))
            WHEN sku.discount_type = 'flat' THEN CAST(ROUND(sku.variant_mrp - CAST(sku.discount AS DECIMAL(10, 2)), 2) AS DECIMAL(10, 2))
            ELSE CAST(sku.variant_mrp AS DECIMAL(10, 2))
            END AS final_unit_price,
           tag_data.tags  -- Aggregated tags as a separate subquery
            FROM products AS p
            INNER JOIN sku_product_new AS sku ON p.id = sku.product_id
            LEFT JOIN key_specification_values as ksv ON ksv.product_id = p.id
            LEFT JOIN categories as c ON c.id = p.sub_sub_category_id
            LEFT JOIN (
            SELECT product_id, COUNT(*) AS total_orders
            FROM order_details
            GROUP BY product_id) AS od ON p.id = od.product_id
            LEFT JOIN (
            SELECT product_id, ROUND(AVG(rating), 2) AS avg_rating
            FROM reviews
            WHERE status = 1 
            GROUP BY product_id) AS r ON p.id = r.product_id
            LEFT JOIN deal_of_the_days AS deal ON p.id = deal.product_id
            LEFT JOIN sellers AS seller ON p.user_id = seller.id
            LEFT JOIN shops AS shop ON seller.id = shop.seller_id
            LEFT JOIN (
            SELECT pt.product_id, GROUP_CONCAT(t.tag) AS tags
            FROM product_tag AS pt
            JOIN tags AS t ON pt.tag_id = t.id
            GROUP BY pt.product_id) AS tag_data ON p.id = tag_data.product_id
        ";

$whereCondition = [];
$orderConditions = [];
$havingConditions = [];

$whereConditions[] = 'sku.variant_mrp is not null AND request_status = 1 AND p.status = 1';
$whereConditions[] = 'seller.status = "approved"';
$whereConditions[] = 'shop.vacation_status = 0 AND shop.temporary_close = 0';


// Filter by categoryIds if provided
if (!empty($categoryIds) && (empty($subCategoryIds) || empty($subSubCategoryIds))) {
    $categoryIdsArray = array_map('intval', explode(',', $categoryIds));
    $categoryIdsList = implode(',', $categoryIdsArray);
    $whereConditions[] = "category_id IN ($categoryIdsList)";
}

// Filter by subCategoryIds if provided
if (!empty($subCategoryIds) && empty($subSubCategoryIds)) {
    $subCategoryIdArray = array_map('intval', explode(',', $subCategoryIds));
    $subCategoryIdList = implode(',', $subCategoryIdArray);
    $whereConditions[] = "sub_category_id IN ($subCategoryIdList)";
}

// Filter by subSubCategoryIds if provided
if (!empty($subSubCategoryIds)) {
    $subSubCategoryIdArray = array_map('intval', explode(',', $subSubCategoryIds));
    $subSubCategoryIdList = implode(',', $subSubCategoryIdArray);
    $whereConditions[] = "sub_sub_category_id IN ($subSubCategoryIdList)";
}

// Filter by brandIds if provided
if (!empty($brandIds)) {
    $brandIdsArray = array_map('intval', explode(',', $brandIds));
    $brandIdsList = implode(',', $brandIdsArray);
    $whereConditions[] = "brand_id IN ($brandIdsList)";
}

// Filter by sellerIds if provided
if (!empty($sellerIds)) {
    $sellerIdsArray = array_map('intval', explode(',', $sellerIds));
    $sellerIdsList = implode(',', $sellerIdsArray);
    $whereConditions[] = "user_id IN ($sellerIdsList)";
}

// Add search condition if provided
if (!empty($searchTerm)) {
    $searchTermEscaped = $mysqli->real_escape_string($searchTerm);
    $whereConditions[] = "(p.name LIKE '%$searchTermEscaped%' OR tags LIKE '%$searchTermEscaped%')";
}

// Add color filtering condition if provided
if (!empty($colorIds)) {
    $colorArray = array_map('trim', explode(',', $colorIds)); // Split and trim colors
    $colorConditions = []; // Array to hold individual color conditions

    foreach ($colorArray as $color) {
        $colorEscaped = $mysqli->real_escape_string($color);
        $colorConditions[] = "JSON_CONTAINS(colors, '\"$colorEscaped\"')"; // Use JSON_CONTAINS for each color
    }

    // Combine color conditions with OR
    if (!empty($colorConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $colorConditions) . ')';
    }
}

// Filter by specifications if provided
if (!empty($specifications)) {
    $specArray = array_map('trim', explode(',', $specifications));
    $specConditions = [];

    foreach ($specArray as $spec) {
        $specEscaped = $mysqli->real_escape_string($spec);
        $specConditions[] = "JSON_CONTAINS(ksv.specification, '\"$specEscaped\"')";
    }

    // Combine specification conditions using OR
    if (!empty($specConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $specConditions) . ')';
    }
}
// Filter by technical specifications if provided
if (!empty($technicalSpecifications)) {
    $techSpecArray = array_map('trim', explode(',', $technicalSpecifications));
    $techSpecConditions = [];

    foreach ($techSpecArray as $techSpec) {
        $techSpecEscaped = $mysqli->real_escape_string($techSpec);
        $techSpecConditions[] = "JSON_CONTAINS(ksv.technical_specification, '\"$techSpecEscaped\"')";
    }

    // Combine technical specification conditions using OR
    if (!empty($techSpecConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $techSpecConditions) . ')';
    }
}

// Filter by free shipping
if ($isFreeShipping) {
    $whereConditions[] = "free_delivery = 1";
}

// filter by featred
if ($isFeatured) {
    $whereConditions[] = "featured = 1";
}

// Filter by Material from choice_options
if (!empty($material)) {
    $materialArray = array_map('trim', explode(',', $material));
    $materialConditions = [];

    foreach ($materialArray as $mat) {
        $matEscaped = $mysqli->real_escape_string($mat);
        $materialConditions[] = "JSON_CONTAINS(choice_options, '{\"title\": \"Material\", \"options\": [\"$matEscaped\"]}')";
    }

    // Combine material conditions using OR
    if (!empty($materialConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $materialConditions) . ')';
    }
}

// Filter by Other Colour from choice_options
if (!empty($otherColors)) {
    $colourArray = array_map('trim', explode(',', $otherColors));
    $colourConditions = [];

    foreach ($colourArray as $col) {
        $colEscaped = $mysqli->real_escape_string($col);
        $colourConditions[] = "JSON_CONTAINS(choice_options, '{\"title\": \"Colour\", \"options\": [\"$colEscaped\"]}')";
    }

    // Combine colour conditions using OR
    if (!empty($colourConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $colourConditions) . ')';
    }
}

// Filter by Size from choice_options
if (!empty($size)) {
    $sizeArray = array_map('trim', explode(',', $size));
    $sizeConditions = [];

    foreach ($sizeArray as $sz) {
        $szEscaped = $mysqli->real_escape_string($sz);
        $sizeConditions[] = "JSON_CONTAINS(choice_options, '{\"title\": \"Size\", \"options\": [\"$szEscaped\"]}')";
    }

    // Combine size conditions using OR
    if (!empty($sizeConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $sizeConditions) . ')';
    }
}

// Filter by Type from choice_options
if (!empty($type)) {
    $typeArray = array_map('trim', explode(',', $type));
    $typeConditions = [];

    foreach ($typeArray as $ty) {
        $tyEscaped = $mysqli->real_escape_string($ty);
        $typeConditions[] = "JSON_CONTAINS(choice_options, '{\"title\": \"Type\", \"options\": [\"$tyEscaped\"]}')";
    }

    // Combine type conditions using OR
    if (!empty($typeConditions)) {
        $whereConditions[] = '(' . implode(' OR ', $typeConditions) . ')';
    }
}

// Add price filtering conditions if provided
if (!empty($minimumPrice)) {
    $whereConditions[] = "
        CASE
            WHEN sku.discount IS NULL THEN CAST(sku.variant_mrp AS DECIMAL(10, 2))
            WHEN sku.discount_type = 'percent' THEN CAST(ROUND(sku.variant_mrp * (1 - (CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10, 2)) / 100)), 2) AS DECIMAL(10, 2))
            WHEN sku.discount_type = 'flat' THEN CAST(ROUND(sku.variant_mrp - CAST(sku.discount AS DECIMAL(10, 2)), 2) AS DECIMAL(10, 2))
            ELSE CAST(sku.variant_mrp AS DECIMAL(10, 2)) 
        END >= $minimumPrice";
}

if (!empty($maximumPrice)) {
    $whereConditions[] = "
        CASE 
            WHEN sku.discount IS NULL THEN CAST(sku.variant_mrp AS DECIMAL(10, 2))
            WHEN sku.discount_type = 'percent' THEN CAST(ROUND(sku.variant_mrp * (1 - (CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10, 2)) / 100)), 2) AS DECIMAL(10, 2))
            WHEN sku.discount_type = 'flat' THEN CAST(ROUND(sku.variant_mrp - CAST(sku.discount AS DECIMAL(10, 2)), 2) AS DECIMAL(10, 2))
            ELSE CAST(sku.variant_mrp AS DECIMAL(10, 2)) 
        END <= $maximumPrice";
}

if (!is_null($rating) && $rating > 0) {
    $havingConditions[] = 'avg_rating >= ' . $rating . ' AND avg_rating <= 5';
}

// Add the deal-related condition if isDealOfTheDay is true
if ($isDealOfTheDay) {
    $whereConditions[] = "deal.status = 1 
                          AND deal.start_date_time <= NOW() 
                          AND deal.expire_date_time >= NOW()"; // Ensure current running deals
}


// Filter by discount percentage (from POST data)
if (!is_null($discountPercentageFilter)) {
    // Assuming $discountPercentageFilter is the minimum discount percentage you want to filter by
    $havingConditions[] = "(
        (p.discount_type = 'percent' AND p.discount >= $discountPercentageFilter) 
        OR 
        (p.discount_type = 'flat' AND (p.discount / p.unit_price) * 100 >= $discountPercentageFilter)
    )";
}


// Handle sorting based on the selected sort order
if ($sortOrder === 'priceHigh') {
    $orderConditions[] = "final_unit_price DESC";
} elseif ($sortOrder === 'priceLow') {
    $orderConditions[] = "final_unit_price ASC";
} elseif ($sortOrder === 'discount') {
    // Ensure only products with discounts are included
    $whereConditions[] = "sku.discount > 0";

    // Sort by the discount percentage, regardless of discount type
    $orderConditions[] = "CASE 
        WHEN sku.discount_type = 'percent' THEN CAST(REPLACE(sku.discount, '%', '') AS DECIMAL(10, 2)) 
        WHEN sku.discount_type = 'flat' THEN (CAST(sku.discount AS DECIMAL(10, 2)) / sku.variant_mrp) * 100
        ELSE 0 
    END DESC";  // Sort by the calculated discount percentage in descending order
} elseif ($sortOrder === 'newArrivals') {
    $orderConditions[] = "p.created_at DESC";
} elseif ($sortOrder === 'bestSelling') {
    $orderConditions[] = "total_orders DESC";
} elseif ($sortOrder === 'topProducts') {
    $orderConditions[] = "avg_rating DESC";
}


// Append WHERE clause if any conditions exist
if (!empty($whereConditions)) {
    $query .= " WHERE " . implode(' AND ', $whereConditions);
}

// Group products for perfect joining of tabels
$query .= " GROUP BY p.id";

// Now, add the HAVING clause to filter by the aliased column
if (!empty($havingConditions)) {
    $query .= " HAVING " . implode(' AND ', $havingConditions);
}

// If there are any order conditions, append them
if (!empty($orderConditions)) {
    $query .= " ORDER BY " . implode(', ', $orderConditions);
}

// Add LIMIT and OFFSET
$queryLimit = "$query LIMIT $limit OFFSET $offset;";



$fieldTypeMap = [
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
    'final_unit_price' => 'float'
];

// if showFiltersOptions is false then show products
if (!$showFiltersOptions) {
    if ($result = $mysqli->query($queryLimit)) {
        while ($row = $result->fetch_assoc()) {

            // Decode the jsons if it's a JSON string
            if (isset($row['images']) && is_string($row['images'])) {
                $row['images'] = json_decode($row['images'], true); // Decode JSON string to array
            }
            if (isset($row['category_ids']) && is_string($row['category_ids'])) {
                $row['category_ids'] = json_decode($row['category_ids'], true); // Decode JSON string to array
            }
            if (isset($row['color_image']) && is_string($row['color_image'])) {
                $row['color_image'] = json_decode($row['color_image'], true); // Decode JSON string to array
            }
            if (isset($row['choice_options']) && is_string($row['choice_options'])) {
                $row['choice_options'] = json_decode($row['choice_options'], true); // Decode JSON string to array
            }
            if (isset($row['variation']) && is_string($row['variation'])) {
                $row['variation'] = json_decode($row['variation'], true); // Decode JSON string to array
            }
            if (isset($row['colors']) && is_string($row['colors'])) {
                $row['colors'] = json_decode($row['colors'], true); // Decode JSON string to array
            }
            if (isset($row['attributes']) && is_string($row['attributes'])) {
                $row['attributes'] = json_decode($row['attributes'], true); // Decode JSON string to array
            }

            // Dynamically cast fields based on the fieldTypeMap
            foreach ($fieldTypeMap as $field => $type) {
                if (isset($row[$field])) {
                    // Cast based on the type defined in the map
                    switch ($type) {
                        case 'int':
                            $row[$field] = (int) $row[$field];
                            break;
                        case 'float':
                            $row[$field] = (float) $row[$field];
                            break;
                        // Add more cases if needed, like 'bool', 'string', etc.
                    }
                }
            }
            $products[] = $row; // Add the processed row to products
        }
        $result->close();
    }

//////////s
$related_product_ids = array_column($products, 'id');

if (!empty($related_product_ids)) {
    $related_product_ids = array_map('intval', $related_product_ids);

    $sku_query = "SELECT product_id, thumbnail_image, image, listed_price, variant_mrp, discount, discount_type 
                  FROM sku_product_new 
                  WHERE  product_id IN (" . implode(',', $related_product_ids) . ") 
                   AND variant_mrp IS NOT NULL  order by id desc";

    $sku_result = $mysqli->query($sku_query);
    $sku_product_map = [];

    while ($sku = $sku_result->fetch_assoc()) {
        $sku_product_map[$sku['product_id']] = $sku;
    }
    // Merge SKU Data with Products
    foreach ($products as &$product) {
        if (isset($sku_product_map[$product['id']])) {
            
            $sku = $sku_product_map[$product['id']];
            $product['images'] = (!empty($sku['image']) && is_string($sku['image']))
                ? json_decode($sku['image'], true)
                : [];
            $product['thumbnail'] = $sku['thumbnail_image'] != null ? $sku['thumbnail_image'] : $product['images'][0];

            

            $product['unit_price'] = $sku['variant_mrp'];

            // Safely set discount fields if they exist
            $product['discount'] = isset($sku['discount']) ? intval($sku['discount']) : 0;

            $product['discount_type'] = isset($sku['discount_type']) ? $sku['discount_type'] : null;
        }
    }
}
}

// Get total number of products for pagination meta info (without limit and offset)
$totalQuery = "SELECT COUNT(*) as total FROM ($query) as aggregated_products";

$totalResult = $mysqli->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();

$totalProducts = $totalRow['total'];
$totalResult->close();
// if showFiltersOptions is true then show available filters
if ($showFiltersOptions) {

    $filters = [
        'categories' => [], // To hold full nested category data
        'brands' => [], // To hold brand data
        'colors' => [], // To hold color data
        'materials' => [], // To hold materials from choice_options
        'sizes' => [], // To hold sizes from choice_options
        'types' => [], // To hold types from choice_options
        'otherColors' => [], // To hold colors from choice_options
        'free_delivery' => 0,
        'minPrice' => 0, // To hold minimum available price
        'maxPrice' => 0,// To hold maximum available price,
        'specifications' => [], // To hold specifications
        'technical_specifications' => [] // To hold technical specifications
    ];


    // Now fetch the minimum and maximum prices independent of pagination
    $priceQuery = "SELECT MIN(final_unit_price) AS minPrice, MAX(final_unit_price) AS maxPrice FROM ($query) AS priced_products";
    $priceResult = $mysqli->query($priceQuery);
    $priceRow = $priceResult->fetch_assoc();
    $filters['minPrice'] = (float) $priceRow['minPrice'];
    $filters['maxPrice'] = (float) $priceRow['maxPrice'];
    $priceResult->close();

    // Now fetch distinct filter values independent of pagination
    $filterQuery = "SELECT DISTINCT category_id, sub_category_id, sub_sub_category_id, brand_id, colors, 
                    choice_options, id, discount, discount_type ,unit_price, free_delivery,
                    specification, key_features, technical_specification, specs, keyf, techs 
                    FROM ($query) as filtered_products";
    // echo $filterQuery;die;
    // Execute the filter query
    $filterResult = $mysqli->query($filterQuery);
    
    while ($filterRow = $filterResult->fetch_assoc()) {

        // Process category
        if (!is_null($filterRow['category_id']) && !in_array($filterRow['category_id'], array_column($filters['categories'], 'id'))) {
            $categoryId = $filterRow['category_id'];
            $categoryQuery = "SELECT * FROM categories WHERE id = $categoryId"; // Adjust your table name and column
            $categoryResult = $mysqli->query($categoryQuery); // Assuming $mysqli is your database connection

            if ($categoryResult && $categoryResult->num_rows > 0) {
                $categoryData = $categoryResult->fetch_assoc();
                if ($categoryData) {
                    $categoryData['id'] = (int) $categoryData['id'];
                    $categoryData['parent_id'] = (int) $categoryData['parent_id'];
                    $categoryData['sub_parent_id'] = (int) $categoryData['sub_parent_id'];
                    $categoryData['position'] = (int) $categoryData['position'];
                    $categoryData['home_status'] = (int) $categoryData['home_status'];
                    $categoryData['priority'] = (int) $categoryData['priority'];
                    $categoryData['children'] = []; // Add an empty array for subcategories
                    $filters['categories'][] = $categoryData; // Add full category data to the array
                }
            }
        }

        // Process subcategory
        if (!is_null($filterRow['sub_category_id'])) {
            $categoryIndex = array_search($filterRow['category_id'], array_column($filters['categories'], 'id'));
            if ($categoryIndex !== false) {
                $category = &$filters['categories'][$categoryIndex]; // Get reference to the category
                if (!in_array($filterRow['sub_category_id'], array_column($category['children'], 'id'))) {
                    $subCategoryId = $filterRow['sub_category_id'];
                    $subCategoryQuery = "SELECT * FROM categories WHERE id = $subCategoryId"; // Adjust your table name and column
                    $subCategoryResult = $mysqli->query($subCategoryQuery); // Assuming $mysqli is your database connection

                    if ($subCategoryResult && $subCategoryResult->num_rows > 0) {
                        $subCategoryData = $subCategoryResult->fetch_assoc();
                        if ($subCategoryData) {

                            $subCategoryData['id'] = (int) $subCategoryData['id'];
                            $subCategoryData['parent_id'] = (int) $subCategoryData['parent_id'];
                            $subCategoryData['sub_parent_id'] = (int) $subCategoryData['sub_parent_id'];
                            $subCategoryData['position'] = (int) $subCategoryData['position'];
                            $subCategoryData['home_status'] = (int) $subCategoryData['home_status'];
                            $subCategoryData['priority'] = (int) $subCategoryData['priority'];
                            $subCategoryData['children'] = []; // Add an empty array for sub-sub-categories
                            $category['children'][] = $subCategoryData; // Add sub-category to the category's children
                        }
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
                    $subCategory = &$category['children'][$subCategoryIndex]; // Get reference to the subcategory
                    if (!in_array($filterRow['sub_sub_category_id'], array_column($subCategory['children'], 'id'))) {
                        $subSubCategoryId = $filterRow['sub_sub_category_id'];
                        $subSubCategoryQuery = "SELECT * FROM categories WHERE id = $subSubCategoryId"; // Adjust your table name and column
                        $subSubCategoryResult = $mysqli->query($subSubCategoryQuery); // Assuming $mysqli is your database connection

                        if ($subSubCategoryResult && $subSubCategoryResult->num_rows > 0) {
                            $subSubCategoryData = $subSubCategoryResult->fetch_assoc();
                            if ($subSubCategoryData) {
                                $subSubCategoryData['id'] = (int) $subSubCategoryData['id'];
                                $subSubCategoryData['parent_id'] = (int) $subSubCategoryData['parent_id'];
                                $subSubCategoryData['sub_parent_id'] = (int) $subSubCategoryData['sub_parent_id'];
                                $subSubCategoryData['position'] = (int) $subSubCategoryData['position'];
                                $subSubCategoryData['home_status'] = (int) $subSubCategoryData['home_status'];
                                $subSubCategoryData['priority'] = (int) $subSubCategoryData['priority'];
                                $subCategory['children'][] = $subSubCategoryData; // Add sub-sub-category to subcategory's children
                            }
                        }
                    }
                }
            }
        }

        // Avoid duplicates for brands
        if (!is_null($filterRow['brand_id']) && !in_array($filterRow['brand_id'], array_column($filters['brands'], 'id'))) {

            // Fetch full brand data
            $brandId = $filterRow['brand_id'];
            $brandQuery = "SELECT * FROM brands WHERE id = $brandId"; // Adjust your table name and column
            $brandResult = $mysqli->query($brandQuery); // Assuming $mysqli is your database connection

            if ($brandResult && $brandResult->num_rows > 0) {
                $brandData = $brandResult->fetch_assoc();
                if ($brandData) {
                    $brandData['id'] = (int) $brandData['id'];
                    $brandData['status'] = (int) $brandData['status'];
                    $filters['brands'][] = $brandData; // Add full brand data to the array
                }
            }
        }
        // Handle colors from JSON array
        if (!is_null($filterRow['colors']) && !empty($filterRow['colors'])) {
            $colorArray = json_decode($filterRow['colors'], true);
            foreach ($colorArray as $color) {
                if (!in_array($color, $filters['colors'])) {
                    $filters['colors'][] = $color;
                }
            }
        }

         // Handle choice_options for materials, sizes, and types
         if (!empty($filterRow['choice_options'])) {
            $choiceOptions = json_decode($filterRow['choice_options'], true);
            foreach ($choiceOptions as $option) {
               if ($option['title'] == 'Size') {
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

        if ((int) $filterRow['free_delivery'] === 1) {
            $filters['free_delivery'] = 1;
        }
       
       
        if(!empty($subSubCategoryIds)){
        // SPECIFICATION
        $specValues = json_decode($filterRow['specification'] ?? '[]', true);
        $specKeys = isset($filterRow['specs']) ? explode(',', $filterRow['specs']) : [];
        
        if (count($specKeys) === count($specValues)) {
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
        
        if (count($techKeys) === count($techValues)) {
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

    $filterResult->close();
    $mysqli->close();
}
// Prepare the final response
$response = [
    'total_size' => (int) $totalProducts,
    'products' => $products,
    'filters' => $filters,
];

header('Content-Type: application/json');
echo json_encode($response);
