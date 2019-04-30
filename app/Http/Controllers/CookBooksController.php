<?php

namespace App\Http\Controllers;

use App\Models\CookBook;
use Illuminate\Http\Request;

class CookBooksController extends Controller
{
    public function show(CookBook $cookbook)
    {
        return view('cookbooks.show', compact('cookbook'));
    }
}
