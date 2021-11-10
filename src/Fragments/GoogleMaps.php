<?php

namespace App\ContentSecurity\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/*
 * Allows execution of Google Maps API related resources
 * Nonce on the https://maps.google.com/maps/api/js URL is required before using this fragment.
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
