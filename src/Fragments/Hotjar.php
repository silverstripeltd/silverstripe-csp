<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * https://help.hotjar.com/hc/en-us/articles/115011640307-Content-Security-Policies
 */
class Hotjar implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $domains = [
            '*.hotjar.com',
            '*.hotjar.io',
        ];

        $policy
            ->addDirective(Directive::IMG, $domains)
            ->addDirective(Directive::SCRIPT, $domains)
            ->addDirective(Directive::CONNECT, $domains)
            ->addDirective(Directive::FRAME, $domains)
            ->addDirective(Directive::FONT, $domains);
    }
}
