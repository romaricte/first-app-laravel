<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Trouver un utilisateur par son email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Créer un nouvel utilisateur
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Trouver un utilisateur par son ID
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Obtenir tous les utilisateurs
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Mettre à jour un utilisateur
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User;

    /**
     * Supprimer un utilisateur
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;

    /**
     * Vérifier si un email existe déjà
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool;
}
