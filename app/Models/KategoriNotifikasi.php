<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriNotifikasi extends Model
{
    use HasFactory;

    protected $table = "tref_notification_category";
    protected $fillable = ['background','icon','name'];

    public function notif()
    {
        return $this->hasMany('App\Models\Notifikasi','notification_category_id');
    }
}
