<?php
/**
 * Created by PhpStorm.
 * User: rdubrovin
 * Date: 2018-11-28
 * Time: 14:46
 */

namespace App\Http\Middleware;

use Closure;

class AddCorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->header('Access-Control-Allow-Headers', 'Authorization,content-type');
        $response->header('Access-Control-Expose-Headers', 'Authorization,content-type');
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Methods', '*');

        return $response;
    }
}
