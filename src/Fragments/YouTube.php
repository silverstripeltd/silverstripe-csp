<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a youtube video embeded on the site
 */
class YouTube implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            // This is a CDN for youtube static assets
            ->addDirective(Directive::IMG, [
                '*.ytimg.com',
            ])
            ->addDirective(Directive::SCRIPT, [
                // The main youtube domain
                'www.youtube.com',
                // This is a CDN for youtube static assets which sometimes are JS assets
                's.ytimg.com',
            ])
            ->addDirective(Directive::FRAME, "*.youtube.com");
    }
}
