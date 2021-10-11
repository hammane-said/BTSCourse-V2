<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\LineTeaching;
use App\Models\User;
use App\Models\Establishment;
use App\Models\Branch;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index(Request $request){
        $auth = $request->header('Authorization');
        $info = explode(' ',$auth);
        $email = $info[0];
        $password = md5($info[1]);
        $res = User::where([['email',$email],['password',$password]])->first();
        
        if($res->role == 'admin') return DB::select("SELECT * FROM `users`");
        return DB::select("SELECT * FROM `users` WHERE activated = 1");
    }
    public function login(\Illuminate\Http\Request $request){
        $auth = $request->header('Authorization');
        if(!Helper::IsNullOrEmptyString($auth)){
            $info = explode(' ',$auth);
            if(count($info) === 2){
                $email = $info[0];
                $password = md5($info[1]);
                $res = User::where([['email',$email],['password',$password]])->first();
                
                if(!is_null($res)) {
                    $res->password = $info[1];
                    return $res;
                }
            }
        }
        return abort(401);
        //return DB::table('usersView')->where('email',$email)->where('password',$password)->get();
    }
    public function register(\Illuminate\Http\Request $request){
        $data = $request->all();
        $model = new User();
        foreach($data as $k=>$v){
            if($k == "password") $model[$k] = md5($v);
            else if($k != "modules") $model[$k] = $v;
        }
        $model->save();
        if($model->type == "professor"){
            foreach($data["modules"] as $k){
                $resT = LineTeaching::where([['user_id',$model->id],['lineModule_id',$k],['establishment_id',$model->establishment_id]])->first();
                if(is_null($resT)){
                    $lt = new LineTeaching();
                    $lt->user_id = $model->id;
                    $lt->lineModule_id = $k;
                    $lt->establishment_id = $model->establishment_id;
                    $lt->save();
                }
            }
        }
        return response()->json(array('success' => true, 'last_insert_id' => $model->id), 200);
    }
    public function checkEmail($email){
        $res = User::where('email',$email)->first();
        return $res;
    }
    public function uncheckedUsers(){
        $res = User::where('activated',0)->get();
        return $res;
    }
    public function structure(){
        $establishments = DB::select("SELECT id,name text,'fas fa-university' icon FROM establishments");
        foreach($establishments as $k=>$v){
            $branchs = DB::select("SELECT b.id,name text,'fas fa-code-branch' icon FROM branches b, lineBranchs lb WHERE b.id = lb.branch_id AND lb.establishment_id = ".$v->id);
            foreach($branchs as $k1=>$v1){
                $semesters = DB::select("SELECT id, concat('Semestre',semester) text,'fas fa-graduation-cap' icon FROM semesters");
                foreach($semesters as $k2=>$v2){
                    $unities = DB::select("SELECT distinct unity_id id,unity text,'fab fa-unity' icon FROM modulesView WHERE semester_id = ".$v2->id." AND branch_id = ".$v1->id." ORDER BY unity_id");
                    foreach($unities as $k3=>$v3){
                        $modules = DB::select("SELECT id,module text,'fab fa-leanpub' icon  FROM modulesView WHERE semester_id = ".$v2->id." AND unity_id = ".$v3->id." AND branch_id = ".$v1->id." order by module_id");
                        $v3->nodes = $modules;
                    }
                    $v2->nodes = $unities;
                }
                $v1->nodes = $semesters;
            }
            $v->nodes = $branchs;
        }
        return $establishments;


        /*$establishments = Establishment::All();
        foreach($establishments as $k=>$v){
            $branchs = DB::select("SELECT b.* FROM branchs b, lineBranchs lb WHERE b.id = lb.branch_id AND lb.establishment_id = ".$v->id);
            foreach($branchs as $k1=>$v1){
                $semesters = DB::select("SELECT * FROM semesters");
                foreach($semesters as $k2=>$v2){
                    $unities = DB::select('SELECT distinct unity_id id,unity FROM modulesView WHERE semester_id = '.$v2->id.' AND branch_id = '.$v1->id.' ORDER BY unity_id');
                    foreach($unities as $k3=>$v3){
                        $modules = DB::select('SELECT * FROM modulesView WHERE semester_id = '.$v2->id.' AND unity_id = '.$v3->id.' AND branch_id = '.$v1->id.' order by module_id');
                        $v3->modules = $modules;
                    }
                    $v2->unities = $unities;
                }
                $v1->semesters = $semesters;
            }
            $v->branchs = $branchs;
        }
        return $establishments;*/
    }

    public function toggleConfirmation($id,Request $request){
        $auth = $request->header('Authorization');
        $info = explode(' ',$auth);
        $email = $info[0];
        $password = md5($info[1]);
        $res = User::where([['email',$email],['password',$password]])->first();
        $user = User::find($id);
        if($res->role == 'admin') {
            if($user->activated == 1) $user->activated = 0;
            else $user->activated = 1;
            $user->save();
        }
        return $user;
    }
    public function delete($id,Request $request){
        $auth = $request->header('Authorization');
        $info = explode(' ',$auth);
        $email = $info[0];
        $password = md5($info[1]);
        $res = User::where([['email',$email],['password',$password]])->first();
        if($res->role == 'admin') User::destroy($id);
    }
}
