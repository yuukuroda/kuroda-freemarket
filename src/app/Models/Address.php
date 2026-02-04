<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'post_code',
        'address',
        'building'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }
}
