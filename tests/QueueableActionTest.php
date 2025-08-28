<?php

namespace Spatie\QueueableAction\Tests;

use DateTime;
use Exception;
use Illuminate\Bus\PendingBatch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Spatie\QueueableAction\ActionJob;
use Spatie\QueueableAction\Exceptions\InvalidConfiguration;
use Spatie\QueueableAction\Tests\TestClasses\ActionReturningJob;
use Spatie\QueueableAction\Tests\TestClasses\ActionWithFailedMethod;
use Spatie\QueueableAction\Tests\TestClasses\BackoffAction;
use Spatie\QueueableAction\Tests\TestClasses\BackoffPropertyAction;
use Spatie\QueueableAction\Tests\TestClasses\ComplexAction;
use Spatie\QueueableAction\Tests\TestClasses\ContinueMiddleware;
use Spatie\QueueableAction\Tests\TestClasses\CountRunsMiddleware;
use Spatie\QueueableAction\Tests\TestClasses\CustomActionJob;
use Spatie\QueueableAction\Tests\TestClasses\DataObject;
use Spatie\QueueableAction\Tests\TestClasses\EloquentModelAction;
use Spatie\QueueableAction\Tests\TestClasses\EloquentModelCollectionAction;
use Spatie\QueueableAction\Tests\TestClasses\EloquentModelWithoutRelationsClassAction;
use Spatie\QueueableAction\Tests\TestClasses\EloquentModelWithoutRelationsCollectionParameterAction;
use Spatie\QueueableAction\Tests\TestClasses\EloquentModelWithoutRelationsParameterAction;
use Spatie\QueueableAction\Tests\TestClasses\FailingAction;
use Spatie\QueueableAction\Tests\TestClasses\InvokeableAction;
use Spatie\QueueableAction\Tests\TestClasses\MiddlewareAction;
use Spatie\QueueableAction\Tests\TestClasses\ModelSerializationUser;
use Spatie\QueueableAction\Tests\TestClasses\RetryUntilAction;
use Spatie\QueueableAction\Tests\TestClasses\SimpleAction;
use Spatie\QueueableAction\Tests\TestClasses\TaggedAction;
use stdClass;

beforeEach(function () {
    config()->set('database.default', 'testing');

    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
        $table->string('status')->nullable();
    });
});

test('an action can be queued', function () {
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class);
});

test('an action can be queued and receives job property', function () {
    $action = new ActionReturningJob();
    $job = $action->onQueue()->execute();
    expect($job)->toBeInstanceOf(\Illuminate\Foundation\Bus\PendingDispatch::class);
});

test('an action with dependencies and input can be executed on the queue', function () {
    /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
    $action = app(ComplexAction::class);

    $action->onQueue()->execute(new DataObject('foo'));

    assertLogHas('foo bar');
});

test('an action can be executed on a queue', function () {
    Queue::fake();

    /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
    $action = app(ComplexAction::class);

    $action->queue = 'other';

    $action->onQueue()->execute(new DataObject('foo'));

    Queue::assertPushedOn('other', ActionJob::class);
});

test('an action can be executed on a queue using the on queue method', function () {
    Queue::fake();

    /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
    $action = app(ComplexAction::class);

    $action->onQueue('other')->execute(new DataObject('foo'));

    Queue::assertPushedOn('other', ActionJob::class);
});

test('an action is executed immediately if not queued', function () {
    Queue::fake();

    /** @var \Spatie\QueueableAction\Tests\TestClasses\ComplexAction $action */
    $action = app(ComplexAction::class);

    $action->queue = 'other';

    $action->execute(new DataObject('foo'));

    Queue::assertNotPushed(ActionJob::class);

    assertLogHas('foo bar');
});

test('an action can be queued with a chain of other actions jobs', function () {
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
});

test('an action with the invoke method can be executed on a queue', function () {
    /** @var \Spatie\QueueableAction\Tests\TestClasses\InvokeableAction $action */
    $action = app(InvokeableAction::class);

    $value = random_int(0, 10000);
    $action->onQueue()->execute(new DataObject($value));

    assertLogHas('Invoked: ' . $value . ' bar');
});

test('an action has default action job tag', function () {
    Queue::fake();

    $action = new SimpleAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function ($action) {
        return $action->tags() === [SimpleAction::class];
    });
});

test('an action can have custom job tags', function () {
    Queue::fake();

    $action = new TaggedAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function ($action) {
        return $action->tags() === ['custom_tag', 'tagged_action'];
    });
});

test('an action can have a custom failed callback', function () {
    Queue::fake();

    $action = new ActionWithFailedMethod();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function ($action) {
        return $action->failed(new Exception('foo')) === 'foo';
    });
});

test('the failed callback is executed on failure', function () {
    $action = new FailingAction();

    try {
        $action->onQueue()->execute();
    } catch (Exception $e) {
        //
    }

    $this->assertSame('foobar', $_SERVER['_test_failed_message']);

    unset($_SERVER['_test_failed_message']); // cleanup
});

