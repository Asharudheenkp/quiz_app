<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Pest\Mutate\Mutators\Visibility\FunctionPublicToProtected;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home');
    }

    public function quiz($category)
    {
        return Inertia::render('Quiz', ['category' => $category]);
    }
}
