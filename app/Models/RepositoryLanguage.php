<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositoryLanguage extends Model
{
    use HasFactory;
    protected $table = 'repository_languages';
    protected $fillable = ['id', 'repository_id', 'name', 'value'];
}
