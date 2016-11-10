<?php

namespace App\Domains\Contacts;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'age',
    ];
}
