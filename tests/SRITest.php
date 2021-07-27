<?php

namespace Silverstripe\CSP\Tests;

use Silverstripe\CSP\Requirements\SRIRecord;
use SilverStripe\Dev\SapphireTest;

class SRITest extends SapphireTest
{
    protected $usesDatabase = false;

    public function testSRIAddedToFiles(): void
    {
        // Annoyingly we can't easily do this without using the database as we write the record when creating it...
        $sri = new SRIRecord();
        // We use our own file to keep the hash the same
        $integrity = $sri->createIntegrity('silverstripeltd/silverstripe-csp: tests/example.js');
        $this->assertEquals('sha384-bGe/RBNQDjw1oSdQQ9Orj3inXga8nL70PiYuibiYD7weMiTyu/Y+coqsWPmeVsqL', $integrity);
    }
}
