<?php

namespace App\Services;

use App\Libraries\HashPassword as LibrariesHashPassword;
use App\Models\UserModel;
use Config\Services;
use HashPassword;
use Google\Client;
use Google\Service\Oauth2;
class AuthService
{
    protected UserModel $userModel;
    protected $validation;
    protected $request;

    public function __construct()
    {
        $this->validation = Services::validation();
        $this->request = Services::request();
        $this->userModel = new UserModel();
    }

    public function register(array $data): array
    {
        $token = bin2hex(random_bytes(32));
        if (!$this->validation->setRules($this->registerRules())->run($data)) {
            return [
                'status' => false,
                'errors' => $this->validation->getErrors()
            ];
        }

        $userId = $this->userModel->insert([
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'username' => trim($data['username']),
            'email' => strtolower(trim($data['email'])),
            'email_verification_token' => $token,
            'password' => LibrariesHashPassword::make(trim($data['password'])),
            'status' => 1,
            'failed_attempts' => 0,
            'locked_until' => null,
        ]);
        if (!$userId) {
            return [
                'status' => false,
                'errors' => ['Failed to create account']
            ];
        }
        $this->sendVerification(
            $data['email'],
            $data['first_name'],
            $token
        );
        return [
            'status' => true
        ];
    }


    public function login(array $data): array
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validation->setRules($rules)->run($data)) {
            return [
                'status' => false,
                'message' => implode(
                    '<br>',
                    $this->validation->getErrors()
                )
            ];
        }

        $email = strtolower(trim($data['email']));
        // $password = $data['password'];

        if ($this->isLocked($email)) {
            return [
                'status' => false,
                'message' => 'Account locked. Try again later.'
            ];
        }

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (
            !$user ||
            !LibrariesHashPassword::check($data['password'], $user['password'])
        ) {
            $this->loginFailedAttempt(
                $email,
                $this->request->getIPAddress()
            );

            return [
                'status' => false,
                'message' => 'Invalid credentials.'
            ];
        }

        if (empty($user['email_verified_at'])) {
            return [
                'status' => false,
                'message' => 'Please verify your email before logging in.'
            ];
        }

        if ((int) $user['status'] !== 1) {
            return [
                'status' => false,
                'message' => 'Account disabled.'
            ];
        }



        $this->resetAttempts($email);

        session()->regenerate(true);

        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            // 'role_id' => $user['role_id'] ?? null,
            'logged_in' => true,
        ]);

        $remember = $data['remember'] ?? false;

        if ($remember) {
            $token = bin2hex(random_bytes(32));

            helper('cookie');

            set_cookie([
                'name' => 'remember_token',
                'value' => $token,
                'expire' => 60 * 60 * 24 * 30, // 30 days
                'secure' => false,
                'httponly' => true
            ]);

            $this->userModel->update($user['id'], [
                'remember_token' => $token
            ]);
        }

        return [
            'status' => true
        ];
    }

    public function loginWithOAuth(string $provider, string $code, string $state, ?string $sessionState): array
    {
        return match ($provider) {
            'google' => $this->googleCallback($code, $state, $sessionState),
            default => [
                'status' => false,
                'message' => 'Unsupported provider '
            ],
        };
    }

    private function googleCallback(string $code, string $state, ?string $sessionState): array
    {
        if (!$sessionState || $state !== $sessionState) {
            return [
                'status' => false,
                'message' => 'Invalid OAuth state',
            ];

        }
        session()->remove('google_oauth_state');

        $client = $this->createGoogleClient();

        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            log_message('error', 'Google OAuth Error: ' . json_encode($token));

            return [
                'status' => false,
                'message' => 'Google authentication failed'
            ];
        }

        $client->setAccessToken($token['access_token']);

        // User Info
        $googleService = new Oauth2($client);
        $googleUser = $googleService->userinfo->get();


        if (empty($googleUser->email)) {
            return [
                'status' => false,
                'message' => 'Google account has no email'
            ];
        }
        // User Info

        $email = strtolower($googleUser->email);

        $user = $this->findOrCreateGoogleUser($googleUser, $email);
        if ((int) $user['status'] !== 1) {
            return [
                'status' => false,
                'message' => 'Account disabled'
            ];
        }
        $this->setUserSession($user, 'google');
        return [
            'status' => true
        ];
    }

    private function createGoogleClient(): client
    {
        $client = new Client();

        $client->setClientId(getenv('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI'));
        return $client;
    }

    private function findOrCreateGoogleUser($googleUser, string $email): array
    {
        $user = $this->userModel->where('email', $email)->first();

        if ($user) {
            return $user;
        }

        $userId = $this->userModel->insert([
            'first_name' => $googleUser->givenName ?? '',
            'last_name' => $googleUser->familyName ?? '',
            'username' => $this->generateUsername($email),
            'email' => $email,
            'password' => null,
            'status' => 1,

            'provider' => 'google',
            'provider_id' => $googleUser->id ?? null,

            'email_verified_at' => date('Y-m-d H:i:s'),
            'avatar' => $googleUser->picture ?? null,
        ]);

        return $this->userModel->find($userId);
    }

    private function setUserSession(array $user, string $provider): void
    {
        session()->regenerate(true);

        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'logged_in' => true,
            'auth_provider' => $provider,
        ]);
    }

    private function generateUsername(string $email): string
    {
        $base = explode('@', $email)[0];

        $username = $base;
        $i = 1;

        while ($this->userModel->where('username', $username)->first()) {
            $username = $base . $i++;
        }

        return $username;
    }
    public function sendResetLink(string $email): array
    {
        $emailService = Services::email();
        $user = $this->userModel->where('email', $email)->first();

        if (!$user || empty($user['id'])) {
            return ['status' => true];
        }

        $token = bin2hex(random_bytes(32));

        $this->userModel->update($user['id'], [
            'reset_token' => $token,
            'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+1 hour'))
        ]);

        $link = site_url('reset-password/' . $token . '?=' . time());


        log_message('error', 'RESET LINK: ' . $link);

        $emailService->setTo($email);
        $emailService->setSubject('Reset Your Password');
        $emailService->setMailType('html');

        $emailService->setMessage(view('emails/reset_password', [
            'name' => $user['first_name'] . ' ' . $user['last_name'],
            'link' => $link,
        ]));

        if (!$emailService->send()) {
            log_message('error', $emailService->printDebugger(['headers', 'subject', 'body']));

            return [
                'status' => false,
                'message' => 'Email failed to send'
            ];
        }
        return ['status' => true];
    }

    public function updatePassword(string $token, string $password): array
    {
        $user = $this->userModel
            ->where('reset_token', $token)
            ->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Invalid token'
            ];
        }

        if (
            empty($user['reset_token_expires']) ||
            strtotime($user['reset_token_expires']) < time()
        ) {
            return [
                'status' => false,
                'message' => 'Token expired'
            ];
        }

        if (empty($password)) {
            return [
                'status' => false,
                'message' => 'Password cannot be empty'
            ];
        }

        $update = [
            'password' => LibrariesHashPassword::make($password),
            'reset_token' => null,
            'reset_token_expires' => null
        ];

        $this->userModel->update($user['id'], $update);

        return [
            'status' => true
        ];
    }

    private function isLocked(string $email): bool
    {
        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (!$user) {
            return false;
        }

        return !empty($user['locked_until'])
            && strtotime($user['locked_until']) > time();
    }

    private function loginFailedAttempt(
        string $email,
        string $ip
    ): void {
        $db = db_connect();

        $db->table('login_attempts')->insert([
            'email' => $email,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (!$user) {
            return;
        }

        $attempts = (int) $user['failed_attempts'] + 1;

        $update = [
            'failed_attempts' => $attempts
        ];

        if ($attempts >= 5) {
            $update['locked_until'] = date(
                'Y-m-d H:i:s',
                strtotime('+15 minutes')
            );
        }

        $this->userModel->update(
            $user['id'],
            $update
        );
    }
    public function verifyEmail(string $token): array
    {
        $user = $this->userModel->where('email_verification_token', $token)->first();

        if (!$user) {
            return [
                'status' => false,
                'message' => 'Invalid or expired verification link.'
            ];
        }
        $this->userModel->update($user['id'], [
            'email_verification_token' => null,
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);

        return [
            'status' => true,
            'message' => 'Email verified successfully.'
        ];
    }
    private function sendVerification(string $email, string $firstName, string $token): bool
    {
        $mailer = Services::email();

        $verificationUrl = site_url('verify-email/' . $token);

        $data = [
            'firstName' => $firstName,
            'email' => $email,
            'verificationUrl' => $verificationUrl
        ];

        $mailer->setTo($email);
        $mailer->setSubject('Verify Your Email Address');
        $mailer->setMailType('html');

        $mailer->setMessage(view('emails/verify_email', $data));

        if (!$mailer->send()) {
            log_message('error', $mailer->printDebugger(['headers']));
            return false;
        }

        return true;
    }

    private function resetAttempts(string $email): void
    {
        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (!$user) {
            return;
        }

        $this->userModel->update(
            $user['id'],
            [
                'failed_attempts' => 0,
                'locked_until' => null
            ]
        );
    }

    private function registerRules(): array
    {
        return [
            'first_name' => [
                'rules' => 'required|min_length[2]|max_length[50]'
            ],

            'last_name' => [
                'rules' => 'required|min_length[2]|max_length[50]'
            ],

            'username' => [
                'rules' => 'required|min_length[4]|max_length[20]|is_unique[users.username]'
            ],

            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]'
            ],

            'password' => [
                'rules' =>
                    'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/]'
            ],

            'confirm_password' => [
                'rules' => 'required|matches[password]'
            ]
        ];
    }
}