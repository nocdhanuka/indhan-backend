<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Dccompanywise extends Model
{
    public $timestamps  = false;
    protected $table    = 'dccompanywise';
    protected $fillable = [
                            'ecnnumber',
                            'ecnname',
                            'state_id',
                            'district_id',
                            'class_id',
                            'formulation_id',
                            'volume',
                            'value',
                            'adama',
                            'atul',
                            'bayer',
                            'basf',
                            'bharat_certis',
                            'bio_stadt',
                            'cheminova',
                            'coromandal_inter_ltd',
                            'coromandel_agri_co',
                            'crystal',
                            'corteva',
                            'devidayal',
                            'dhanuka_agritech',
                            'excel_crop_care',
                            'fil',
                            'fmc',
                            'gharda_chemicals',
                            'godrej_agrovet_ltd',
                            'gsp',
                            'hindustan_insecticides_ltd',
                            'hindustan_pulverising',
                            'hpm',
                            'heranba',
                            'indofil',
                            'indo_gulf',
                            'insecticides_india_ltd',
                            'ichiban',
                            'ipl',
                            'ju_agri_science',
                            'jubilant',
                            'krishi_rasayan',
                            'meghmani',
                            'nagarjuna_agri_chem',
                            'nichino',
                            'multiplex',
                            'mahindra',
                            'pi_industries',
                            'parijat',
                            'punjab_chemicals',
                            'rallis',
                            'safex',
                            'shivalik_crop',
                            'sudarshan_chemical',
                            'sulphur_mills',
                            'sumitomo',
                            'swal_corporation',
                            'syngenta',
                            'upl_ltd',
                            'willowood_crop_sciences',
                            'zuari_agro_chemicals',
                            'others',
                            'grand_total',
                            'check',
                            'status'
                        ];
}
