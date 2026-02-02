<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UjiSistem extends Model
{
    use HasFactory;

    protected $table = 'uji_sistems';

    protected $fillable = [
        'user_id',
        'score_rasa',
        'score_harga',
        'score_pelayanan',
        'score_kebersihan',
        'score_keramahan',
        'score_average',
        'review',
        'sentiment',
        'confidence_score',
        'probabilities',
    ];

    protected $casts = [
        'probabilities' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
