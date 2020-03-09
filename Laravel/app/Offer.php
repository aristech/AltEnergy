<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Bullet;
use App\OfferStatus;

class Offer extends Model
{
    protected $table = 'offers';
    protected $fillable = ['client_id', 'offer_filename', 'status_id', 'amount', 'number', 'offer_number'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bullets()
    {
        return $this->belongsToMany(Bullet::class);
    }

    public function status()
    {
        return $this->belongsTo(OfferStatus::class);
    }
}
