<?php

namespace App\Features\Feed;

use App\Features\Post\PostRepository;
use App\Features\User\UserRepository;
use Core\Auth\Auth;
use Core\Controller;

class FeedController extends Controller
{
    public static function showFeed(): string
    {
        $userId = Auth::id();

        // Obtener los post para el feed
        $posts = PostRepository::getFeedEntries($userId);

        $following = UserRepository::getFollowing($userId);

        return self::view('feed', [
            'posts' => $posts,
            'following' => $following,
            'username' => Auth::user()
        ]);
    }

    /**
     * Maneja la lógica para alternar el like de una publicación
     */
    public static function toggleLike(int $postId): void
    {
        $userId = Auth::id();

        try {
            $hasLiked = PostRepository::hasLiked($userId, $postId);

            if ($hasLiked) {
                PostRepository::removeLike($postId, $userId);
            } else {
                // Si había un dislike, lo quitamos primero
                if (PostRepository::hasDisliked($userId, $postId)) {
                    PostRepository::removeDislike($postId, $userId);
                }
                PostRepository::addLike($postId, $userId);
            }
        } catch (\Exception $e) {
            // Si hay un error, podríamos guardar el error en los logs
            error_log($e->getMessage());
        }

        // Redirigimos de vuelta a la página anterior
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Maneja la lógica para alternar el dislike de una publicación
     */
    public static function toggleDislike(int $postId): void 
    {
        $userId = Auth::id();
        
        try {
            $hasDisliked = PostRepository::hasDisliked($userId, $postId);

            if ($hasDisliked) {
                PostRepository::removeDislike($postId, $userId);
            } else {
                // Si había un like, lo quitamos primero
                if (PostRepository::hasLiked($userId, $postId)) {
                    PostRepository::removeLike($postId, $userId);
                }
                PostRepository::addDislike($postId, $userId);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        // Redirigimos de vuelta a la página anterior
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
