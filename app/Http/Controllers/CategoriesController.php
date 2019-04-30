<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->where('is_directory', true)->orderBy('created_at', 'desc')->get();

        return view('categories.index', compact('categories'));
    }
}
