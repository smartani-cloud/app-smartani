<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = "tref_jobs";

    public $timestamps = false;
    
    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('is_active',0);
    }
}
