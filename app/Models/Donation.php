<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'status',
        'razon_social',
        'persona_contacto',
        'celular_contacto',
        'email_contacto',
        'requested_date',
        'approved_date',
        'additional_info',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}