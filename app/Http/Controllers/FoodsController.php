<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodsController extends Controller
{
    public function index()
    {
        $foods = Food::query()->orderBy('created_at', 'desc')->paginate(40);

        return view('foods.index', compact('foods'));
    }
}
