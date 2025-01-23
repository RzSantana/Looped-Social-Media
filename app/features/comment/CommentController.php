<?php

namespace App\Features\Comment;

use Core\Auth\Auth;
use Core\Controller;
use Core\Session;

class CommentController extends Controller
{
    /**
     * Procesa la creación de un nuevo comentario.
     */
    public static function create(): void
    {
        // Validación básica de datos
        $postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
        $text = trim($_POST['text'] ?? '');

        if (!$postId) {
            header('Location: /post/' . $postId);
            exit;
        }

        if (empty($text)) {
            Session::flash('error', 'El comentario no puede estar vacío');
            header('Location: /post/' . $postId);
            exit;
        }

        // Limitamos la longitud del comentario
        if (strlen($text) > 500) {
            Session::flash('error', 'El comentario es demasiado largo');
            header('Location: /post/' . $postId);
            exit;
        }

        try {
            CommentRepository::createComment($postId, Auth::id(), $text);

            // Redirigimos de vuelta a la publicación
            header("Location: /post/$postId");
        } catch (\Exception $e) {
            Session::flash('error', 'Error al crear el comentario');
            header("Location: /post/$postId");
        }
    }

    /**
     * Elimina un comentario.
     */
    public static function delete(int $commentId): void
    {
        try {
            // Verificamos si el usuario puede eliminar el comentario
            if (!CommentRepository::canDelete($commentId, Auth::id())) {
                Session::flash('error', 'No tienes permiso para eliminar este comentario');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Eliminamos el comentario
            CommentRepository::delete($commentId);

            Session::flash('success', 'Comentario eliminado correctamente');
        } catch (\Exception $e) {
            Session::flash('error', 'Error al eliminar el comentario');
        }

        // Redirigimos de vuelta a la página anterior
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
