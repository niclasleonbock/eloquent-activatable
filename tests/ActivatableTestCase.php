<?php
use Orchestra\Testbench\TestCase;
use Carbon\Carbon;

class ActivatableTestCase extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $artisan = $this->app->make('artisan');

        $artisan->call('migrate', [
            '--database'    => 'testing',
            '--path'        => '../tests/database/migrations',
        ]);

        $artisan->call('db:seed', [
            '--database'    => 'testing',
            '--class'       => 'TopicTableSeeder',
        ]);
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__ . '/../src';

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', array(
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'prefix'    => '',
        ));
    }

    protected function getModel()
    {
        return new Topic();
    }

    protected function getActivatedTopic()
    {
        $topic = $this->getModel()->withDeactivated()->first();
        $topic->activate();

        return $topic;
    }

    protected function getDeactivatedTopic()
    {
        $topic = $this->getModel()->withDeactivated()->first();
        $topic->deactivate();

        return $topic;
    }


    /**
     * Test only activated (default) functionality.
     *
     * @test
     */
    public function testOnlyActivated()
    {
        $count = $this->getModel()->count();

        $this->assertEquals(0, $count);
    }

    /**
     * Test only activated (default) functionality.
     *
     * @test
     */
    public function testWithDectivated()
    {
        $count = $this->getModel()->withDeactivated()->count();

        $this->assertEquals(1, $count);
    }

    /**
     * Test activating functionality.
     *
     * @test
     */
    public function testActivating()
    {
        $topic = $this->getDeactivatedTopic();

        $topic->activate();

        $this->assertEquals(Carbon::now(), $topic->activated_at);
    }

    /**
     * Test deactivating functionality.
     *
     * @test
     */
    public function testDeactivating()
    {
        $topic = $this->getActivatedTopic();

        $topic->deactivate();

        $this->assertEquals(null, $topic->activated_at);
    }

    /**
     * Test checking status functionality of activated data set.
     *
     * @test
     */
    public function testStatusActivated()
    {
        $topic = $this->getActivatedTopic();

        $this->assertEquals(true, $topic->activated());
    }

    /**
     * Test checking status functionality of deactivated data set.
     *
     * @test
     */
    public function testStatusDeactivated()
    {
        $topic = $this->getDeactivatedTopic();

        $this->assertEquals(false, $topic->activated());
    }
}
