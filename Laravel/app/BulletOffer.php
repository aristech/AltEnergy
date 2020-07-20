<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BulletOffer extends Model
{
    protected $table = 'bullet_offer';

    protected $fillable = ['bullet_id', 'offer_id', 'quantity'];
}
