<?php

// folder path
namespace App\Http\Middleware;

use Closure;

// Request class path.
use Illuminate\Http\Request;

// App, Session facade class path.
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLanguage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // log the action
        logger()->info("[app\Http\Middleware\SetLanguage@handle] language set-up initiated");

        // check if current customer session has stored lang (locale)
        if (Session::has('locale')) {

            // Tell Laravel's App engine to use this language for the current request
            // i.e., update the APP_LOCALE from current session.
            App::setLocale(Session::get('locale'));
        }

        // go for next middleware or controller function.
        return $next($request);
    }
}
