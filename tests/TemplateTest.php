<?php

namespace Silverstripe\CSP\Tests;

use Silverstripe\CSP\Nonce\NonceGenerator;
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

    public function testCustomScriptAttributesAreKept(): void
    {
        $nonce = NonceGenerator::get();
        $requirements = CSPBackend::create();
        $requirements->customScriptWithAttributes('alert(1);', ['type' => 'module', 'data-test' => 'one'], 'module');
        $requirements->customScriptWithAttributes('alert(2);', ['async' => true]);
        $html = $requirements->includeInHTML('<head></head><body></body>');

        $expected = <<<HTML
        <head></head><body><script type="module" data-test="one" nonce="$nonce">//<![CDATA[
        alert(1);
        //]]></script>
        <script type="application/javascript" async nonce="$nonce">//<![CDATA[
        alert(2);
        //]]></script>
        </body>
        HTML;

        $this->assertEquals($expected, $html);
    }

    public function testCustomScriptReplacesAttributes(): void
    {
        $nonce = NonceGenerator::get();
        $requirements = CSPBackend::create();
        $requirements->customScriptWithAttributes('alert(1);', ['type' => 'module'], 'replaced');
        $requirements->customScript('alert(2);', 'replaced');
        $html = $requirements->includeInHTML('<head></head><body></body>');

        $this->assertStringContainsString('<script type="application/javascript" nonce="' . $nonce . '">', $html);
        $this->assertStringNotContainsString('type="module"', $html);
    }

    public function testNonceCannotBeOverridden(): void
    {
        $nonce = NonceGenerator::get();
        $requirements = CSPBackend::create();
        $requirements->customScriptWithAttributes('alert(1);', ['nonce' => 'not-the-nonce']);
        $html = $requirements->includeInHTML('<head></head><body></body>');

        $this->assertStringContainsString('nonce="' . $nonce . '"', $html);
        $this->assertStringNotContainsString('not-the-nonce', $html);
    }

    public function testCustomScriptsAndCSSKeepTheFrameworkReturnType(): void
    {
        $nonce = NonceGenerator::get();
        $requirements = CSPBackend::create();
        $requirements->customScript('alert(1);', 'script');
        $requirements->customCSS('p { height: 0; }', 'style');

        $this->assertSame(['script' => 'alert(1);'], $requirements->getCustomScripts());
        $this->assertSame(['style' => 'p { height: 0; }'], $requirements->getCustomCSS());
        $this->assertSame(
            ['script' => ['nonce' => $nonce, 'src' => 'alert(1);']],
            $requirements->getCustomScriptsWithNonce()
        );
        $this->assertSame(
            ['style' => ['nonce' => $nonce, 'src' => 'p { height: 0; }']],
            $requirements->getCustomCSSWithNonce()
        );
    }
}
