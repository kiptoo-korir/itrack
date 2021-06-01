<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repository extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'fullname', 'owner', 'platform', 'repository_id', 'description', 'date_created_online', 'date_updated_online', 'date_pushed_online', 'issues_count'];
}
