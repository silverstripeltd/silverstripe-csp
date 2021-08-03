<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * https://developers.google.com/web/fundamentals/security/csp
 * Exclusion Google Maps API
 *
 * NB:
 * With custom map markers these should be excluded via 'self' in your `img-src` directive.
 */
class GoogleMaps implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://maps.googleapis.com',
            ])
            ->addDirective(Directive::IMG, [
                'https://maps.googleapis.com',
                'https://maps.gstatic.com',
            ]);
    }
}
