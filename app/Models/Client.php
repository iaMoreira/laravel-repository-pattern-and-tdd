<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Client extends BaseModel
{
    protected $fillable = ['name', 'document', 'email'];
}
