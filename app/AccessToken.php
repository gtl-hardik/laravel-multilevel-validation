<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $table = 'oauth_access_tokens';

    public function users() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
