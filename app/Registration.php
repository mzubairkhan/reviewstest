<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable=[
            'username',
            'first_name',
            'last_name',
            'email',
            'password',
            'gender',
            'age',
            'address',
            'created'
        ];
}
