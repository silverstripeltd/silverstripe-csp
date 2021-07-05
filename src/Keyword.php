<?php

namespace Silverstripe\CSP;

use ReflectionClass;

abstract class Keyword
{
    const NONE = 'none';
    const REPORT_SAMPLE = 'report-sample';
    const SELF = 'self';
    const STRICT_DYNAMIC = 'strict-dynamic';
    const UNSAFE_EVAL = 'unsafe-eval';
    const UNSAFE_HASHES = 'unsafe-hashes';
    const UNSAFE_INLINE = 'unsafe-inline';

    public static function all(): array
    {
        return (new ReflectionClass(Keyword::class))->getConstants();
    }
}
