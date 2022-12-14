<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use App\Traits\GeneralTrait;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    use GeneralTrait;

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
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E001', 'Token is Invalid');
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E001', 'Token is Expired');
            }else{
                return $this->returnError('E001', 'Authorization Token not found');
            }
        }
        return $next($request);
    }
}