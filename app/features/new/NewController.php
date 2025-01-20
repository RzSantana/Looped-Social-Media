<?php

namespace App\Features\New;

use App\Features\Post\PostRepository;
use App\Features\User\UserRepository;
use Core\Auth\Auth;
use Core\Controller;
use Core\Session;

class NewController extends Controller {
    public static function showNew(): string
    {
        $userId = Auth::id();
        $user = UserRepository::find($userId);

        return self::view('new', [
            'user' => $user,
            'text' => Session::getFlash('text', ''),
            'errors' => Session::getFlash('errors', [])  
        ]);
    }

    public static function createPost(): void 
    {
        $userId = Auth::id();
        $text = trim($_POST['caption'] ?? '');
        
        // Validación básica
        if (empty($text)) {
            Session::flash('errors', ['text' => 'El texto es requerido']);
            Session::flash('text', $text);
            header('Location: /new');
            exit;
        }

        if (strlen($text) > 100) {
            Session::flash('errors', ['text' => 'El texto no puede exceder los 100 caracteres']);
            Session::flash('text', $text);
            header('Location: /new');
            exit;
        }

        try {
            $entry = PostRepository::createPost($userId, $text);

            if (!$entry) {
                Session::flash('errors', 'No se pudo crear la publicación');
                Session::flash('text', $text);
                header('Location: /new');
                exit;
            }

            // Redirigir al perfil después de crear la publicación
            header('Location: /profile');
            exit;
        } catch (\Exception $e) {
            Session::flash('error', 'Error al crear la publicación');
            Session::flash('text', $text);
            header('Location: /new');
            exit;
        }
    }
}