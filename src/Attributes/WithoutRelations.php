<?php

namespace Spatie\QueueableAction\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PARAMETER)]

class WithoutRelations
{
}
