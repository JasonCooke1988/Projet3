<?php

namespace App\Model\Factory;

use PDO;

/**
 * Class PDOFactory
 * Creates the Connection if it doesn't exist
 */
class PDOFactory
{
    /**
     * Stores the Connection
     */
    private static $pdo = null;

    /**
     * Returns the Connection if it exists or creates it before returning it
     */
    public static function getPDO()
    {
        require_once '../config/db.php';
        if (self::$pdo === null) {
            self::$pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
            self::$pdo->exec('SET NAMES UTF8');
        }
        return self::$pdo;
    }
}