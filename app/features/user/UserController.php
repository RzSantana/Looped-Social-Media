<?php

namespace App\Features\User;

use Core\Auth\Auth;
use Core\Controller;
use App\Features\Post\PostRepository;
use Core\Database\DataBase;
use Core\Session;
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

    public static function showProfileEdit(): string
    {
        $userId = Auth::id();

        // Obtener datos del usuario actual
        $user = UserRepository::find($userId);

        // Obtener posts del usuario
        $posts = PostRepository::getPostsByUser($userId);

        return self::view('edit', [
            'user' => $user,
            'posts' => $posts,
            'oldInputs' => Session::get('old_input', []),
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
            'followingCount' => $followingCount,
            'success' => Session::get('success'),
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

    public static function editProfile(): string
    {
        $userId = Auth::id();

        // Validamos y recolectamos los datos del formulario
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $selectedPosts = $_POST['selected_posts'] ?? [];

        // Validación básica de los datos
        if (empty($username)) {
            $errors['username'] = 'El nombre de usuario es requerido';
        } elseif (strlen($username) < 3 || strlen($username) > 20) {
            $errors['username'] = 'El nombre debe tener entre 3 y 20 caracteres';
        }

        if (empty($email)) {
            $errors['email'] = 'El correo electrónico es requerido';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El correo electrónico no es válido';
        }

        // Verificamos si el nombre de usuario o email ya existen (excluyendo al usuario actual)
        $existingUser = UserRepository::findByUsername($username);
        if ($existingUser && $existingUser['id'] !== $userId) {
            $errors['username'] = 'Este nombre de usuario ya está en uso';
        }

        $existingEmail = UserRepository::findByEmail($email);
        if ($existingEmail && $existingEmail['id'] !== $userId) {
            $errors['email'] = 'Este correo electrónico ya está registrado';
        }

        // Si hay errores, volvemos al formulario
        if (isset($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old_input', [
                'username' => $username,
                'email' => $email,
                // 'description' => $description
            ]);
            header('Location: /profile/edit');
            exit;
        }

        // Iniciamos una transacción para asegurar la integridad de los datos
        DataBase::beginTransaction();

        try {
            // Actualizamos el perfil del usuario
            UserRepository::update($userId, [
                'user' => $username,
                'email' => $email,
                // 'description' => $description
            ]);

            // Si hay posts seleccionados, los procesamos
            if (!empty($selectedPosts)) {
                // Verificamos que los posts pertenezcan al usuario
                foreach ($selectedPosts as $postId) {
                    $post = PostRepository::find($postId);
                    if ($post && $post['user_id'] === $userId) {
                        PostRepository::delete($postId);
                    }
                }
            }

            Database::commit();
            Session::flash('success', 'Perfil actualizado correctamente');
            header('Location: /profile');
            exit;
        } catch (\Exception $e) {
            Database::rollback();
            throw $e;
        }

        return 'editanto...';
    }
}
