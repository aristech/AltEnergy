<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Offer;

class OfferStatus extends Model
{
    protected $table = 'offer_statuses';

    protected $fillable = ['name'];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
