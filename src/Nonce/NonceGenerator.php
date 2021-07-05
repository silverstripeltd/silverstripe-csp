<?php

namespace Silverstripe\CSP;

use SilverStripe\Core\Injector\Injectable;

/**
 * This generates the nonce's, you can implement your own version otherwise by default
 * we're using the random string implementation
 */
abstract class NonceGenerator
{
    use Injectable;

    private ?string $nonce = null;

    abstract public function generate(): string;

    public static function get(): string
    {
        $instance = static::singleton();

        if ($instance->nonce === null) {
            $instance->nonce = $instance->generate();
        }

        return $instance->nonce;
    }
}
