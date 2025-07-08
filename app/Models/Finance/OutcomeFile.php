<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeFile extends Model
{
    use HasFactory;

    protected $table = "outcome_files";
    protected $fillable = [
    	'outcome_id',
    	'file_name',
    	'file_extension',
        'file_size',
        'file_path',
        'user_id'
    ];

    public function outcome()
    {
        return $this->belongsTo('App\Models\Finance\Outcome','outcome_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function getNameExtensionAttribute()
    {
        return $this->file_name.'.'.$this->file_extension;
    }

    public function getFullFilePathAttribute()
    {
        return $this->file_path.$this->name_extension;
    }

    public function getFormatSizeUnitsAttribute()
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
