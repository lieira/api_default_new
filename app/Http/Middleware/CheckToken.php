<?php

namespace App\Http\Middleware;

use App\Token;
use Closure;

class CheckToken
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
        if (Token::find($request->token) == null) {

            return response()->json([
                'success' => false,
                'message' => 'Invalid Service Token'
            ]);
        }

        return $next($request);
    }
}
