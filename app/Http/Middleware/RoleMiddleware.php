<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware {
    public function handle($request, Closure $next, ...$roles){

        $flagNext = false;
        foreach($roles as $role){
            if($request->user()->hasRole($role)){
                $flagNext   = true;
                break;
            }
        }
        
        if($flagNext==true){
            return $next($request);
        } else {
            abort(404);
        }
        
    }
}
