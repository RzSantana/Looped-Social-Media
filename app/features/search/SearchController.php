<?php

namespace App\Features\Search;

use Core\Controller;

class SearchController extends Controller {
    public static function showSearch(): string
    {
        return self::view('search');
    }
}