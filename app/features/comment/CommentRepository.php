<?php

namespace App\Features\Comment;

use Core\Database\Repository;
use Core\Database\Database;

/**
 * Repositorio para la gestión de comentarios en la red social.
 */
class CommentRepository extends Repository
{
    /** @var string Nombre de la tabla de comentarios */
    protected static string $table = 'comments';

    /**
     * Obtiene todos los comentarios de una publicación específica.
     *
     * @param int $postId ID de la publicación
     * @return array Lista de comentarios con información del usuario
     */
    public static function getByEntry(int $postId): array
    {
        $query = "SELECT c.*, u.user as username
                 FROM comments c
                 INNER JOIN users u ON c.user_id = u.id
                 WHERE c.entry_id = :postId
                 ORDER BY c.date DESC";

        return Database::select($query, ['postId' => $postId]);
    }

    /**
     * Crea un nuevo comentario.
     *
     * @param int $postId ID de la publicación
     * @param int $userId ID del usuario que comenta
     * @param string $text Contenido del comentario
     * @return int ID del comentario creado
     */
    public static function createComment(int $postId, int $userId, string $text): int
    {
        return self::create([
            'entry_id' => $postId,
            'user_id' => $userId,
            'text' => $text,
            'date' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Elimina todos los comentarios de una publicación.
     *
     * @param int $postId ID de la publicación
     * @return int Número de comentarios eliminados
     */
    public static function deleteByEntry(int $postId): int
    {
        return Database::delete(
            "DELETE FROM " . self::$table . " WHERE entry_id = :postId",
            ['postId' => $postId]
        );
    }

    /**
     * Verifica si un usuario puede eliminar un comentario.
     * Un usuario puede eliminar un comentario si es el autor del comentario
     * o si es el autor de la publicación.
     *
     * @param int $commentId ID del comentario
     * @param int $userId ID del usuario
     * @return bool true si el usuario puede eliminar el comentario
     */
    public static function canDelete(int $commentId, int $userId): bool
    {
        $query = "SELECT COUNT(*) as count
                 FROM comments c
                 LEFT JOIN entries e ON c.entry_id = e.id
                 WHERE (c.id = :commentId)
                 AND (c.user_id = :userId OR e.user_id = :userId)";

        $result = Database::select($query, [
            'commentId' => $commentId,
            'userId' => $userId
        ]);

        return (int)$result[0]['count'] > 0;
    }
}
