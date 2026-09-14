<?php

namespace OrderSync;

/**
 * Utilidad de conexión a la base de datos gestion_pedidos (MySQL/MariaDB),
 * definida en la evidencia GA6-220501096-AA2-EV03, usando PDO.
 *
 * Implementa un patrón singleton sencillo: la primera vez que se solicita
 * la conexión, se crea; las siguientes veces se reutiliza la misma.
 */
class Database
{
    private static ?\PDO $conexion = null;

    private const HOST = "localhost";
    private const PUERTO = "3306";
    private const BASE_DATOS = "gestion_pedidos";
    private const USUARIO = "ordersync";
    private const CONTRASENA = "OrderSync2026!";

    public static function obtenerConexion(): \PDO
    {
        if (self::$conexion === null) {
            $dsn = "mysql:host=" . self::HOST . ";port=" . self::PUERTO
                 . ";dbname=" . self::BASE_DATOS . ";charset=utf8mb4";

            self::$conexion = new \PDO($dsn, self::USUARIO, self::CONTRASENA, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        }

        return self::$conexion;
    }
}
