<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Database;
use PDO;

abstract class Model
{
    protected static function db(): PDO
    {
        return Database::getConnection();
    }

    public static function beginTransaction(): bool
    {
        return self::db()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::db()->commit();
    }

    public static function rollBack(): bool
    {
        if (self::db()->inTransaction()) {
            return self::db()->rollBack();
        }
        return false;
    }
}

