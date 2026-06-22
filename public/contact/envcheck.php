<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

function mas(?string $v): string {
    if(!$v) return 'VACIO';
    return substr($v, 0, 3) . str_repeat('*', max(0, strlen($v)-6)) . substr($v, -3);
}

echo "SMTP_HOST:" .  (($_ENV['SMTP_HOST'] ?? '') ?: 'VACIO') . "<br>";
echo "SMTP_USER: " . mas($_ENV['SMTP_USER'] ?? '') . "<br>";
echo "SMTP_PASS: " . (($_ENV['SMTP_PASS'] ?? '') ? 'OK (set)' : 'VACIO') . "<br>";
echo "SMTP_PORT: " . (($_ENV['SMTP_PORT'] ?? '') ?: 'VACIO') . "<br>";