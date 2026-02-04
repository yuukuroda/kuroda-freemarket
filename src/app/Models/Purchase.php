<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->belongsTo(Item::class);
    }
    public function addresses()
    {
        return $this->belongsTo(Address::class);
    }
    public function payments()
    {
        return $this->belongsTo(Payment::class);
    }
}
