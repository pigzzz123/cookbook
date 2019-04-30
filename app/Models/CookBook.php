<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookBook extends Model
{
    protected $fillable = [
        'name', 'cover', 'description', 'tips'
    ];

    public function foods()
    {
        return $this->hasMany(CookBookFood::class, 'book_id');
    }

    public function steps()
    {
        return $this->hasMany(CookBookStep::class, 'book_id');
    }
}
