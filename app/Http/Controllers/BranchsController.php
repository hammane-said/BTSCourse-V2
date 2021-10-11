<?php

namespace App\Http\Controllers;

use App\Models\Branch;

class BranchsController extends Controller
{
    public function getBranches(){
        return Branch::All();
    }
    public function getBranchsByEstablishment($establishmentId){
        $model = new Branch();
        return $model->getBranchsByEstablishment($establishmentId);
    }
}
