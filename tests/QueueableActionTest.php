<?php

namespace Spatie\QueueableAction\Tests;

use Exception;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Exceptions\InvalidConfiguration;
use Spatie\QueueableAction\Tests\TestClasses\ActionWithFailedMethod;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;
use Spatie\QueueableAction\Tests\TestClasses\ContinueMiddleware;
use Spatie\QueueableAction\Tests\TestClasses\CustomActionJob;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
use Spatie\QueueableAction\Tests\TestClasses\ModelSerializationUser;
use Spatie\QueueableAction\Tests\TestClasses\EloquentModelAction;
use Spatie\QueueableAction\Tests\TestClasses\FailingAction;
use Spatie\QueueableAction\Tests\TestClasses\InvokeableAction;
use Spatie\QueueableAction\Tests\TestClasses\MiddlewareAction;
use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;
use Spatie\QueueableAction\Tests\TestClasses\TaggedAction;
use stdClass;

class QueueableActionTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status');
        });
    }

    /** @test */
    public function an_action_can_be_queued()
    {
        Queue::fake();

        $action = new SimpleAction();

        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class);
    }

    /** @test */
    public function an_action_with_dependencies_and_input_can_be_executed_on_the_queue()
    {
        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->onQueue()->execute(new DataObject('foo'));

        $this->assertLogHas('foo bar');
    }

    /** @test */
    public function an_action_can_be_executed_on_a_queue()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->queue = 'other';

        $action->onQueue()->execute(new DataObject('foo'));

        Queue::assertPushedOn('other', ActionJob::class);
    }

    /** @test */
    public function an_action_can_be_executed_on_a_queue_using_the_on_queue_method()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->onQueue('other')->execute(new DataObject('foo'));

        Queue::assertPushedOn('other', ActionJob::class);
    }

    /** @test */
    public function an_action_is_executed_immediately_if_not_queued()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->queue = 'other';

        $action->execute(new DataObject('foo'));

        Queue::assertNotPushed(ActionJob::class);

        $this->assertLogHas('foo bar');
    }

    /** @test */
    public function an_action_can_be_queued_with_a_chain_of_other_actions_jobs()
    {
        Queue::fake();

        /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
        $action = app(ComplexAction::class);

        $action->onQueue()
            ->execute(new DataObject('foo'))
            ->chain([
                new ActionJob(SimpleAction::class),
            ]);

        Queue::assertPushedWithChain(ActionJob::class, [
            new ActionJob(SimpleAction::class),
        ]);
    }

    /** @test */
    public function an_action_with_the_invoke_method_can_be_executed_on_a_queue()
    {
        /** @var \Spatie\QueueableAction\Tests\TestClasses\InvokeableAction $action */
        $action = app(InvokeableAction::class);

        $value = random_int(0, 10000);
        $action->onQueue()->execute(new DataObject($value));

        $this->assertLogHas('Invoked: '.$value.' bar');
    }

    /** @test */
    public function an_action_has_default_action_job_tag()
    {
        Queue::fake();

        $action = new SimpleAction();

        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class, function ($action) {
            return $action->tags() === ['action_job'];
        });
    }

    /** @test */
    public function an_action_can_have_custom_job_tags()
    {
        Queue::fake();

        $action = new TaggedAction();

        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class, function ($action) {
            return $action->tags() === ['custom_tag', 'tagged_action'];
        });
    }

    public function an_action_can_have_a_custom_failed_callback()
    {
        Queue::fake();

        $action = new ActionWithFailedMethod();


        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class, function ($action) {
            return $action->failed(new Exception('foo')) === 'foo';
        });
    }

    /** @test */
    public function the_failed_callback_is_executed_on_failure()
    {
        $action = new FailingAction();

        try {
            $action->onQueue()->execute();
        } catch (Exception $e) {
            //
        }

        $this->assertSame('foobar', $_SERVER['_test_failed_message']);

        unset($_SERVER['_test_failed_message']); // cleanup
    }

    /** @test */
    public function an_action_can_have_job_middleware()
    {
        Queue::fake();

        $action = new MiddlewareAction();

        $action->onQueue()->execute();

        Queue::assertPushed(ActionJob::class, function ($action) {
            return is_array($action->middleware())
                && count($action->middleware()) === 1
                && $action->middleware[0] instanceof ContinueMiddleware;
        });
    }

    /** @test */
    public function the_action_job_class_can_be_changed()
    {
        Queue::fake();

        Config::set('queuableaction.job_class', CustomActionJob::class);

        $action = new SimpleAction();

        $action->onQueue()->execute();

        Queue::assertPushed(CustomActionJob::class);
        Queue::assertNotPushed(ActionJob::class);
    }

    /** @test */
    public function a_custom_job_class_must_extends_action_job()
    {
        Queue::fake();

        Config::set('queuableaction.job_class', stdClass::class);

        $action = new SimpleAction();

        $this->expectException(InvalidConfiguration::class);
        $this->expectExceptionMessage("The given job class `". stdClass::class ."` does not extend `".ActionJob::class."`");

        $action->onQueue()->execute();
    }

    /** @test */
    public function an_action_serializes_and_deserializes_an_eloquent_model()
    {
        $user = ModelSerializationUser::create([
            'status' => 'unverified',
        ]);

        /** @var \Spatie\QueueableAction\Tests\TestClasses\EloquentModelAction $action */
        $action = app(EloquentModelAction::class);

        $actionJob = new ActionJob($action, [$user]);

        // simulate action job is push to the queue
        $serialized = serialize($actionJob);

        // model change after pushed to queue, but before handling
        $user->update(['status' => 'verified']);

        // simulate action job is handled by a queue worker
        $unSerialized = unserialize($serialized);

        // the model should be deserialized by pulling the latest instance from the database
        $unSerializedModel = $unSerialized->parameters()[0];
        $this->assertInstanceOf(ModelSerializationUser::class, $unSerializedModel);
        $this->assertSame($user->id, $unSerializedModel->id);
        $this->assertSame('verified', $unSerializedModel->status);
    }
}
