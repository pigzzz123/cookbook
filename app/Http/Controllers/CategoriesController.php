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

    public function show(Category $category)
    {
        $cookbooks = $category->books()->with(['foods' => function($query) {
            $query->take(5);
        }, 'foods.food'])->orderBy('created_at', 'desc')->paginate(16);

        return view('categories.show', compact('category', 'cookbooks'));
    }
}
