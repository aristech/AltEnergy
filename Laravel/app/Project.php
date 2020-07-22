<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = ['title_id',    'status',     'client_id',     'marks',     'techs',     'appointment_start',  'appointment_end',  'cost', 'manager_payment', 'aitisi_eda',     'aitisi_paroxou',     'upografi_aitisis',     'parallagi_sxedion',     'rantevou_xaraksis_metriti',     'topothetisi_metriti',     'katathesi_meletis',     'egkrisi_meletis',     'katathesi_pistopoihtikon',     'udrauliki_egkatastasi',     'kleisimo_grammis_aeriou',     'dokimi_steganotitas',     'rantevou_elegxou',     'rantevou_epanelegxou',     'enausi',     'ekdosi_fullou_kausis',     'timologio'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
