<?php namespace App\Users\Repositories\Interfaces;

use App\Users\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function createUser(array $attributes) : User;
    public function findUserById($userId) : User;
    public function findUserByEmail(string $email) : User;
    public function listAllUsers() : Collection;
    public function updateUser(array $attributes) : bool;
    public function deleteUser() : bool;
}
