<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;

class DecodeDriverJwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       try{
        $driver = auth('driver-api')->user();
        if (!$driver) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        $request->attributes->add([
            'auth_driver' => $driver
        ]);
        return $next($request);

       }
       catch(JWTException $e){
        return response()->json([
            'status' => false,
            'message' => 'Invalid or expired token'
        ], 401);

       }
    }
}
