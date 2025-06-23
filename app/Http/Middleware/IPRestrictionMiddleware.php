<?php

namespace App\Http\Middleware;

use Closure;

class IPRestrictionMiddleware
{
    public function handle($request, Closure $next)
    {

        $allowedIPs = ['10.132.0.3','35.195.114.254','51.15.241.59', '35.233.218.214', '79.11.49.24', '87.31.11.4', '31.222.229.18','79.1.180.184','79.11.132.107','54.39.83.156','93.117.122.138']; // Add your allowed IPs here
        $clientIP = $request->ip();

        // Check if the URL contains /admin
        if (strpos($request->getRequestUri(), '/admin') !== false) {
            if (in_array($clientIP, $allowedIPs)) {
                return $next($request);
            } else {
                abort(403, 'Access Forbidden');
            }
        }

        // If the URL does not contain /admin, simply pass the request through
        return $next($request);
    }
}
