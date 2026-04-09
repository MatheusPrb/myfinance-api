<?php

namespace App\Http\Middleware;

use App\Domain\Exceptions\ForbiddenNotAdminException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->is_admin) {
            throw new ForbiddenNotAdminException();
        }

        return $next($request);
    }
}
