<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function getJabatan($unit)
    {
        $jabatan = Unit::find($unit)->jabatan->where('status_id', 1)->pluck("name","code");
        return json_encode($jabatan);
    }
}
