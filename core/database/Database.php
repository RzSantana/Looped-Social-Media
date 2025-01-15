<?php

namespace Core\Database;

use Core\App;
use Core\Exceptons\DatabaseExpection;
use PDO;
use PDOException;

/**
 * Clase principal para el manejo de la conexión y operaciones con la base de datos.
 * 
 * Esta clase implementa el patrón Singleton para mantener una única conexión a la base
 * de datos durante toda la ejecución de la aplicación. Proporciona métodos para realizar
 * operaciones CRUD básicas de manera segura usando PDO.
 * 
 * Ejemplo de uso:
 * ```php
 * // Realizar una consulta SELECT
 * $users = Database::select("SELECT * FROM users WHERE active = :active", ['active' => 1]);
 * 
 * // Insertar un nuevo registro
 * $id = Database::insert(
 *     "INSERT INTO users (name, email) VALUES (:name, :email)",
 *     ['name' => 'John', 'email' => 'john@example.com']
 * );
 * ```
 * 
 * @package Core\Database
 */
class DataBase
{
    /** @var PDO|null Instancia de conexión PDO */
    private static ?PDO $connection = null;

    /**
     * Configuración de la conexión a la base de datos
     * 
     * @var array<string, string>
     */
    private static array $config = [
        'host' => App::env('DB_HOST'),
        'dbname' => App::env('DB_NAME'),
        'user' => App::env('DB_USER'),
        'password' => App::env('DB_PASS'),
        'charset' => 'utf8mb4'
    ];

    /**
     * Obtiene la conexión a la base de datos, creándola si no existe.
     * 
     * Este método implementa el patrón Singleton para asegurar que solo existe
     * una conexión a la base de datos durante toda la ejecución de la aplicación.
     * 
     * @throws DatabaseException Si hay un error al establecer la conexión
     * @return PDO Conexión activa a la base de datos
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=%s",
                    self::$config['host'],
                    self::$config['dbname'],
                    self::$config['charset']
                );

                self::$connection = new PDO(
                    $dsn,
                    self::$config['user'],
                    self::$config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $exception) {
                throw new DatabaseExpection("Error de conexión: " . $exception->getMessage());
            }

            return self::$connection;
        }
    }

    /**
     * Ejecuta una consulta SELECT y retorna los resultados.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array<string, mixed> $params Parámetros para la consulta preparada
     * @throws DatabaseException Si hay un error al ejecutar la consulta
     * @return array<int, array<string, mixed>> Resultado de la consulta como array asociativo
     * 
     * @example Uso
     * ```php
     * $users = Database::select(
     *     "SELECT * FROM users WHERE status = :status",
     *     ['status' => 'active']
     * );
     * ```
     */
    public static function select(string $query, array $params = []): array
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $exception) {
            throw new DatabaseExpection("Error en la consulta: " . $exception->getMessage());
        }
    }


    /**
     * Ejecuta una consulta INSERT y retorna el ID del último registro insertado.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array<string, mixed> $params Parámetros para la consulta preparada
     * @throws DatabaseException Si hay un error al ejecutar la inserción
     * @return int ID del registro insertado
     * 
     * @example Uso
     * ```php
     * $userId = Database::insert(
     *     "INSERT INTO users (name, email) VALUES (:name, :email)",
     *     ['name' => 'John', 'email' => 'john@example.com']
     * );
     * ```
     */
    public static function insert(string $query, array $params = []): int
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            $stmt->execute($params);
            return self::getConnection()->lastInsertId();
        } catch (PDOException $exception) {
            throw new DatabaseExpection("Error en la inserción: " . $exception->getMessage());
        }
    }

    /**
     * Ejecuta una consulta UPDATE y retorna el número de filas afectadas.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array<string, mixed> $params Parámetros para la consulta preparada
     * @throws DatabaseException Si hay un error al ejecutar la actualización
     * @return int Número de filas afectadas
     * 
     * @example Uso
     * ```php
     * $affected = Database::update(
     *     "UPDATE users SET status = :status WHERE id = :id",
     *     ['status' => 'inactive', 'id' => 1]
     * );
     * ```
     */
    public static function update(string $query, array $params = []): int
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $exception) {
            throw new DatabaseExpection("Error en actualización: " . $exception->getMessage());
        }
    }

    /**
     * Ejecuta una consulta DELETE y retorna el número de filas afectadas.
     * 
     * @param string $query Consulta SQL con marcadores de posición
     * @param array<string, mixed> $params Parámetros para la consulta preparada
     * @throws DatabaseException Si hay un error al ejecutar la eliminación
     * @return int Número de filas afectadas
     * 
     * @example Uso
     * ```php
     * $affected = Database::delete(
     *     "DELETE FROM users WHERE id = :id",
     *     ['id' => 1]
     * );
     * ```
     */
    public static function delete(string $query, array $params = []): int
    {
        try {
            $stmt = self::getConnection()->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $exception) {
            throw new DatabaseExpection("Error en la eliminacion: " . $exception->getMessage());
        }
    }

    /**
     * Inicia una transacción en la base de datos.
     * 
     * @throws DatabaseException Si hay un error al iniciar la transacción
     */
    public static function beginTransaction(): void
    {
        self::getConnection()->beginTransaction();
    }

    /**
     * Confirma una transacción activa.
     * 
     * @throws DatabaseException Si hay un error al confirmar la transacción
     */
    public static function commit(): void
    {
        self::getConnection()->commit();
    }

    /**
     * Revierte una transacción activa.
     * 
     * @throws DatabaseException Si hay un error al revertir la transacción
     */
    public static function rollback(): void
    {
        self::getConnection()->rollBack();
    }
}
