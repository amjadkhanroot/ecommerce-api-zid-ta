<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Role;
use Closure;
use ErrorException;
use Illuminate\Http\Request;

class SellerMiddleware
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Role::find($request->user()->role_id)->code == RoleEnum::get('seller')) {
                return $next($request);
            }
            return $this->apiResponse(false, 'unauthorized!', [], [], 401);

        } catch (ErrorException $exception) {
            return $this->apiResponse(false, 'unauthorized!', [], [],400);
        }
    }
}
