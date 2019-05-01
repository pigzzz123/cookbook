<?php

namespace App\Http\Controllers;

use App\Models\CookBook;
use App\Models\Food;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function root(Request $request)
    {
        $query = $request->input('query', '');

        $cookbooks = CookBook::with(['foods' => function($query) {
            $query->take(5);
        }, 'foods.food'])->orderBy('created_at', 'desc')->paginate(15);
        if ($query) {
            $cookbooks = CookBook::search($query)->paginate(15);
        }

        $foods = Food::query()->orderBy('created_at', 'desc')->take(10)->get();

        return view('pages.root', compact('cookbooks', 'foods', 'query'));
    }
}
