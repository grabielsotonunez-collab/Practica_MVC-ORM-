<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class Database
{
    private static ?PDO $conexion = null;

    public static function connection(): PDO
    {
        if (self::$conexion instanceof PDO) {
            return self::$conexion;
        }

        $configuracion = require dirname(__DIR__, 2)
            .'/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $configuracion['host'],
            $configuracion['port'],
            $configuracion['database'],
            $configuracion['charset'],
        );

        try {
            self::$conexion = new PDO(
                $dsn,
                $configuracion['username'],
                $configuracion['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ],
            );
        } catch (PDOException $excepcion) {
            throw new RuntimeException(
                'No fue posible conectar con la base de datos.',
                0,
                $excepcion,
            );
        }

        return self::$conexion;
    }
}
