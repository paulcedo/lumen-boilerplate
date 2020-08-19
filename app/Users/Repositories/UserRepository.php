<?php namespace App\Users\Repositories;

use App\Users\Exceptions\UserErrorException;
use App\Users\Exceptions\UserInvalidArgumentException;
use App\Users\Exceptions\UserNotFoundException;
use App\Users\Repositories\Interfaces\UserRepositoryInterface;
use App\Users\User;
use ErrorException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository implements UserRepositoryInterface
{
    protected $model;
    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function createUser(array $attributes) : User
    {
        try {
            return $this->model->create($attributes);
        } catch (QueryException $e) {
            throw new UserInvalidArgumentException($e->getMessage());
        } catch (\ErrorException $e) {
            throw new UserErrorException($e->getMessage());
        }
    }

    public function findUserById($userId) : User
    {
        try {
            return $this->model->findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($e->getMessage());
        }
    }

    public function findUserByEmail(string $email) : User
    {
        try {
            return $this->model->where('email',$email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException($e->getMessage());
        }
    }

    public function listAllUsers() : Collection
    {
        return $this->model->all();
    }

    public function updateUser(array $attributes) : bool
    {
        try {
            return $this->model->update($attributes);
        } catch (QueryException $e) {
            throw new UserInvalidArgumentException($e->getMessage());
        } catch (NotFoundHttpException $e) {
            throw new UserNotFoundException($e->getMessage());
        } catch (ErrorException $e) {
            throw new UserErrorException($e->getMessage());
        }
    }

    public function deleteUser() : bool
    {
        return $this->model->delete();
    }

}
