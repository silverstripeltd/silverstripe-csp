<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a LinkedIn related assets
 */
class LinkedIn implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://*.licdn.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://*.licdn.com',
            ])
            ->addDirective(Directive::CONNECT, [
                'https://*.linkedin.oribi.io',
                'https://*.linkedin.com',
            ])
            ->addDirective(Directive::IMG, [
                'https://*.linkedin.com',
            ]);
    }
}
