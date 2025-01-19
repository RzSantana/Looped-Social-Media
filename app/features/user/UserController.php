<?php

namespace App\Features\User;

use Core\Auth\Auth;
use Core\Controller;
use App\Features\Post\PostRepository;
use Exception;

class UserController extends Controller
{
    public static function showProfile(): string
    {
        $userId = Auth::id();

        // Obtener datos del usuario actual
        $user = UserRepository::find($userId);

        // Obtener posts del usuario
        $posts = PostRepository::getPostsByUser($userId);

        // Obtener métricas de seguimiento
        $followersCount = UserRepository::getFollowersCount($userId);
        $followingCount = UserRepository::getFollowingCount($userId);

        return self::view('profile', [
            'user' => $user,
            'posts' => $posts,
            'isOwnProfile' => true,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount
        ]);
    }

    public static function showUserProfile(int $userId): string
    {
        $currentUserId = Auth::id();
        
        // Obtener datos del usuario a visualizar
        $user = UserRepository::find($userId);

        if (!$user) {
            header('Location: /');
            exit;
        }

        // Obtener posts del usuario
        $posts = PostRepository::getPostsByUser($userId);

        // Verificar si el usuario actual sigue al perfil mostrado
        $isFollowing = UserRepository::isFollowing($currentUserId, $userId);

        // Obtener métricas de seguimiento
        $followersCount = UserRepository::getFollowersCount($userId);
        $followingCount = UserRepository::getFollowingCount($userId);

        return self::view('profile', [
            'user' => $user,
            'posts' => $posts,
            'isOwnProfile' => $currentUserId === $userId,
            'isFollowing' => $isFollowing,
            'followersCount' => $followersCount,
            'followingCount' => $followingCount
        ]);
    }

    public static function follow(int $userToFollowId): void
    {
        try {
            $currentUserId = Auth::id();

            // Prevenir seguirse a uno mismo
            if ($currentUserId === $userToFollowId) {
                header('Location: /user/' . $userToFollowId);
                exit;
            }

            // Verificar que el usuario a seguir existe
            $userToFollow = UserRepository::find($userToFollowId);
            if (!$userToFollow) {
                header('Location: /');
                exit;
            }

            // Verificar si ya sigue al usuario
            if (UserRepository::isFollowing($currentUserId, $userToFollowId)) {
                header('Location: /user/' . $userToFollowId);
                exit;
            }

            // Intentar seguir
            UserRepository::follow($currentUserId, $userToFollowId);

            // Redirigir de vuelta al perfil del usuario
            header('Location: /user/' . $userToFollowId);
            exit;
        } catch (Exception $e) {
            header('Location: /user/' . $userToFollowId);
            exit;
        }
    }

    public static function unfollow(int $followedId): void
    {
        try {
            $currentUserId = Auth::id();

            // Verificar que no se intenta dejar de seguirse a uno mismo
            if ($currentUserId === $followedId) {
                header('Location: /user/' . $followedId);
                exit;
            }

            // Intentar dejar de seguir
            UserRepository::unfollow($currentUserId, $followedId);

            // Redirigir de vuelta al perfil del usuario
            header('Location: /user/' . $followedId);
            exit;
        } catch (Exception $e) {
            header('Location: /user/' . $followedId);
            exit;
        }
    }
}
