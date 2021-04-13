<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessToken extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['owner', 'platform', 'access_token'];
}
