<?php

namespace Wink\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class Authenticate
{
    protected $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->auth->guard('wink')->check()) {
            $this->auth->shouldUse('wink');
        } else {
            throw new AuthenticationException(
                'Unauthenticated.', ['wink'], route('wink.auth.login')
            );
        }

        return $next($request);
    }
}
