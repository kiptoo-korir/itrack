<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;
    protected $table = 'issues';
    protected $fillable = ['owner', 'repository', 'issue_no', 'state', 'title', 'body', 'date_created_online', 'date_updated_online', 'labels', 'date_closed_online', 'created_at', 'updated_at'];
}
