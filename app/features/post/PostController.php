<?php

namespace App\Features\Post;

use Core\Controller;

class PostController extends Controller
{
    public static function showPost(int $postId)
    {
        $post = PostRepository::getPost($postId);
        return self::view('post', ['post' => $post]);
    }
}
