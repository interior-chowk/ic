<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Shiprocket Credentilas
    |--------------------------------------------------------------------------
    |
    | Here you can set the default shiprocket credentilas. However, you can pass the credentials while connecting to shiprocket client
    | 
    */

    'credentials' => [
        // 'email' => env('SHIPROCKET_EMAIL', 'youemail@email.com'),
        // 'password' => env('SHIPROCKET_PASSWORD', 'secret'),
        'email' => env('SHIPROCKET_EMAIL', 'app@interiorchowk.com'),
        'password' => env('SHIPROCKET_PASSWORD', 'App@#$12'),
        'endpoint' => env('SHIPROCKET_API_URL', 'https://apiv2.shiprocket.in/v1/external'),
    ],
    
    // 'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwaXYyLnNoaXByb2NrZXQuaW4vdjEvZXh0ZXJuYWwvYXV0aC9sb2dpbiIsImlhdCI6MTcxMTU4NTgwMiwiZXhwIjoxNzEyNDQ5ODAyLCJuYmYiOjE3MTE1ODU4MDIsImp0aSI6Imc0eUxFUzV0U2F4UERsbmYiLCJzdWIiOjM2MzQ1NjAsInBydiI6IjA1YmI2NjBmNjdjYWM3NDVmN2IzZGExZWVmMTk3MTk1YTIxMWU2ZDkiLCJjaWQiOjM1MDIxMzZ9.Uujn8QMosr2NgGqCfp-srpSsGe6NCHlckVmfMPDjWEU',
    
    //'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjM2MzQ1NjAsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzQwOTgwMzQyLCJqdGkiOiJNWDltR0hrcExzSGJoQ1hGIiwiaWF0IjoxNzQwMTE2MzQyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTc0MDExNjM0MiwiY2lkIjozNTAyMTM2LCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.l5z7yKy3LmFkdzKvG26vbuVe8oUz_e8FgwmVHOeZgEg',
    'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjU5NTAxMzUsInNvdXJjZSI6InNyLWF1dGgtaW50IiwiZXhwIjoxNzQ5NDY0NzgyLCJqdGkiOiJNbGhxQ0N5VFNxa1ZQNHVzIiwiaWF0IjoxNzQ4NjAwNzgyLCJpc3MiOiJodHRwczovL3NyLWF1dGguc2hpcHJvY2tldC5pbi9hdXRob3JpemUvdXNlciIsIm5iZiI6MTc0ODYwMDc4MiwiY2lkIjozNTAyMTM2LCJ0YyI6MzYwLCJ2ZXJib3NlIjpmYWxzZSwidmVuZG9yX2lkIjowLCJ2ZW5kb3JfY29kZSI6IiJ9.qBiPksjUJluPdlNxz1EOz_R6br42fgBJj5EjGVpAkFo',
    'expires_on' => '2026-05-30 23:39:02',


    /*
    |--------------------------------------------------------------------------
    | Default output response type
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the output response you need.
    | 
    | Supported: "collection" , "object", "array"
    | 
    */

    'responseType' => 'collection',
];
