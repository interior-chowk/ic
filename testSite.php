<?php
require __DIR__.'/vendor/autoload.php'; // Adjust the path if necessary

$host = env('DB_HOST');
$dbname = env('DB_DATABASE');
$user = env('DB_USERNAME');
$password = env('DB_PASSWORD');
echo $host;
?>