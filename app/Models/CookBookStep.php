<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CookBookStep extends Model
{
    protected $fillable = [
        'book_id', 'cover', 'content', 'order'
    ];

    public function book()
    {
        return $this->belongsTo(CookBook::class, 'book_id');
    }
}
