<?php

namespace Trax\Auth\Test\Utils;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Trax\Test\XapiStore\Utils\ActivityFactory;

class TraxAuthTestCase extends TestCase
{
    use WithFaker;

    /**
     * Clients API.
     *
     * @var \Trax\Auth\Test\Utils\ClientFactory
     */
    public $clients;

    /**
     * Accesses API.
     *
     * @var \Trax\Auth\Test\Utils\AccessFactory
     */
    public $accesses;
    
    /**
     * Users API.
     *
     * @var \Trax\Auth\Test\Utils\UserFactory
     */
    public $users;
    
    /**
     * Roles API.
     *
     * @var \Trax\Auth\Test\Utils\RoleFactory
     */
    public $roles;
    
    /**
     * Entities API.
     *
     * @var \Trax\Auth\Test\Utils\EntityFactory
     */
    public $entities;
    
    /**
     * Owners API.
     *
     * @var \Trax\Auth\Test\Utils\OwnerFactory
     */
    public $owners;
    
    /**
     * Statements extended API.
     *
     * @var \Trax\Auth\Test\Utils\StatementFactory
     */
    public $statements;
    
    /**
     * @var bool
     */
    public $asUser = false;
    
    /**
     * @var \Trax\Auth\Stores\Users\User
     */
    public $admin;

    /**
     * Setup.
     *
     * @return  void
     */
    protected function authSetup(): void
    {
        putenv("TRAX_AUTH_IS_USER=" . ($this->asUser ? 'true' : 'false'));
        parent::setUp();

        // Owners API.
        $this->owners = new ResourceApi('owners', new OwnerFactory($this->app, $this->faker), $this);

        // Roles API.
        $roleFactory = new RoleFactory($this->app, $this->faker);
        $roleFactory->setOwnerFactory($this->owners->factory);
        $this->roles = new ResourceApi('roles', $roleFactory, $this);

        // Entities API.
        $entityFactory = new EntityFactory($this->app, $this->faker);
        $entityFactory->setOwnerFactory($this->owners->factory);
        $this->entities = new ResourceApi('entities', $entityFactory, $this);

        // Clients API.
        $clientFactory = new ClientFactory($this->app, $this->faker);
        $clientFactory->setOwnerFactory($this->owners->factory);
        $clientFactory->setEntityFactory($this->entities->factory);
        $this->clients = new ResourceApi('clients', $clientFactory, $this);

        // Accesses API.
        $accessesFactory = new AccessFactory($this->app, $this->faker);
        $accessesFactory->setClientFactory($this->clients->factory);
        $this->accesses = new ResourceApi('accesses', $accessesFactory, $this);

        // Users API.
        $userFactory = new UserFactory($this->app, $this->faker);
        $userFactory->setOwnerFactory($this->owners->factory);
        $userFactory->setEntityFactory($this->entities->factory);
        $userFactory->setRoleFactory($this->roles->factory);
        $this->users = new ResourceApi('users', $userFactory, $this);

        // Activities API (to make some tests on the xAPI store).
        $activityFactory = new ActivityFactory($this->app, $this->faker);
        $activityFactory->setOwnerFactory($this->owners->factory);
        $this->xactivities = new ResourceApi('xapi/ext/activities', $activityFactory, $this);

        // Create an admin user when needed.
        if ($this->asUser) {
            $this->admin = $this->users->factory->make(['admin' => true]);
        }
    }
}
