<?php
/**
 * KIDSS database connection.
 *
 * The application uses PDO with MySQL prepared statements.
 * You can override the defaults with server environment variables:
 *   KIDSS_DB_HOST
 *   KIDSS_DB_PORT
 *   KIDSS_DB_NAME
 *   KIDSS_DB_USER
 *   KIDSS_DB_PASS
 */

declare(strict_types=1);

function getDatabaseConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('KIDSS_DB_HOST') ?: '127.0.0.1';
    $port = getenv('KIDSS_DB_PORT') ?: '3306';
    $database = getenv('KIDSS_DB_NAME') ?: 'kidss_db';
    $username = getenv('KIDSS_DB_USER') ?: 'root';
    $password = getenv('KIDSS_DB_PASS');
    $password = $password === false ? '' : $password;

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('KIDSS database connection failed: ' . $e->getMessage());
        throw new RuntimeException('Database connection failed.', 0, $e);
    }

    return $pdo;
}

// Convenient shared connection for files that simply require database.php.
$pdo = getDatabaseConnection();
