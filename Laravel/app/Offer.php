<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;
use App\Bullet;
use App\OfferStatus;
use App\OfferText;

class Offer extends Model
{
    protected $table = 'offers';
    protected $fillable = ['client_id', 'title_id', 'offer_filename', 'offer_text_id', 'status_id', 'amount', 'number', 'offer_number'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function bullets()
    {
        return $this->belongsToMany(Bullet::class)->withPivot('quantity');
    }

    public function text()
    {
        return $this->belongsToMany(OfferText::class);
    }

    public function status()
    {
        return $this->belongsTo(OfferStatus::class);
    }
}
