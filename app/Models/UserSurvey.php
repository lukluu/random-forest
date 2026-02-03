<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSurvey extends Model
{
    protected $table = 'user_surveys';

    // Guarded id agar semua kolom lain bisa diisi via create()
    protected $guarded = ['id'];

    // Casting tipe data agar output JSON/Array lebih rapi (Opsional tapi disarankan)
    protected $casts = [
        'score_average' => 'float',
        'created_at' => 'datetime',
    ];
}
