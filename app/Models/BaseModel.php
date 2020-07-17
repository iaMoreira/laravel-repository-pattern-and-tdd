<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class BaseModel extends Model
{
    public static function getRules($id){
        return [];
    }
}
