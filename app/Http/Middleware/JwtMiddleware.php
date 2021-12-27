<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */

    public function handle($request, Closure $next)
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();           

        } 
        catch (Exception $e) {

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

                return response()->json([
                    'success' => false,
                    'message' => 'Token invalido!'
                ],401);
            }
            elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Token expirado!'
                ],400);
            }
            else {

                return response()->json([
                    'success' => false,
                    'message' => 'Token de autorização não encontrado!'
                ],401);
            }
        }

        
        return $next($request);
    }
}
