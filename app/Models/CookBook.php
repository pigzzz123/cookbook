<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class CookBook extends Model
{
    use Searchable;

    protected $fillable = [
        'name', 'cover', 'description', 'tips', 'category_id'
    ];

    public function toSearchableArray()
    {
        return $this->only('id', 'name', 'description', 'tips');
    }

    public function foods()
    {
        return $this->hasMany(CookBookFood::class, 'book_id');
    }

    public function steps()
    {
        return $this->hasMany(CookBookStep::class, 'book_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getCoverAttribute($value)
    {
        return storage_url($value, 'admin');
    }
}
