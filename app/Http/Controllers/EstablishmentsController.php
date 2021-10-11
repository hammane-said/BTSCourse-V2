<?php

namespace App\Http\Controllers;

use App\Models\Establishment;

class EstablishmentsController extends Controller
{
    public function getEstablishments(){
        $model = new Establishment();
        return $model::all();
    }
}
