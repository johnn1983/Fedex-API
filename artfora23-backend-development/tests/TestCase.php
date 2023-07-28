<?php

namespace App\Tests;

use Illuminate\Contracts\Console\Kernel;
use Artel\Support\Tests\TestCase as BaseTestCase;
use Artel\Support\AutoDoc\Tests\AutoDocTestCaseTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use AutoDocTestCaseTrait;
    protected $prepareSequencesExceptTables = ['migrations', 'settings'];

    protected string $jwt;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->loadEnvironmentFrom('.env.testing');
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function actingAs(Authenticatable $user, $driver = null)
    {
        parent::actingAs($user, $driver);

        $this->jwt = JWTAuth::fromUser($user);

        $this->withHeaders(['Authorization' => 'Bearer ' . $this->jwt]);

        return $this;
    }
}
