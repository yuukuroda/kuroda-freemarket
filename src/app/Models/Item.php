<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'image',
        'condition',
        'name',
        'brand',
        'description',
        'price'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }

    public function good()
    {
        return $this->hasMany(Good::class);
    }
    public function isGoodByAuthUser()
    {
        return $this->good()->where('user_id', auth()->id())->exists();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
