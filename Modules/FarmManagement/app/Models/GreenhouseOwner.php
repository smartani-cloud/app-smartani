<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\GreenhouseOwnerFactory;

use App\Helpers\PhoneHelper;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GreenhouseOwner extends Model
{
    use HasFactory;

    protected $table = "tm_greenhouse_owners";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'nickname',
        'photo',
        'nik',
        'npwp',
        'gender_id',
        'birth_place',
        'birth_date',
        'address',
        'rt',
        'rw',
        'region_id',
        'phone_number',
        'email',
        'unit_id',
        'active_status_id',
    ];

    // protected static function newFactory(): GreenhouseOwnerFactory
    // {
    //     // return GreenhouseOwnerFactory::new();
    // }

    public function userProfiles()
    {
        return $this->morphMany('App\Models\UserProfile', 'profilable');
    }

    public function units()
    {
        return $this->belongsToMany('App\Models\Unit','tas_greenhouse_owner_units','owner_id', 'unit_id')->withTimestamps();
    }

    public function gender()
    {
        return $this->belongsTo('Modules\Core\Models\References\Gender','gender_id');
    }

    public function region()
    {
        return $this->belongsTo('Modules\Core\Models\References\Region','region_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function activeStatus()
    {
        
        return $this->belongsTo('Modules\Core\Models\References\Status','active_status_id');
    }

    public function getTitleNameAttribute()
    {
        return ($this->gender_id == 1 ? 'Bapak ' : 'Ibu ').$this->name;
    }

    public function getBirthDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->birth_date)->isoFormat('D MMMM Y');
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->birth_date)->age . ' tahun';
    }

    public function getAgeOriginalAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getPhotoPathAttribute()
    {
        if($this->photo) return 'storage/img/photo/owner/'.$this->photo;
        else return null;
    }

    public function getShowPhotoAttribute()
    {
        return $this->photoPath && Storage::disk('public')->exists('img/photo/owner/'.$this->photo) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getRegionCodeAttribute()
    {
        return $this->region->code;
    }

    public function getPhoneNumberIdAttribute()
    {
        $phone_number = $this->phone_number[0] == '0' ? '+62'.substr($this->phone_number, 1) : $this->phone_number;
        return $phone_number;
    }
    
    public function getPhoneNumberWithDashIdAttribute()
    {
        return PhoneHelper::addDashesId((string)$this->phone_number,'mobile','0');
    }
    
    public function scopeActive($query)
    {
        return $query->where('active_status_id',5);
    }
}
