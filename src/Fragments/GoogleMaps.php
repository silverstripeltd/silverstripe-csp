<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/*
 * Allows execution of Google Maps API related resources
 * Depending on how you include GoogleMaps API you will need either:
 *  - inline script: requires nonce.
 *  - node package in build-chain via dynamic calls: whitelist 'https://maps.google.com' via 'script-src' directive.
 *
 * https://content-security-policy.com/examples/google-maps/
 */
class GoogleMaps implements Fragment
{
    public static function addTo(Policy $policy): void
    {
         $policy
             ->addDirective(Directive::CONNECT, 'https://maps.googleapis.com')
             ->addDirective(Directive::IMG,
                 [
                    'https://maps.gstatic.com',
                    'https://*.googleapis.com',
                    'https://*.ggpht.com'
                 ]
             );
    }
}
