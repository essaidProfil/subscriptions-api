<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Authentication endpoints (JSON login handled by firewall).
 */
class AuthController extends AbstractController
{
    /**
     * Placeholder endpoint (json_login handles the auth flow).
     *
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        return $this->json(['message' => 'Use JSON login (handled by firewall).']);
    }
}
