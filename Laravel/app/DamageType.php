<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Damage;

class DamageType extends Model
{
    protected $table = 'damage_types';

    protected $fillable = ['name'];

    protected $hidden = ["created_at", "updated_at"];

    public function damages()
    {
        return $this->hasMany(Damage::class);
    }
}
