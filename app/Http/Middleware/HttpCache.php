<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->set('Cache-Control', 'public, max-age=0, must-revalidate');

            $etag = $response->headers->get('ETag');
            if ($etag && $request->header('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent(null);
            }
        }

        return $response;
    }
}
