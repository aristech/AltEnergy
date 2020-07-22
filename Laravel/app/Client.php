<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Manager;
use App\Mark;
use App\Offer;
use App\Project;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = ['lastname', 'firstname', 'afm', 'doy', 'arithmos_gnostopoihshs', 'arithmos_meletis', 'arithmos_hkasp', 'arithmos_aitisis', 'plithos_diamerismaton', 'dieuthinsi_paroxis', 'kw_oikiako', 'kw', 'levitas', 'foldername', 'address', 'telephone', 'telephone2', 'mobile', 'email', 'manager_id', 'zipcode', 'level', 'location'];

    protected $hidden = ['created_at', 'updated_at'];

    public function manager()
    {
        return $this->belongsTo(Manager::class, 'manager_id');
    }

    public function marks()
    {
        return $this->belongsToMany(Mark::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
