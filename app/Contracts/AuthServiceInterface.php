<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{
    /**
     * Inscrire un nouvel utilisateur
     *
     * @param array $userData
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(array $userData): array;

    /**
     * Connecter un utilisateur
     *
     * @param array $credentials
     * @return array
     * @throws \App\Exceptions\AuthenticationException
     */
    public function login(array $credentials): array;

    /**
     * Déconnecter un utilisateur
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool;

    /**
     * Générer un token pour un utilisateur
     *
     * @param User $user
     * @param string $tokenName
     * @return string
     */
    public function generateToken(User $user, string $tokenName = 'auth_token'): string;

    /**
     * Valider les identifiants
     *
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(array $credentials): bool;

    /**
     * Journaliser une tentative de connexion
     *
     * @param string $email
     * @param bool $success
     * @param string|null $ipAddress
     * @return void
     */
    public function logLoginAttempt(string $email, bool $success, ?string $ipAddress = null): void;
}
