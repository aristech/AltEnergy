<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Manager;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = ['lastname', 'firstname', 'afm', 'doy','arithmos_gnostopoihshs' ,'arithmos_meletis' ,'arithmos_hkasp' ,'arithmos_aitisis' ,'address','telephone', 'telephone2', 'mobile', 'email', 'manager_id', 'zipcode', 'level' ,'location'];

    protected $hidden = ['created_at','updated_at'];

    public function manager()
    {
        return $this->belongsTo(Manager::class,'manager_id');
    }

}
