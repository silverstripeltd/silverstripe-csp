<?php

namespace Silverstripe\CSP\Tests;

use Silverstripe\CSP\Directive;
use SilverStripe\Dev\SapphireTest;

class DirectiveTest extends SapphireTest
{
    public function testItCanDertimineIfADirectiveIsValid(): void
    {
        $this->assertTrue(Directive::isValid(Directive::BASE));
        $this->assertFalse(Directive::isValid('invalid'));
    }
}
