<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_number',
        'month',
        'amount',
        'status',
        'description',
        'photo',
        'admin_note',
        'admin_photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
