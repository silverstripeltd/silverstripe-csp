<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a adobe tag manager
 */
class AdobeTagManager implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                // This is a CDN for adobe tag manager
                'https://assets.adobedtm.com',
                'https://js.adsrvr.org',
                'https://insight.adsrvr.org',
                'https://*.adsrvr.org',
            ])
            ->addDirective(Directive::FRAME, [
                'https://insight.adsrvr.org',
                'https://*.adsrvr.org',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                // This is a CDN for adobe tag manager
                'https://assets.adobedtm.com',
                'https://js.adsrvr.org',
                'https://insight.adsrvr.org',
                'https://*.adsrvr.org',
            ]);
    }
}
