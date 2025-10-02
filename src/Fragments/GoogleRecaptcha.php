<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a Google Recaptcha related resources
 */
class GoogleRecaptcha implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT_ELEM, [
                '*.google.com',
                'https://*.gstatic.com',
                'https://googleads.g.doubleclick.net',
                'https://*.googlesyndication.com',
            ])
            ->addDirective(Directive::CONNECT, [
                '*.google.com',
                'https://*.gstatic.com',
                'https://googleads.g.doubleclick.net',
                'https://*.googlesyndication.com',
            ]);
    }
}
