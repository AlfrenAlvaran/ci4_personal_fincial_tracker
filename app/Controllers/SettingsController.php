<?php

namespace App\Controllers;

use App\Services\AuthService;

class SettingsController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function setting()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId  = session()->get('user_id');
        $profile = $this->authService->getRawProfile($userId);

        if (!$profile) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        return view('settings/setting', [
            'title' => 'Settings',
            'user'  => $profile,
        ]);
    }

    public function changePassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $result = $this->authService->changePassword(
            $userId,
            $this->request->getPost('current_password'),
            $this->request->getPost('new_password'),
            $this->request->getPost('confirm_password')
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to('/settings')->with('success', 'Password updated successfully.');
    }

    public function setPassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $result = $this->authService->setPassword(
            $userId,
            $this->request->getPost('new_password'),
            $this->request->getPost('confirm_password')
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to('/settings')->with('success', 'Password set successfully.');
    }

    public function preferences()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $this->authService->updatePreferences($userId, $this->request->getPost());

        return redirect()->to('/settings')->with('success', 'Preferences saved.');
    }

    public function disconnectGoogle()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $result = $this->authService->disconnectGoogle($userId);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to('/settings')->with('success', 'Google account disconnected.');
    }

    public function deleteAccount()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        $result = $this->authService->deleteAccount(
            $userId,
            $this->request->getPost('password')
        );

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        session()->destroy();

        return redirect()->to('/login')->with('success', 'Your account has been deleted.');
    }
}