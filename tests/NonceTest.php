<?php

namespace Silverstripe\CSP\Tests;

use Silverstripe\CSP\NonceGenerator;
use SilverStripe\Dev\SapphireTest;

class NonceTest extends SapphireTest
{
    public function testCallingNonceWillGenerateTheSameValue(): void
    {
        $nonce = NonceGenerator::get();

        $this->assertEquals(strlen($nonce), 32);

        foreach (range(1, 5) as $i) {
            $this->assertEquals($nonce, NonceGenerator::get());
        }
    }
}
