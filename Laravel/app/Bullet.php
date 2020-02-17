<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Offer;

class Bullet extends Model
{
    protected $table = 'bullets';

    protected $fillable = ['description', 'mark_id'];

    public function offers()
    {
        return $this->belongsToMany(Offer::class);
    }
}
