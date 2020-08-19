<?php

use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Faker\Factory as Faker;
abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        Hash::setRounds(4);

        return $app;
    }

    protected $faker;

    /**
     * Set up the test
     */
    public function setUp():void
    {
        parent::setUp();
        $this->faker = Faker::create();
    }

    /**
     * Reset the migrations
     */
    public function tearDown():void
    {
        $this->artisan('migrate:reset');
        parent::tearDown();
    }
}
