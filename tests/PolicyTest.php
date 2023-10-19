<?php

namespace Silverstripe\CSP\Tests;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injector;
use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Vimeo;
use Silverstripe\CSP\Fragments\YouTube;
use Silverstripe\CSP\Keyword;
use Silverstripe\CSP\NonceGenerator;
use Silverstripe\CSP\Policies\Basic;
use Silverstripe\CSP\Policies\CMS;
use Silverstripe\CSP\Policies\Policy;
use Silverstripe\CSP\Scheme;
use Silverstripe\CSP\Value;
use SilverStripe\Dev\SapphireTest;

/*
 * We can't test middleware with silverstripe... so we're testing the policy itself (mainly)
 */
class PolicyTest extends SapphireTest
{
    public function testBasicPolicyAddsCorrectHeaders(): void
    {
        [$request, $response] = $this->getRequestResponse();
        /** @var Policy $policy */
        $policy = Injector::inst()->get(Basic::class);
        $policy->applyTo($response);
        $nonce = NonceGenerator::get();
        $expected = <<<TXT
        base-uri 'self'; connect-src 'self'; default-src 'self'; form-action 'self'; img-src 'self'; media-src 'self'; object-src 'none'; script-src 'self' 'nonce-$nonce'; style-src 'self' 'nonce-$nonce'; font-src 'self'; upgrade-insecure-requests
        TXT;
        $this->assertEquals($expected, $response->getHeader('content-security-policy'));
        $this->assertEquals('Basic', $response->getHeader('csp-name'));
    }

    public function testAdminPolicyWillOnlyBeAddedForAdmin(): void
    {
        [$request, $response] = $this->getRequestResponse('admin');
        /** @var Policy $policy */
        $policy = Injector::inst()->get(CMS::class);
        $this->assertTrue($policy->shouldBeApplied($request, $response));

        [$request, $response] = $this->getRequestResponse('example');
        $this->assertFalse($policy->shouldBeApplied($request, $response));
    }

    public function testAReportURICanBeSet(): void
    {
        [$request, $response] = $this->getRequestResponse();
        /** @var Policy $policy */
        $policy = Injector::inst()->get(CMS::class);

        Environment::setEnv('CSP_REPORT_TO', 'https://example.com');
        Environment::setEnv('CSP_REPORT_ONLY', 'enabled');
        $policy->applyTo($response);
        $this->assertContains('report-to https://example.com', $response->getHeader('Content-Security-Policy-Report-Only'));
        $this->assertContains('report-uri https://example.com', $response->getHeader('Content-Security-Policy-Report-Only'));

        $policy->reportTo('https://silverstripe.com');
        $response->removeHeader('Content-Security-Policy-Report-Only');
        $policy->applyTo($response);
        $this->assertContains('report-to https://silverstripe.com', $response->getHeader('Content-Security-Policy-Report-Only'));
        $this->assertContains('report-uri https://silverstripe.com', $response->getHeader('Content-Security-Policy-Report-Only'));
    }

    public function testIsCanUseMultipleValuesForTheSameDirective(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this
                    ->addDirective(Directive::FRAME, 'src-1')
                    ->addDirective(Directive::FRAME, 'src-2')
                    ->addDirective(Directive::FORM_ACTION, 'action-1')
                    ->addDirective(Directive::FORM_ACTION, 'action-2');
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'frame-src src-1 src-2; form-action action-1 action-2',
            $response->getHeader('content-security-policy')
        );
    }

    public function testNoneOverridesOtherValuesForTheSameDirective(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this
                    ->addDirective(Directive::CONNECT, 'connect-1')
                    ->addDirective(Directive::FRAME, 'src-1')
                    ->addDirective(Directive::CONNECT, Keyword::NONE);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'connect-src \'none\'; frame-src src-1',
            $response->getHeader('content-security-policy')
        );
    }

    public function testValuesOverrideNoneValueForTheSameDirective(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this
                    ->addDirective(Directive::CONNECT, Keyword::NONE)
                    ->addDirective(Directive::FRAME, 'src-1')
                    ->addDirective(Directive::CONNECT, Keyword::SELF);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'connect-src \'self\'; frame-src src-1',
            $response->getHeader('content-security-policy')
        );
    }

    public function testAPolicyCanBePutIntoReportModeOnly(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this->reportOnly();
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertNull($response->getHeader('content-security-policy'));
        $this->assertNotNull($response->getHeader('content-security-policy-report-only'));
    }

    public function testItCanAddMultipleValuesForTheSameDirectiveInOneGo(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this
                    ->addDirective(Directive::FRAME, ['src-1', 'src-2']);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'frame-src src-1 src-2',
            $response->getHeader('content-security-policy')
        );
    }

    public function testItWillAutomaticallyQuoteSpecialDirectiveValues(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this->addDirective(Directive::SCRIPT, [Keyword::SELF]);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            "script-src 'self'",
            $response->getHeader('content-security-policy')
        );
    }

    public function testItWillAutomaticallyQuoteHashedValues(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this->addDirective(Directive::SCRIPT, [
                    'sha256-hash1',
                    'sha384-hash2',
                    'sha512-hash3',
                ]);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            "script-src 'sha256-hash1' 'sha384-hash2' 'sha512-hash3'",
            $response->getHeader('content-security-policy')
        );
    }

    public function testItWillNotOutputTheSameDirectiveValuesTwice(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this->addDirective(Directive::SCRIPT, [Keyword::SELF, Keyword::SELF]);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            "script-src 'self'",
            $response->getHeader('content-security-policy')
        );
    }

    public function testItWillAHandleSchemeValues(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this->addDirective(Directive::IMG, [
                    Scheme::DATA,
                    Scheme::HTTPS,
                    Scheme::WS,
                ]);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'img-src data: https: ws:',
            $response->getHeader('content-security-policy')
        );
    }

    public function testItCanUseAnEmptyValueForADirective(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this
                    ->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, Value::NO_VALUE)
                    ->addDirective(Directive::BLOCK_ALL_MIXED_CONTENT, Value::NO_VALUE);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'upgrade-insecure-requests; block-all-mixed-content',
            $response->getHeader('content-security-policy')
        );
    }

    public function testCanAddFragmentsToAPolicy(): void
    {
        $policy = new class extends Policy {
            public function configure()
            {
                $this->addFragments([
                   Youtube::class,
                   Vimeo::class,
                ]);
            }
        };

        [$request, $response] = $this->getRequestResponse();
        $policy->applyTo($response);
        $this->assertEquals(
            'img-src *.ytimg.com; script-src www.youtube.com s.ytimg.com player.vimeo.com; frame-src *.youtube.com player.vimeo.com; child-src player.vimeo.com',
            $response->getHeader('content-security-policy')
        );
    }

    public function getRequestResponse(string $url = '/'): array
    {
        return [
            new HTTPRequest('GET', $url),
            HTTPResponse::create(),
        ];
    }
}
