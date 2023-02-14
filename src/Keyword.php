<?php

namespace Silverstripe\CSP;

use ReflectionClass;

abstract class Keyword
{
    public const NONE = 'none';
    public const REPORT_SAMPLE = 'report-sample';
    public const SELF = 'self';
    public const STRICT_DYNAMIC = 'strict-dynamic';
    public const UNSAFE_EVAL = 'unsafe-eval';
    public const UNSAFE_HASHES = 'unsafe-hashes';
    public const UNSAFE_INLINE = 'unsafe-inline';

    public static function all(): array
    {
        return (new ReflectionClass(Keyword::class))->getConstants();
    }
}
