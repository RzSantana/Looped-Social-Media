<?php

namespace App\Controllers;

use Core\Controller as CoreController;

class HomeController extends CoreController
{
    public static function index(): string
    {
        $data = [
            'title' => 'Looped'
        ];
        return self::view('home', $data);
    }
}
