<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookBookFood extends Model
{
    protected $fillable = [
        'book_id', 'food_id', 'number'
    ];

    public function book()
    {
        return $this->belongsTo(CookBook::class, 'book_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }
}
