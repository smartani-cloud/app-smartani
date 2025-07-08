<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = "tm_notification";
    protected $fillable = [
    	'user_id',
    	'desc',
    	'link',
    	'is_active',
        'notification_category_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\LoginUser','user_id');
    }
	
	public function kategori()
    {
        return $this->belongsTo('App\Models\KategoriNotifikasi','notification_category_id');
    }
}
