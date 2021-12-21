<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseBuilder;
use Closure;
use Illuminate\Http\Request;

class IsAdmin
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
        if (request()->user()->role->slug == 'admin') {
            return $next($request);
        }

        return ResponseBuilder::buildErrorResponse('Unauthorized', [], 401);
    }
}
