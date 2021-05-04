<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessToken extends Model
{
    use Uuids;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['owner', 'platform', 'access_token'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'owner', 'id');
    }

    public function platform()
    {
        return $this->belongsTo('App\Models\Platform', 'platform', 'id');
    }
}
