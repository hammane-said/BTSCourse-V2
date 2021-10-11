<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Branch extends Model
{
    use HasFactory;
    public function getBranchsByEstablishment($establishmentId){
        return DB::select("SELECT l.*,b.name branch,b.abbreviation,e.name establishment
        FROM establishments e,lineBranchs l, branchs b 
        WHERE e.id = l.establishment_id and b.id = l.branch_id and e.id = ".$establishmentId);
    }
}
