<?php

namespace App\Features\User;

use Core\Database\Repository;
use Core\Database\Database;
use Core\Exceptions\DatabaseException;
use Exception;

/**
 * Repositorio para la gestión de usuarios en la red social.
 *
 * Esta clase implementa operaciones específicas para el manejo de usuarios,
 * incluyendo funcionalidades de red social como seguimiento entre usuarios
 * y búsqueda por nombre de usuario.
 *
 * @package App\Repositories
 */
class UserRepository extends Repository
{
    /** @var string Nombre de la tabla de usuarios */
    protected static string $table = 'users';

    /**
     * Encuentra un usuario por su nombre de usuario.
     *
     * @param string $username Nombre de usuario a buscar
     * @return array<string, mixed>|null Datos del usuario o null si no se encuentra
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example Uso
     * ```php
     * $user = $userRepo->findByUsername('john_doe');
     * if ($user) {
     *     echo "Usuario encontrado: " . $user['email'];
     * }
     * ```
     */
    public static function findByUsername(string $username): ?array
    {
        $result = self::findBy(['user' => $username], [], 1);
        return $result ? $result[0] : null;
    }

    /**
     * Encuentra un usuario por su email.
     *
     * @param string $username Email del usuario a buscar
     * @return array<string, mixed>|null Datos del usuario o null si no se encuentra
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example Uso
     * ```php
     * $user = $userRepo->findByEmail('john_doe@mail.com');
     * if ($user) {
     *     echo "Usuario encontrado: " . $user['user'];
     * }
     * ```
     */
    public static function findByEmail(string $email): ?array
    {
        $result = self::findBy(['email' => $email], [], 1);
        return $result ? $result[0] : null;
    }

    /**
     * Busca usuarios por coincidencia parcial en el nombre de usuario.
     *
     * @param string $search Término de búsqueda
     * @return array<int, array<string, mixed>> Lista de usuarios que coinciden
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example Uso
     * ```php
     * $users = $userRepo->searchByUsername('john');
     * foreach ($users as $user) {
     *     echo $user['user'] . "\n";
     * }
     * ```
     */
    public static function searchByUsername(string $search): array
    {
        $query = "SELECT * FROM " . self::$table . " WHERE user LIKE :search";
        return Database::select($query, ['search' => "%$search%"]);
    }

    /**
     * Obtiene todos los seguidores de un usuario.
     *
     * @param int $userId ID del usuario
     * @return array<int, array<string, mixed>> Lista de usuarios seguidores
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example Uso
     * ```php
     * $followers = $userRepo->getFollowers(1);
     * echo "Número de seguidores: " . count($followers);
     * ```
     */
    public static function getFollowers(int $userId): array
    {
        $query = "SELECT u.* FROM users u
                 INNER JOIN follows f ON f.user_id = u.id
                 WHERE f.user_followed = :userId";
        return Database::select($query, ['userId' => $userId]);
    }

    /**
     * Obtiene todos los usuarios que sigue un usuario.
     *
     * @param int $userId ID del usuario
     * @return array<int, array<string, mixed>> Lista de usuarios seguidos
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example Uso
     * ```php
     * $following = $userRepo->getFollowing(1);
     * echo "Siguiendo a: " . count($following) . " usuarios";
     * ```
     */
    public static function getFollowing(int $userId): array
    {
        $query = "SELECT u.* FROM users u
                 INNER JOIN follows f ON f.user_followed = u.id
                 WHERE f.user_id = :userId";
        return Database::select($query, ['userId' => $userId]);
    }

