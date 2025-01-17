<?php

namespace Core\Database;


/**
 * Clase base abstracta para implementar el patrón Repository.
 *
 * Esta clase proporciona una implementación base de operaciones CRUD comunes
 * que pueden ser utilizadas por cualquier entidad del sistema. Los repositorios
 * específicos pueden extender esta clase y añadir métodos específicos según
 * sus necesidades.
 *
 * @package Core\Database
 */
abstract class Repository
{
    /** @var string Nombre de la tabla en la base de datos */
    protected static string $table;

    /** @var string Nombre de la clave primaria de la tabla */
    protected static string $primaryKey = 'id';

    /**
     * Encuentra un registro por su ID.
     *
     * @param int $id ID del registro a buscar
     * @return array<string, mixed>|null Datos del registro o null si no se encuentra
     *
     * @example Uso
     * ```php
     * $user = $repository->find(1);
     * if ($user) {
     *     echo $user['name'];
     * }
     * ```
     */
    public static function find(int $id): ?array
    {
        $query = "SELECT * FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id LIMIT 1";
        $result = DataBase::select($query, ['id' => $id]);
        return $result ? $result[0] : null;
    }

    /**
     * Encuentra todos los registros que coinciden con los criterios especificados.
     *
     * @param array<string, mixed> $criteria Criterios de búsqueda (campo => valor)
     * @param array<string, string> $orderBy Criterios de ordenamiento (campo => dirección)
     * @param int|null $limit Límite de resultados a retornar
     * @return array<int, array<string, mixed>> Lista de registros encontrados
     *
     * @example Uso
     * ```php
     * $activeUsers = $repository->findBy(
     *     ['status' => 'active'],
     *     ['created_at' => 'DESC'],
     *     10
     * );
     * ```
     */
    public static function findBy(array $criteria, array $orderBy = [], ?int $limit = null): array
    {
        $query = "SELECT * FROM " . static::$table;

        if (!empty($criteria)) {
            $conditions = array_map(fn($field) => "$field = :$field", array_keys($criteria));
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        if (!empty($orderBy)) {
            $orders = array_map(
                fn($field, $direction) => "$field $direction",
                array_keys($orderBy),
                array_values($orderBy)
            );
            $query .= " ORDER BY " . implode(', ', $orders);
        }

        if ($limit !== null) {
            $query .= " LIMIT $limit";
        }
        return Database::select($query, $criteria);
    }

    /**
     * Crea un nuevo registro en la base de datos.
     *
     * @param array<string, mixed> $data Datos del nuevo registro
     * @return int ID del registro creado
     * @throws DatabaseException Si ocurre un error durante la inserción
     *
     * @example Uso
     * ```php
     * $userId = $repository->create([
     *     'name' => 'John Doe',
     *     'email' => 'john@example.com'
     * ]);
     * ```
     */
    public static function create(array $data): int
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($field) => ":$field", $fields);

        $query = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            static::$table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );

        return Database::insert($query, $data);
    }

    /**
     * Actualiza un registro existente.
     *
     * @param int $id ID del registro a actualizar
     * @param array<string, mixed> $data Nuevos datos del registro
     * @return int Número de filas afectadas
     * @throws DatabaseException Si ocurre un error durante la actualización
     *
     * @example Uso
     * ```php
     * $affected = $repository->update(1, [
     *     'email' => 'newemail@example.com'
     * ]);
     * ```
     */
    public static function update(int $id, array $data): int
    {
        $fields = array_map(fn($field) => "$field = :$field", array_keys($data));

        $query = sprintf(
            "UPDATE %s SET %s WHERE %s = :id",
            static::$table,
            implode(', ', $fields),
            static::$primaryKey
        );

        $data['id'] = $id;
        return Database::update($query, $data);
    }

    /**
     * Elimina un registro.
     *
     * @param int $id ID del registro a eliminar
     * @return int Número de filas afectadas
     * @throws DatabaseException Si ocurre un error durante la eliminación
     *
     * @example Uso
     * ```php
     * $deleted = $repository->delete(1);
     * ```
     */
    public static function delete(int $id): int
    {
        $query = "DELETE FROM " . static::$table . " WHERE " . static::$primaryKey . " = :id";
        return Database::delete($query, ['id' => $id]);
    }

    /**
     * Cuenta el número de registros que coinciden con los criterios.
     *
     * @param array<string, mixed> $criteria Criterios de búsqueda (campo => valor)
     * @return int Número de registros encontrados
     * @throws DatabaseException Si ocurre un error durante la consulta
     *
     * @example Uso
     * ```php
     * $activeCount = $repository->count(['status' => 'active']);
     * ```
     */
    public static function count(array $criteria = []): int
    {
        $query = "SELECT COUNT(*) as count FROM " . static::$table;

        if (!empty($criteria)) {
            $conditions = array_map(fn($field) => "$field = :$field", array_keys($criteria));
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        $result = DataBase::select($query, $criteria);
        return (int)$result[0]['count'];
    }
}
