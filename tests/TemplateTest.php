<?php

namespace Silverstripe\CSP\Tests;

use Silverstripe\CSP\NonceGenerator;
use Silverstripe\CSP\Requirements\CSPBackend;
use SilverStripe\Dev\SapphireTest;

class TemplateTest extends SapphireTest
{
    public function testNonceGetsAddedToScripts(): void
    {
        $nonce = NonceGenerator::get();
        $requirements = CSPBackend::create();
        $requirements->customScript('<script>alert(1);</script>');
        $html = $requirements->includeInHTML('<head></head><body></body>');

        $expected = <<<HTML
        <head></head><body><script type="application/javascript" nonce="$nonce">//<![CDATA[
        <script>alert(1);</script>
        //]]></script>
        </body>
        HTML;

        $this->assertEquals($expected, $html);
    }

    public function testNonceGetsAddedToStyles(): void
    {
        $nonce = NonceGenerator::get();
        $requirements = CSPBackend::create();
        $requirements->customCSS('<style>p{ height: 0; }</style>');
        $html = $requirements->includeInHTML('<head></head><body></body>');

        $expected = <<<HTML
        <head><style type="text/css" nonce="$nonce">
        <style>p{ height: 0; }</style>
        </style>
        </head><body></body>
        HTML;

        $this->assertEquals($expected, $html);
    }
}
