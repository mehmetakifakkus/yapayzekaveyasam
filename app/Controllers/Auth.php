<?php

namespace App\Controllers;

use App\Models\UserModel;
use Config\Services;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
    }

    /**
     * Redirect to Google OAuth
     */
    public function google()
    {
        $clientId = getenv('google.clientId') ?: 'YOUR_GOOGLE_CLIENT_ID';
        $redirectUri = getenv('google.redirectUri') ?: base_url('auth/callback');

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ];

        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        return redirect()->to($authUrl);
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        $code = $this->request->getGet('code');

        if (!$code) {
            $this->session->setFlashdata('error', 'Google ile giriş yapılamadı.');
            return redirect()->to('/');
        }

        // Exchange code for access token
        $clientId = getenv('google.clientId') ?: 'YOUR_GOOGLE_CLIENT_ID';
        $clientSecret = getenv('google.clientSecret') ?: 'YOUR_GOOGLE_CLIENT_SECRET';
        $redirectUri = getenv('google.redirectUri') ?: base_url('auth/callback');

        $client = Services::curlrequest();

        try {
            // Get access token
            $tokenResponse = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code'          => $code,
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'grant_type'    => 'authorization_code',
                ],
            ]);

            $tokenData = json_decode($tokenResponse->getBody(), true);

            if (!isset($tokenData['access_token'])) {
                throw new \Exception('Token alınamadı');
            }

            // Get user info
            $userResponse = $client->get('https://www.googleapis.com/oauth2/v2/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $tokenData['access_token'],
                ],
            ]);

            $googleUser = json_decode($userResponse->getBody(), true);

            if (!isset($googleUser['id'])) {
                throw new \Exception('Kullanıcı bilgisi alınamadı');
            }

            // Find or create user
            $user = $this->userModel->findByGoogleId($googleUser['id']);

            if (!$user) {
                // Check if email already exists
                $existingUser = $this->userModel->findByEmail($googleUser['email']);

                if ($existingUser) {
                    // Link Google account to existing user
                    $this->userModel->update($existingUser['id'], [
                        'google_id' => $googleUser['id'],
                        'avatar'    => $googleUser['picture'] ?? null,
                    ]);
                    $user = $this->userModel->find($existingUser['id']);
                } else {
                    // Create new user
                    $userId = $this->userModel->insert([
                        'google_id' => $googleUser['id'],
                        'name'      => $googleUser['name'],
                        'email'     => $googleUser['email'],
                        'avatar'    => $googleUser['picture'] ?? null,
                    ]);
                    $user = $this->userModel->find($userId);
                }
            } else {
                // Update avatar if changed
                if (isset($googleUser['picture']) && $user['avatar'] !== $googleUser['picture']) {
                    $this->userModel->update($user['id'], [
                        'avatar' => $googleUser['picture'],
                    ]);
                }
            }

            // Set session
            $this->session->set([
                'user_id'   => $user['id'],
                'user_name' => $user['name'],
                'logged_in' => true,
            ]);

            $this->session->setFlashdata('success', 'Hoş geldiniz, ' . $user['name'] . '!');

            // Redirect to intended page or home
            $redirect = $this->session->get('redirect_after_login') ?? '/';
            $this->session->remove('redirect_after_login');

            return redirect()->to($redirect);

        } catch (\Exception $e) {
            log_message('error', 'Google OAuth Error: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Google ile giriş yapılırken bir hata oluştu.');
            return redirect()->to('/');
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/')->with('success', 'Başarıyla çıkış yaptınız.');
    }
}
