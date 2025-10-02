<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a jQuery CDN
 */
class JQuery implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                // This is a CDN for jQuery used from userforms
                'https://code.jquery.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                // This is a CDN for jQuery used from userforms
                'https://code.jquery.com',
            ]);
    }
}
