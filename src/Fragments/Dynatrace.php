<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a Dynatrace
 */
class Dynatrace implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::CONNECT, [
                'https://*.dynatrace.com',
                'https://*.bf.dynatrace.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://*.dynatrace.com',
            ])
            ->addDirective(Directive::SCRIPT, [
                'https://js-cdn.dynatrace.com',
                'https://*.dynatrace.com',
            ]);
    }
}
