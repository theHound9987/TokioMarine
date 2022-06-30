<?php

namespace App\Providers;

use App\Repositories\Interfaces\NoteRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use App\Repositories\NoteRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(NoteRepositoryInterface::class,NoteRepository::class);
        $this->app->bind(TagRepositoryInterface::class,TagRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
