<?php

namespace App\FirebaseServices;

use Google\Client;
use Illuminate\Support\Facades\Http;
use App\User;
use DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class FirebaseNotificationService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/firebase-service-account.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    /**
     * Send FCM notification to all users.
     *
     * @param array $notificationData
     * @return array
     */
    public function sendNotificationToAllUsers(array $notificationData)
    {
        // Retrieve all FCM tokens
        $tokens = User::where('role', NULL)->whereNotNull('cm_firebase_token')->pluck('cm_firebase_token')->toArray();

       
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];

       
        $responses = [];

        foreach ($tokens as $fcmToken) {
           
            $payload = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $notificationData['title'],
                        "body" => $notificationData['body'],
                        "image" => $notificationData['data']['image'] ?? null,  
                    ],
                    "data" => $notificationData['data'] ?? [],
                ]
            ];

           
            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

           
            $responses[] = [
                'token' => $fcmToken,
                'response' => $response->json(),
            ];
        }

        return $responses;
    }
    
    public function sendNotificationToAllProviders(array $notificationData)
    {
        // Retrieve all FCM tokens
        $tokens = User::whereIn('role', [2,3,4,5])->whereNotNull('cm_firebase_token')->pluck('cm_firebase_token')->toArray();

       
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];

       
        $responses = [];

        foreach ($tokens as $fcmToken) {
           
            $payload = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $notificationData['title'],
                        "body" => $notificationData['body'],
                        "image" => $notificationData['data']['image'] ?? null,  
                    ],
                    "data" => $notificationData['data'] ?? [],
                ]
            ];

           
            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

           
            $responses[] = [
                'token' => $fcmToken,
                'response' => $response->json(),
            ];
        }

        return $responses;
    }

    public function sendNotificationToHomepageVisitors($users)
    {
        $meta = \App\Model\SeoMeta::where('page', '/')->first();
        $og = json_decode($meta->og_tags, true);

        $notificationData = [
            'title' => ' 👀 Looking for something special?',
            'body'  =>'Check out our latest arrivals!',
            'data'  => [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'type' => 'homepage_visit',
                'screen' => 'HomeScreen'
            ],
        ];

        // ✅ Use passed $users collection
        $tokens = $users->whereNotNull('cm_firebase_token')
                        ->pluck('cm_firebase_token')
                        ->toArray();
        

        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];
        $responses = [];

        foreach ($tokens as $fcmToken) {
            $payload = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $notificationData['title'],
                        "body"  => $notificationData['body'],
                        "image" => $og['image']
                    ],
                    "data" => $notificationData['data'],
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

            $responseData = $response->json();

            // Optional: remove invalid tokens
            if (isset($responseData['error']['status']) && $responseData['error']['status'] === 'NOT_FOUND') {
                User::where('cm_firebase_token', $fcmToken)->update(['cm_firebase_token' => null]);
            }

            $responses[] = [
                'token' => $fcmToken,
                'response' => $responseData,
            ];
        }

        return [
            'HomePageVisitorsStatus' => 'success',
            'HomePageVisitorsCount' => count($responses),
            'HomePageVisitorsresponses' => $responses
        ];
    }

    public function sendNotificationToCategoryVisitors($results)
    {
        if (empty($results) || $results->isEmpty()) {
            return ['status' => 'error', 'message' => 'No category visitors found.'];
        }

        $responses = [];

        foreach ($results as $categoryData) {
            $imageUrl = asset('storage/app/public/category/' . $categoryData->icon);

            $productName = $categoryData->category_name;
            if (mb_strlen($productName) > 20) {
                $productName = mb_substr($productName, 0, 20) . '...';
            }

            $fcmToken = $categoryData->cm_firebase_token ?? null;

            if (empty($fcmToken)) {
                \Log::info("Skipped user with empty Firebase token for category visit", (array) $categoryData);
                $responses[] = [
                    'token' => null,
                    'status' => 'error',
                    'message' => 'No Firebase token found for category visitor.'
                ];
                continue;
            }

            $dataPayload = [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'type'         => 'category_visit',
                'screen'       => 'CategoryScreen',
                'category'     => (string) ($categoryData->category_id ?? ''),
            ];

            $notificationData = [
                'title' => 'Still exploring ' . $productName . ' items?',
                'body'  => 'Here are some top picks.',
                'image' => $imageUrl,
                'data'  => $dataPayload
            ];

            $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];

            $payload = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $notificationData['title'],
                        "body"  => $notificationData['body'],
                        "image" => $notificationData['image'],
                    ],
                    "data" => $notificationData['data'],

                    "android" => [
                        "notification" => [
                            "image" => $notificationData['image']
                        ]
                    ],

                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "mutable-content" => 1
                            ]
                        ],
                        "fcm_options" => [
                            "image" => $notificationData['image']
                        ]
                    ],

                    "webpush" => [
                        "notification" => [
                            "image" => $notificationData['image']
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

            \Log::info("Notification sent", [
                'token' => $fcmToken,
                'response' => $response->json()
            ]);

            $responses[] = [
                'token' => $fcmToken,
                'response' => $response->json(),
            ];
        }

        return $responses;
    }

    public function sendNotificationToCartUsers($userCart)
    {
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];

        $token    = $userCart['token'];
        $products = $userCart['products']; 

        $productNames = array_map(function ($p) {
            return mb_strlen($p['product_name']) > 20 
                ? mb_substr($p['product_name'], 0, 20) . '...' 
                : $p['product_name'];
        }, $products);

        $firstImage = $products[0]['image'] ?? $products[0]['thumbnail'];

        $imageUrl   = asset('/storage/app/public/images') . '/' . $firstImage;
    
        $title = "Items waiting in your cart 🛒";
        $body  = "You left " . implode(", ", $productNames) . " in your cart. Checkout now!";

        $payload = [
            "message" => [
                "token" => $token,

                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                    "image" => $imageUrl,
                ],

                "data" => [
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    "type"         => "cart_reminder",
                    "route"        => "/cart",
                    "screen"       => "CartScreen"
                ],

                "apns" => [
                    "payload" => [
                        "aps" => [
                            "mutable-content" => 1
                        ],
                    ],
                    "fcm_options" => [
                        "image" => $imageUrl
                    ]
                ],

                "android" => [
                    "notification" => [
                        "image" => $imageUrl,
                    ]
                ],
            ]
        ];

        // 5. Send to FCM
        $response = Http::withToken($accessToken)
            ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

        return [
            'token'    => $token,
            'response' => $response->json(),
        ];
    }


    public function sendNotificationToWishlistofCustomer($product)
    {
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];
        $responses = [];

        foreach ($product as $item) {
            $productName = $item->product_name;
            if (mb_strlen($productName) > 20) {
                $productName = mb_substr($productName, 0, 20) . '...';
            }

             $imageUrl = asset('/storage/app/public/images') . '/' . $item->image;
            // $imageUrl = 'https://interiorchowk.com/storage/app/public/banner/2025-07-16-68777c4cd5fab.webp';

            $payload = [
                "message" => [
                    "token" => $item->cm_firebase_token,

                    "notification" => [
                        "title" => "Saved to your wishlist 💖",
                        "body"  => "Don’t wait — {$productName}  is low in stock!",
                        "image" => $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail,
                    ],

                    "data" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "type"         => "cart_reminder",
                        'route'        => '/wishlists',
                        "screen"       => "WishlistScreen"
                    ],

                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "mutable-content" => 1
                            ],
                        ],
                        "fcm_options" => [
                            "image" =>  $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail
                        ]
                    ],

                    "android" => [
                        "notification" => [
                            "image" =>$imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail, 
                        ]
                    ],
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

            $responses[] = [
                'token'    => $item->cm_firebase_token,
                'response' => $response->json(),
            ];
        }

        return [
            'WishlistNotificationStatus'    => 'success',
            'WishlisttNotificationCount'     => count($responses),
            'WishlistNotificationResponses' => $responses
        ];
    }

    public function sendNotificationToCheckoutUsers($product)
    {
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];
        $responses = [];

        foreach ($product as $item) {
            $productName = $item->product_name;
            if (mb_strlen($productName) > 20) {
                $productName = mb_substr($productName, 0, 20) . '...';
            }

             $imageUrl = asset('/storage/app/public/images') . '/' . $item->image;
            // $imageUrl = 'https://interiorchowk.com/storage/app/public/banner/2025-07-16-68777c4cd5fab.webp';

            $payload = [
                "message" => [
                    "token" => $item->cm_firebase_token,

                    "notification" => [
                        "title" => "Left something behind?",
                        "body"  => "Complete your purchase now and enjoy fast delivery.",
                        "image" => $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail,
                    ],

                    "data" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "type"         => "CheckoutReminder",
                        'route'        => '/checkout',
                        "screen"       => "CheckoutScreen"
                    ],

                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "mutable-content" => 1
                            ],
                        ],
                        "fcm_options" => [
                            "image" =>  $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail
                        ]
                    ],

                    "android" => [
                        "notification" => [
                            "image" =>$imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail, 
                        ]
                    ],
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

            $responses[] = [
                'token'    => $item->cm_firebase_token,
                'response' => $response->json(),
            ];
        }

        return [
            'CheckoutNotificationStatus'    => 'success',
            'CheckoutNotificationCount'     => count($responses),
            'CheckoutNotificationResponses' => $responses
        ];
    }

    public function sendNotificationToProductViewUser($product)
    {
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];
        $responses = [];

        foreach ($product as $item) {
            $productName = $item->product_name;
            if (mb_strlen($productName) > 20) {
                $productName = mb_substr($productName, 0, 20) . '...';
            }

             $imageUrl = asset('/storage/app/public/images') . '/' . $item->image;
            // $imageUrl = 'https://interiorchowk.com/storage/app/public/banner/2025-07-16-68777c4cd5fab.webp';

            $payload = [
                "message" => [
                    "token" => $item->cm_firebase_token,

                    "notification" => [
                        "title" => "Liked this product? ❤ ",
                        "body"  => "{$productName} ? It’s selling fast - grab yours now!",
                        "image" => $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail,
                    ],

                    "data" => [
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                            "type"         => "ProductViewReminder",
                            "screen"       => "ProductScreen",
                            "route"        => '/product/' . $item->product_slug,
                        ],


                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "mutable-content" => 1
                            ],
                        ],
                        "fcm_options" => [
                            "image" =>  $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail
                        ]
                    ],

                    "android" => [
                        "notification" => [
                            "image" =>$imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail, 
                        ]
                    ],
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

            $responses[] = [
                'token'    => $item->cm_firebase_token,
                'response' => $response->json(),
            ];
        }

        return [
            'ProductNotificationStatus'    => 'success',
            'ProductNotificationCount'     => count($responses),
            'ProductNotificationResponses' => $responses
        ];
    }

    public function sendNotificationToTimerProductViewUser($product)
    {
        $accessToken = $this->client->fetchAccessTokenWithAssertion()['access_token'];
        $responses = [];

        foreach ($product as $item) {
            $productName = $item->product_name;
            if (mb_strlen($productName) > 20) {
                $productName = mb_substr($productName, 0, 20) . '...';
            }

             $imageUrl = asset('/storage/app/public/images') . '/' . $item->image;
            // $imageUrl = 'https://interiorchowk.com/storage/app/public/banner/2025-07-16-68777c4cd5fab.webp';

            $payload = [
                "message" => [
                    "token" => $item->cm_firebase_token,

                    "notification" => [
                        "title" => "Liked this product? ❤ ",
                        "body"  => "{$productName} ? It’s selling fast - grab yours now!",
                        "image" => $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail,
                    ],

                   "data" => [
                            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                            "type"         => "ProductViewReminder",
                            "screen"       => "ProductScreen",
                            "route"        => '/product/' . $item->product_slug,
                            "button_text"  => "SHOP",
                            "end_time"     => now()->addHours(4)->format('Y-m-d H:i:s'), // Countdown ends in 4 hours
                        ],

                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "mutable-content" => 1
                            ],
                        ],
                        "fcm_options" => [
                            "image" =>  $imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail
                        ]
                    ],

                    "android" => [
                        "notification" => [
                            "image" =>$imageUrl ?? asset('/storage/app/public/images') . '/' . $item->product_thumbnail, 
                        ]
                    ],
                ]
            ];

            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/interior-chowk/messages:send", $payload);

            $responses[] = [
                'token'    => $item->cm_firebase_token,
                'response' => $response->json(),
            ];
        }

        return [
            'ProductNotificationStatus'    => 'success',
            'ProductNotificationCount'     => count($responses),
            'ProductNotificationResponses' => $responses
        ];
    }


}