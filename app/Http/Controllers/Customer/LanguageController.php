<?php

// folder path
namespace App\Http\Controllers\Customer;

// Controller class path
use App\Http\Controllers\Controller;

// Seesion Facade class path
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language and save to session.
     */
    public function switchLang($locale)
    {
        // log the action
        logger()->info("[app\Http\Controllers\Customer\LanguageController@switchLang] Switching language initiated.");

        // Define the languages we currently support
        $availableLocales = ['en', 'gu'];

        // check if incoming lang (locale) is from available ones?
        if (in_array($locale, $availableLocales)) {

            // Save the choice for current customer time-period
            Session::put('locale', $locale);

            // Log for debugging
            logger()->info("[app\Http\Controllers\Customer\LanguageController@switchLang] User switched language to: " . $locale);
        }

        // redirect back customer with changes applied
        return redirect()->back();
    }
}
