<?php

namespace Spatie\QueueableAction\Tests\TestClasses;

use Spatie\QueueableAction\Testing\QueueableActionFake;

class QueueableActionFakeTestClass extends QueueableActionFake
{
    public static function getPushedCountTest(string $actionJobClass): int
    {
        return self::getPushedCount($actionJobClass);
    }

    public static function getChainedClassesTest()
    {
        return self::getChainedClasses();
    }
}
