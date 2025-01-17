<?php

namespace App\Features\Feed;

use Core\Controller;
use Core\Session;

class FeedController extends Controller {
    public static function showFeed(): string
    {
        return self::view('feed', [
            'username' => Session::get('user_name', '')
        ]);
    }
}