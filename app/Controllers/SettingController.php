<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SettingController extends BaseController
{
    public function profile()
    {
        return view('settings/profile', [
            'title' => 'Profile',
            'pageTitle' => 'My Profile'
        ]);
    }
    public function setting()
    {
        return view('settings/setting', [
            'title' => 'Settings',
            'pageTitle' => 'My Account'
        ]);
    }
}