    /**
     * Hace que un usuario siga a otro.
     *
     * @param int $userId ID del usuario que seguirá
     * @param int $followedId ID del usuario a seguir
     * @return bool true si la operación fue exitosa, false en caso contrario
     *
     * @example Uso
     * ```php
     * if ($userRepo->follow(1, 2)) {
     *     echo "Usuario 1 ahora sigue al usuario 2";
     * }
     * ```
     */
    public static function follow(int $userId, int $followedId): bool
    {
        try {
            Database::insert(
                "INSERT INTO follows (user_id, user_followed) VALUES (:userId, :followedId)",
                ['userId' => $userId, 'followedId' => $followedId]
            );
            return true;
        } catch (DatabaseException $e) {
            return false;
        }
    }

    /**
     * Hace que un usuario deje de seguir a otro.
     *
     * @param int $userId ID del usuario que dejará de seguir
     * @param int $followedId ID del usuario que dejará de ser seguido
     * @return bool true si la operación fue exitosa, false en caso contrario
     *
     * @example
     * ```php
     * if ($userRepo->unfollow(1, 2)) {
     *     echo "Usuario 1 ya no sigue al usuario 2";
     * }
     * ```
     */
    public static function unfollow(int $userId, int $followedId): bool
    {
        try {
            Database::delete(
                "DELETE FROM follows WHERE user_id = :userId AND user_followed = :followedId",
                ['userId' => $userId, 'followedId' => $followedId]
            );
            return true;
        } catch (DatabaseException  $e) {
            return false;
        }
    }

    /**
     * Verifica si un usuario sigue a otro.
     *
     * @param int $userId ID del usuario seguidor
     * @param int $followedId ID del usuario seguido
     * @return bool true si el usuario sigue al otro, false en caso contrario
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example
     * ```php
     * if ($userRepo->isFollowing(1, 2)) {
     *     echo "El usuario 1 sigue al usuario 2";
     * }
     * ```
     */
    public static function isFollowing(int $userId, int $followedId): bool
    {
        $query = "SELECT COUNT(*) as count FROM follows 
                 WHERE user_id = :userId AND user_followed = :followedId";
        $result = Database::select($query, [
            'userId' => $userId,
            'followedId' => $followedId
        ]);
        return (int)$result[0]['count'] > 0;
    }

    /**
     * Obtiene el número de seguidores de un usuario.
     *
     * @param int $userId ID del usuario
     * @return int Número de seguidores
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example
     * ```php
     * $followersCount = $userRepo->getFollowersCount(1);
     * echo "Tienes $followersCount seguidores";
     * ```
     */
    public static function getFollowersCount(int $userId): int
    {
        $query = "SELECT COUNT(*) as count FROM follows WHERE user_followed = :userId";
        $result = Database::select($query, ['userId' => $userId]);
        return (int)$result[0]['count'];
    }

    /**
     * Obtiene el número de usuarios que sigue un usuario.
     *
     * @param int $userId ID del usuario
     * @return int Número de usuarios seguidos
     * @throws DatabaseException Si ocurre un error en la consulta
     *
     * @example
     * ```php
     * $followingCount = $userRepo->getFollowingCount(1);
     * echo "Sigues a $followingCount usuarios";
     * ```
     */
    public static function getFollowingCount(int $userId): int
    {
        $query = "SELECT COUNT(*) as count FROM follows WHERE user_id = :userId";
        $result = Database::select($query, ['userId' => $userId]);
        return (int)$result[0]['count'];
    }

    public static function register(string $username, string $email, string $password): ?array
    {
        try {
            Database::beginTransaction();

            // Hasheamos la contraseña de forma segura
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT, ['cost' => 11]);

            $userId = Database::insert(
                "INSERT INTO " . self::$table . " (user, email, password) VALUES (:user, :email, :password)",
                [
                    'user' => $username,
                    'email' => $email,
                    'password' => $hashedPassword
                ]
            );

            if (!$userId) {
                Database::rollback();
                return null;
            }

            // Obtenemos los datos del usuario creado
            $user = self::find($userId);

            Database::commit();
            return $user;
        } catch (DatabaseException $e) {
            Database::rollback();
            throw new Exception("Error: " . $e->getMessage());
            ;
        }
    }
}
