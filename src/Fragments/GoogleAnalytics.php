<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a Google analytics related resources
 */
class GoogleAnalytics implements Fragment
{

    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://*.google-analytics.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://*.google-analytics.com',
                'https://*.googletagmanager.com',
                'https://www.googleadservices.com',
            ])
            ->addDirective(Directive::CONNECT, [
                'https://*.google-analytics.com',
                'https://*.analytics.google.com',
                'https://*.googletagmanager.com',
                'https://www.google.co.nz/ads/ga-audiences',
                'https://google.com',
                'https://*.google.com',
                'https://www.google.com.au',
                'https://www.googleadservices.com',
                'https://*.googleapis.com',
            ])
            ->addDirective(Directive::IMG, [
                'https://*.google-analytics.com',
                'https://*.googletagmanager.com',
            ]);
    }
}
