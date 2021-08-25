<?php

namespace Silverstripe\CSP;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;
use Silverstripe\CSP\Policies\CMS;
use Silverstripe\CSP\Policies\Policy;
use SilverStripe\ORM\DatabaseAdmin;

class CSPMiddleware implements HTTPMiddleware
{
    use Configurable;

    private static array $policies = [];

    private static bool $sri_enabled = false;

    public function process(HTTPRequest $request, callable $delegate): HTTPResponse
    {
        /** @var HTTPResponse $response */
        $response = $delegate($request);

        // Skip if we've not built the database yet or on CLI requests (e.g. first dev/build)
        if (Director::is_cli()) {
            return $response;
        }

        $policies = $this->getPolicies();

        foreach ($policies as $policyClass) {
            /** @var Policy $policy */
            $policy = Injector::inst()->get($policyClass);

            if ($policy->shouldBeApplied($request, $response)) {
                $policy->applyTo($response);
            }
        }

        return $response;
    }

    public function getPolicies(): array
    {
        $policies = static::config()->get('policies');

        return !empty($policies)
            ? $policies
            : [CMS::class];
    }
}