test('an action can have job middleware', function () {
    Queue::fake();

    $action = new MiddlewareAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function ($action) {
        $middleware = array_merge($action->middleware, $action->middleware());

        return count($middleware) === 1
            && $middleware[0] instanceof ContinueMiddleware;
    });
});

test('middleware runs only once', function () {
    $_SERVER['_test_run_count_middleware'] = 0;

    $action = new class extends SimpleAction {
        public function middleware(): array
        {
            return [new CountRunsMiddleware()];
        }
    };

    $action->onQueue()->execute();

    $this->assertEquals(1, $_SERVER['_test_run_count_middleware']);

    unset($_SERVER['_test_run_count_middleware']); // cleanup
});

test('the action job class can be changed', function () {
    Queue::fake();

    Config::set('queuableaction.job_class', CustomActionJob::class);

    $action = new SimpleAction();

    $action->onQueue()->execute();

    Queue::assertPushed(CustomActionJob::class);
    Queue::assertNotPushed(ActionJob::class);
});

test('a custom job class must extends action job', function () {
    Queue::fake();

    Config::set('queuableaction.job_class', stdClass::class);

    $action = new SimpleAction();

    $action->onQueue()->execute();
})->throws(
    InvalidConfiguration::class,
    "The given job class `" . stdClass::class . "` does not extend `" . ActionJob::class . "`"
);

test('an action serializes and deserializes an eloquent model', function () {
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

    expect($unSerializedModel)->toBeInstanceOf(ModelSerializationUser::class)
        ->and($unSerializedModel->id)->toEqual($user->id)
        ->and($unSerializedModel->status)->toEqual('verified');
});

test('an action serializes eloquent model respecting without relations attribute', function (
    string $actionClass,
    bool $expectRelationsSerialized
) {
    $user = ModelSerializationUser::create([]);
    $user->children()->create([]);

    $action = app($actionClass);

    // make sure relation was loaded before passing to action
    $user->load('children');

    $actionJob = new ActionJob($action, [$user]);

    // simulate action job is pushed to the queue
    $serialized = serialize($actionJob);

    // deserialize to assert which properties were loaded
    $unSerialized = unserialize($serialized);

    $unSerializedModel = $unSerialized->parameters()[0];

    expect($unSerializedModel)->toBeInstanceOf(ModelSerializationUser::class)
        ->and($unSerializedModel->relationLoaded('children'))->toEqual($expectRelationsSerialized);
})
    ->with([
        [EloquentModelAction::class, true],
        [EloquentModelWithoutRelationsParameterAction::class, false],
        [EloquentModelWithoutRelationsClassAction::class, false],
    ]);


test(
    'an action serializes an eloquent model collection respecting without relations attribute',
    function (
        string $actionClass,
        bool $expectRelationsSerialized
    ) {
        $user = ModelSerializationUser::create([]);
        $child = $user->children()->create([]);

        $action = app($actionClass);

        // make sure relations were loaded before passing to action
        $user->load('children');
        $child->load('children');

        $actionJob = new ActionJob($action, [collect([$user, $child])]);

        // simulate action job is pushed to the queue
        $serialized = serialize($actionJob);

        // deserialize to assert which properties were loaded
        $unSerialized = unserialize($serialized);

        $unSerializedModelCollection = $unSerialized->parameters()[0];

        expect($unSerializedModelCollection)->toBeInstanceOf(Collection::class)
            ->and($unSerializedModelCollection[0]->relationLoaded('children'))->toEqual($expectRelationsSerialized)
            ->and($unSerializedModelCollection[1]->relationLoaded('children'))->toEqual($expectRelationsSerialized);
    }
)
    ->with([
        [EloquentModelCollectionAction::class, true],
        [EloquentModelWithoutRelationsCollectionParameterAction::class, false],
    ]);

test('an action can have a backoff property', function () {
    Queue::fake();

    $action = new BackoffPropertyAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function (ActionJob $action) {
        return $action->backoff() === 5;
    });
});

test('an action can have a backoff function', function () {
    Queue::fake();

    $action = new BackoffAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function ($action) {
        return $action->backoff() === [5, 10, 15];
    });
});

test('an action can have a retryUntil function', function () {
    Queue::fake();
    $until = DateTime::createFromFormat("Y-m-d H:m:s", "2000-01-01 00:00:00");
    $action = new RetryUntilAction();

    $action->onQueue()->execute();

    Queue::assertPushed(ActionJob::class, function ($action) use ($until) {
        return $action->retryUntil()->getTimestamp() === $until->getTimestamp();
    });
});

test('an action can be batched', function () {
    Bus::fake();

    Bus::batch([
        new ActionJob(SimpleAction::class),
        new ActionJob(SimpleAction::class),
        new ActionJob(SimpleAction::class),
    ])->dispatch();

    Bus::assertBatched(function (PendingBatch $batch): bool {
        return $batch->jobs->count() === 3
            && $batch->jobs->first() instanceof ActionJob
            && $batch->jobs->first()->displayName() === SimpleAction::class;
    });
});
