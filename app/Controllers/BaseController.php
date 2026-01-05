<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\Session\Session;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 */
abstract class BaseController extends Controller
{
    /**
     * @var array<string>
     */
    protected $helpers = ['url', 'form', 'text'];

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array|null
     */
    protected $currentUser = null;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');

        // Load current user if logged in
        if ($this->session->has('user_id')) {
            $userModel = model('UserModel');
            $this->currentUser = $userModel->find($this->session->get('user_id'));
        }
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return $this->currentUser !== null;
    }

    /**
     * Get current user
     */
    protected function getCurrentUser(): ?array
    {
        return $this->currentUser;
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->session->setFlashdata('error', 'Bu işlem için giriş yapmanız gerekiyor.');
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Check if current user is admin
     */
    protected function isAdmin(): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $adminEmails = getenv('admin.emails') ?: '';
        $adminEmailList = array_map('trim', explode(',', $adminEmails));

        return in_array($this->currentUser['email'], $adminEmailList);
    }

    /**
     * Require admin access
     */
    protected function requireAdmin(): void
    {
        $this->requireAuth();

        if (!$this->isAdmin()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Get shared view data
     */
    protected function getViewData(array $data = []): array
    {
        return array_merge([
            'currentUser' => $this->currentUser,
            'isLoggedIn'  => $this->isLoggedIn(),
            'isAdmin'     => $this->isAdmin(),
        ], $data);
    }
}
