<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'status',
        'resolution_notes',
        'evidence_path',
        'completed_at',
    ];

    /**
     * Un Ticket pertenece a un Usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}