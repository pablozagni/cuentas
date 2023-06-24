<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{

    public function tieneHijos() {
        $hijos = Cuenta::where('parent_id','=',$this->id);
        return $hijos->count() > 0 ;
    }

    public function usadaEnConfig() {
        return false ;
    }

}
