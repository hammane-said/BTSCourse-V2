<?php

namespace App\Http\Middleware;

use App\Helper\Helper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckAuthorisation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->header('Authorization');
        if(!Helper::IsNullOrEmptyString($auth)){
            $info = explode(' ',$auth);
            if(count($info) === 2){
                $email = $info[0];
                $password = md5($info[1]);
                $res = User::where([['email',$email],['password',$password]])->first();
                if(!is_null($res)) return $next($request);
            }
        }
        return abort(401);
    }
}
