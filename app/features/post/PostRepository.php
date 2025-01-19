<?php

namespace App\Features\Post;

use Core\Database\Repository;
use Core\Database\Database;
use Core\Exceptions\DatabaseException;

class PostRepository extends Repository
{
    protected static string $table = 'entries';

    /**
     * Obtiene las entradas del feed para un usuario específico.
     * Incluye las entradas de los usuarios que sigue y sus propias entradas.
     * 
     * @param int $userId ID del usuario
     * @param int $limit Límite de entradas a obtener
     * @param int $offset Desplazamiento para paginación
     * @return array Lista de entradas con información de usuario y métricas
     */
    public static function getFeedEntries(int $userId, int $limit = 10, int $offset = 0): array
    {
        $query = "
            SELECT 
                e.*,
                u.user as author_name,
                (SELECT COUNT(*) FROM likes WHERE entry_id = e.id) as likes_count,
                (SELECT COUNT(*) FROM dislikes WHERE entry_id = e.id) as dislikes_count,
                (SELECT COUNT(*) FROM comments WHERE entry_id = e.id) as comments_count,
                EXISTS(SELECT 1 FROM likes WHERE entry_id = e.id AND user_id = :current_user) as user_liked,
                EXISTS(SELECT 1 FROM dislikes WHERE entry_id = e.id AND user_id = :current_user2) as user_disliked
            FROM entries e
            INNER JOIN users u ON e.user_id = u.id
            WHERE e.user_id IN (
                SELECT user_followed FROM follows WHERE user_id = :user_id
                UNION
                SELECT :user_id2
            )
            ORDER BY e.date DESC
            LIMIT :limit OFFSET :offset
        ";

        return Database::select($query, [
            'user_id' => $userId,
            'user_id2' => $userId,
            'current_user' => $userId,
            'current_user2' => $userId,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    /**
     * Obtiene una entrada específica con toda su información de interacciones
     * y el estado actual del usuario que hace la petición.
     */
    public static function getEntry(int $postId, int $userId): ?array
    {
        $query = "
            SELECT 
                e.*,
                u.user as author_name,
                (SELECT COUNT(*) FROM likes WHERE entry_id = e.id) as likes_count,
                (SELECT COUNT(*) FROM dislikes WHERE entry_id = e.id) as dislikes_count,
                (SELECT COUNT(*) FROM comments WHERE entry_id = e.id) as comments_count,
                EXISTS(SELECT 1 FROM likes WHERE entry_id = e.id AND user_id = :user_id) as user_liked,
                EXISTS(SELECT 1 FROM dislikes WHERE entry_id = e.id AND user_id = :user_id2) as user_disliked
            FROM entries e
            INNER JOIN users u ON e.user_id = u.id
            WHERE e.id = :entry_id
            LIMIT 1
        ";

        $result = Database::select($query, [
            'entry_id' => $postId,
            'user_id' => $userId,
            'user_id2' => $userId
        ]);

        return $result ? $result[0] : null;
    }

    /**
     * Verifica si un usuario ha dado like a una entrada
     */
    public static function hasLiked(int $userId, int $postId): bool
    {
        $result = Database::select(
            "SELECT COUNT(*) as count FROM likes WHERE user_id = :userId AND entry_id = :postId",
            ['userId' => $userId, 'postId' => $postId]
        );
        return $result[0]['count'] > 0;
    }

    /**
     * Verifica si un usuario ha dado dislike a una entrada
     */
    public static function hasDisliked(int $userId, int $postId): bool
    {
        $result = Database::select(
            "SELECT COUNT(*) as count FROM dislikes WHERE user_id = :userId AND entry_id = :postId",
            ['userId' => $userId, 'postId' => $postId]
        );
        return $result[0]['count'] > 0;
    }

    /**
     * Añade un "me gusta" a una entrada
     */
    public static function addLike(int $postId, int $userId): void
    {
        Database::insert(
            "INSERT INTO likes (entry_id, user_id) VALUES (:entry_id, :user_id)",
            ['entry_id' => $postId, 'user_id' => $userId]
        );
    }

    /**
     * Elimina un "me gusta" de una entrada
     */
    public static function removeLike(int $postId, int $userId): void
    {
        Database::delete(
            "DELETE FROM likes WHERE entry_id = :entry_id AND user_id = :user_id",
            ['entry_id' => $postId, 'user_id' => $userId]
        );
    }

    /**
     * Añade un dislike a una entrada
     */
    public static function addDislike(int $postId, int $userId): void
    {
        Database::insert(
            "INSERT INTO dislikes (entry_id, user_id) VALUES (:entry_id, :user_id)",
            ['entry_id' => $postId, 'user_id' => $userId]
        );
    }

    /**
     * Elimina un dislike de una entrada
     */
    public static function removeDislike(int $postId, int $userId): void
    {
        Database::delete(
            "DELETE FROM dislikes WHERE entry_id = :entry_id AND user_id = :user_id",
            ['entry_id' => $postId, 'user_id' => $userId]
        );
    }

    /**
     * Crea una nueva entrada
     */
    public static function createEntry(int $userId, string $text): int
    {
        return self::create([
            'user_id' => $userId,
            'text' => $text,
            'date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Elimina una entrada y todas sus interacciones asociadas
     */
    public static function deleteEntry(int $postId, int $userId): bool
    {
        try {
            Database::beginTransaction();

            // Verificamos que el usuario sea el propietario de la entrada
            $entry = self::getPost($postId, $userId);
            if (!$entry || $entry['user_id'] !== $userId) {
                Database::rollback();
                return false;
            }

            // Eliminamos todos los likes
            Database::delete(
                "DELETE FROM likes WHERE entry_id = :entry_id",
                ['entry_id' => $postId]
            );

            // Eliminamos todos los dislikes
            Database::delete(
                "DELETE FROM dislikes WHERE entry_id = :entry_id",
                ['entry_id' => $postId]
            );

            // Eliminamos todos los comentarios
            Database::delete(
                "DELETE FROM comments WHERE entry_id = :entry_id",
                ['entry_id' => $postId]
            );

            // Finalmente eliminamos la entrada
            Database::delete(
                "DELETE FROM entries WHERE id = :entry_id AND user_id = :user_id",
                ['entry_id' => $postId, 'user_id' => $userId]
            );

            Database::commit();
            return true;
        } catch (DatabaseException $e) {
            Database::rollback();
            throw $e;
        }
    }

    /**
     * Añade un comentario a una entrada
     */
    public static function addComment(int $postId, int $userId, string $text): int
    {
        return Database::insert(
            "INSERT INTO comments (entry_id, user_id, text, date) VALUES (:entry_id, :user_id, :text, NOW())",
            [
                'entry_id' => $postId,
                'user_id' => $userId,
                'text' => $text
            ]
        );
    }
}
