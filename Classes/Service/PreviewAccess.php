<?php

declare(strict_types=1);

namespace WebTp3\YoastNews\Service;

/** Request-scoped authorization, set only after successful token validation. */
class PreviewAccess
{
    public ?array $record = null;
}
