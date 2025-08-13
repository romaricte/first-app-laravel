<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserRepositoryInterface
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Trouver un utilisateur par son email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        Log::info("Recherche utilisateur par email: {$email}");
        
        return $this->model
            ->where('email', $email)
            ->first();
    }

    /**
     * Créer un nouvel utilisateur
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        Log::info("Création d'un nouvel utilisateur: {$data['email']}");

        // Hash du mot de passe si fourni
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->model->create($data);
    }

    /**
     * Trouver un utilisateur par son ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    /**
     * Obtenir tous les utilisateurs
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Mettre à jour un utilisateur
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        Log::info("Mise à jour utilisateur ID: {$user->id}");

        // Hash du mot de passe si fourni
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user->fresh();
    }

    /**
     * Supprimer un utilisateur
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        Log::info("Suppression utilisateur ID: {$user->id}");
        
        return $user->delete();
    }

    /**
     * Vérifier si un email existe déjà
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}
