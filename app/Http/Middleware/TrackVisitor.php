<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Carbon\Carbon;

class TrackVisitor
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $endTime = microtime(true);

        $duration = round($endTime - $startTime); // Tính thời gian xử lý request (tính bằng giây)
      
        $pagesVisited = 1; 

        Visitor::create([
            'ip_address' => $request->ip(),
            'visited_at' => Carbon::now(),
            'duration' => $duration,
            'pages_visited' => $pagesVisited,
        ]);

        return $response;
    }
}