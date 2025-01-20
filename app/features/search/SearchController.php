<?php

namespace App\Features\Search;

use App\Features\Post\PostRepository;
use App\Features\User\UserRepository;
use Core\Controller;

class SearchController extends Controller {
    public static function showSearch(): string
    {
        $search = $_GET['user'] ?? '';

        if ($search) {
            // Si hay un término de búsqueda, buscamos usuarios
            $users = UserRepository::searchByUsername($search);
            $data = ['users' => $users];
        } else {
            // Si no hay búsqueda, mostramos posts relevantes
            $posts = PostRepository::getRelevantPosts(20);
            $data = ['posts' => $posts];
        }

        return self::view('search', $data);
    }
}