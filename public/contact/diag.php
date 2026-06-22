<?php 
declare(strict_types=1);

ini_set('display_error', '1');
error_reporting(E_ALL);

echo "PHP: " . PHP_VERSION . "<br>";
echo "__DIR__:" . __DIR__ . "<br>";

$autoload = __DIR__ . "/../vendor/autoload.php";
echo "autoload path: {$autoload}<br>";
echo "autoload exists? " . (file_exists($autoload) ? "YES" :  "NO") . "<br>";

require $autoload;

echo "Composer autoload OK <br>";

echo "Dotenv class exists? " . (class_exists(\Dotenv\Dotenv::class) ? "YES" : "NO") . "<br>";
echo "PHPMailer class exists?" . (class_exists(\PHPMailer\PHPMailer\PHPMailer::class) ? "YES" : "NO"). "<br>";