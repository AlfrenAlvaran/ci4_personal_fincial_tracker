<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\AuthService;
use Google\Client;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {
        return view('auth/sign_in', [
            'title' => 'Sign In',
            'cardWidth' => '450px',
            'heroTitle' => 'Welcome Back',
            'heroDescription' => 'Sign in and continue tracking your financial goals.'
        ]);
    }

    public function register()
    {
        return view('auth/sign_up', [
            'title' => 'Register',
            'cardWidth' => '600px',
            'heroTitle' => 'Start Your Financial Journey',
            'heroDescription' => 'Create an account and take control of every peso you earn and spend.'
        ]);
    }

    public function store()
    {
        $result = $this->authService->register(
            $this->request->getPost()
        );

        if (!$result['status']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $result['errors']);
        }

        return redirect()
            ->to('/login')
            ->with('success', 'Account created successfully!');
    }

    public function authenticate()
    {
        $result = $this->authService->login(
            $this->request->getPost()
        );

        if (!$result['status']) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $result['message']);
        }

        return redirect()->to('/');
    }

    public function verify($token)
    {
        $result = $this->authService->verifyEmail($token);
        if (!$result['status']) {
            return redirect()->to('/login')->with('error', $result['message']);
        }
        return redirect()->to('/login')->with('success', $result['message']);
    }

    public function forgotPasswordForm()
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');

        $result = $this->authService->sendResetLink($email);
        if (!$result['status']) {
            return redirect()
                ->back()
                ->with('error', $result['message'] ?? 'Failed to send reset email.');
        }
        return redirect()->back()->with('success', 'If email exists, reset link sent.');
    }

    public function resetPasswordForm($token)
    {
        return view('auth/reset_password', ['token' => $token]);
    }

    public function updatePassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $result = $this->authService->updatePassword($token, $password);

        if (!$result['status']) {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->to('/login')->with('success', 'Password updated!');
    }

    // GOOGLE
    public function google()
    {
        $client = new Client();

        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));

        $client->addScope('email');
        $client->addScope('profile');

        $state = bin2hex(random_bytes(16));
        session()->set('google_oauth_state', $state);
        $client->setState($state);

        return redirect()->to($client->createAuthUrl());
    }

    public function googleCallback()
    {
        $result = $this->authService->loginWithOAuth(
            'google',
            $this->request->getGet('code'),
            $this->request->getGet('state'),
            session()->get('google_oauth_state')
        );

        if (!$result['status']) {
            return redirect()->to('login')
                ->with('error', $result['message']);
        }

        return redirect()->to('/');
    }
    // GOOGLE

    public function profile()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $profile = $this->authService->getProfile((int) session()->get('user_id'));
        if (!$profile) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'User not found.');
        }

        return view('auth/profile', [
            'title' => 'My Profile',
            'user' => $profile
        ]);
    }
    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}
