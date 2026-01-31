<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSurvey extends Model
{
    protected $table = 'user_surveys';

    protected $guarded = ['id'];
}
