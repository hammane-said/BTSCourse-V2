<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Models\User;

class FilesController extends Controller
{
    public function index(Request $request){
        $auth = $request->header('Authorization');
        $info = explode(' ',$auth);
        $email = $info[0];
        $password = md5($info[1]);
        $res = User::where([['email',$email],['password',$password]])->first();
        if($res->role == 'admin') return DB::select("SELECT * FROM `filesview`");
        return DB::select("SELECT * FROM `filesview` WHERE confirmed = 1");
    }
    public function upload(Request $request){
        $res = $request->all();
        $data = json_decode($res["data"],true);
        foreach($res as $k=>$v){
            if($k != 'data'){
                $file = $request->file($k);
                $filename = $file->getClientOriginalName();
                $f = new File();
                $f->path = $filename;
                $f->lineModule_id = $data["lineModule_id"];
                $f->user_id = $data["user_id"];
                $f->save();
                $file->storeAs('files', $f->id.'.'.$file->getClientOriginalExtension());
            }
        }
        return response()->json([
            'type' => 'success',
            'message' => 'uploaded successfully',
        ]);
    }
    public function download($id){
        File::where('id',$id)->first();
        $res = File::where('id',$id)->first();
        if(!is_null($res)) {
            $ext = pathinfo($res["path"], PATHINFO_EXTENSION);
            return Storage::download('files/'.$id.'.'.$ext);
        }
    }
    public function chart($size=20){
        return DB::select("SELECT count(id) Number, DATE(uploadedAt) Date FROM `files` group by DATE(uploadedAt) order by date");
    }
    public function toggleConfirmation($id,Request $request){
        $auth = $request->header('Authorization');
        $info = explode(' ',$auth);
        $email = $info[0];
        $password = md5($info[1]);
        $res = User::where([['email',$email],['password',$password]])->first();
        $file = File::find($id);
        if($res->role == 'admin') {
            if($file->confirmed == 1) $file->confirmed = 0;
            else $file->confirmed = 1;
            $file->save();
        }
        return $file;
    }
    public function delete($id,Request $request){
        $auth = $request->header('Authorization');
        $info = explode(' ',$auth);
        $email = $info[0];
        $password = md5($info[1]);
        $res = User::where([['email',$email],['password',$password]])->first();
        if($res->role == 'admin') File::destroy($id);
    }
}
