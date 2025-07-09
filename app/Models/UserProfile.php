<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class UserProfile extends Model
{
    use HasFactory;

    protected $table = "tas_user_profiles";

    protected $fillable = [
        'user_id',
        'profilable_type',
        'profilable_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\LoginUser','user_id');
    }

    public function profilable()
    {
        return $this->morphTo();
    }
}
