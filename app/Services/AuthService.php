<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Inscrire un nouvel utilisateur
     *
     * @param array $userData
     * @return array
     * @throws ValidationException
     */
    public function register(array $userData): array
    {
        Log::info("Tentative d'inscription pour: {$userData['email']}");

        // Vérifier si l'email existe déjà
        if ($this->userRepository->emailExists($userData['email'])) {
            Log::warning("Tentative d'inscription avec email existant: {$userData['email']}");
            throw ValidationException::withMessages([
                'email' => ['Cette adresse email est déjà utilisée.']
            ]);
        }

        try {
            // Créer l'utilisateur
            $user = $this->userRepository->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'], // Le hash est fait dans le repository
            ]);

            // Générer le token
            $token = $this->generateToken($user);

            Log::info("Inscription réussie pour: {$userData['email']}");

            return [
                'user' => $user,
                'token' => $token
            ];

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'inscription: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Connecter un utilisateur
     *
     * @param array $credentials
     * @return array
     * @throws \Exception
     */
    public function login(array $credentials): array
    {
        $email = $credentials['email'];
        $ipAddress = request()->ip();

        Log::info("Tentative de connexion pour: {$email} depuis IP: {$ipAddress}");

        try {
            // Valider les identifiants
            if (!$this->validateCredentials($credentials)) {
                $this->logLoginAttempt($email, false, $ipAddress);
                throw new \Exception('Identifiants incorrects');
            }

            // Récupérer l'utilisateur authentifié
            $user = Auth::user();
            
            // Générer le token
            $token = $this->generateToken($user);

            $this->logLoginAttempt($email, true, $ipAddress);

            Log::info("Connexion réussie pour: {$email}");

            return [
                'user' => $user,
                'token' => $token
            ];

        } catch (\Exception $e) {
            Log::error("Erreur lors de la connexion: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Déconnecter un utilisateur
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        try {
            // Supprimer le token actuel
            $user->currentAccessToken()->delete();
            
            Log::info("Déconnexion réussie pour utilisateur ID: {$user->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Erreur lors de la déconnexion: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Générer un token pour un utilisateur
     *
     * @param User $user
     * @param string $tokenName
     * @return string
     */
    public function generateToken(User $user, string $tokenName = 'auth_token'): string
    {
        Log::info("Génération token pour utilisateur ID: {$user->id}");
        
        return $user->createToken($tokenName)->plainTextToken;
    }

    /**
     * Valider les identifiants
     *
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(array $credentials): bool
    {
        return Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ]);
    }

    /**
     * Journaliser une tentative de connexion
     *
     * @param string $email
     * @param bool $success
     * @param string|null $ipAddress
     * @return void
     */
    public function logLoginAttempt(string $email, bool $success, ?string $ipAddress = null): void
    {
        $status = $success ? 'SUCCÈS' : 'ÉCHEC';
        $ip = $ipAddress ?? 'IP inconnue';
        
        Log::info("Tentative de connexion - Email: {$email}, Statut: {$status}, IP: {$ip}");
        
        // Ici vous pourriez aussi enregistrer dans une table dédiée aux logs de connexion
        // si vous voulez garder un historique plus détaillé
    }
}
