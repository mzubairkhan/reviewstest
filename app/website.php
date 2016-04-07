<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable=[
          'domain',
          'server_ip',
          'git_repo',
          'title',
          'meta_details',
          'theme',
          'website_type',
          'setting'.
          'created_on'
          ];
}