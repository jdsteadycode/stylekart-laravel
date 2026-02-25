<?php

// get User Model class path
use App\Models\User;

// check if function doesn't exist
if (! function_exists('getDashboardRoute')) {

    function getDashboardRoute(User $user)
    {

        switch ($user->role) {

            case 'admin':
                return 'dashboard.admin';

            case 'vendor':
                return 'dashboard.vendor';

            case 'customer':
                return 'customer.profile';

            default:
                return 'welcome';
        }
    }
}
