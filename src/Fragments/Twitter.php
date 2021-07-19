<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * Docs for this one are super average so will take some trial and error:
 * https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html
 */
class Twitter implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'static.ads-twitter.com/oct.js',
                'static.ads-twitter.com/uwt.js',
                '*.twitter.com',
            ])
            ->addDirective(Directive::IMG, 't.co');
    }
}
