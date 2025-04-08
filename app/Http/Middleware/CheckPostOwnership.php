<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Post;

class CheckPostOwnership
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
        // Get the post from the route
        $post = Post::find($request->route('post'))->first();

        // If the post doesn't exist or doesn't belong to the authenticated user
        if (!$post || $post->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // If the check passes, proceed with the request
        return $next($request);
    }
}

