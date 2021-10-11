<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
//use App\Models\Module;

class ModulesController extends Controller
{
    public function getUnusedModulesDetailsByEstablishment($establishmentId){
        return DB::table('modulesdetail')->where([['establishment_id',$establishmentId],['user_id',0]])->get();
    }
    public function getModulesByBranch($branchId){
        return DB::select("SELECT id,module,concat('S ',semester) semester  FROM modulesview WHERE branch_id = ".$branchId);
    }
}
