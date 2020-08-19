<?php
namespace App\Providers;

use App\Users\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use App\Users\Repositories\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bind the interface to an implementation repository class
     */
    public function register()
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );
    }
}
