<?php

namespace App\Application\Project\ContentBundle\Attributes\Acl;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD )]
class Api
{
    public function __construct(
        bool $enable,
        string $title,
        ?string $description = null,
    ){

    }
}