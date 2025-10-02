<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a qualtrics survey
 */
class QualtricsSurvey implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://*.qualtrics.com',
            ])
            ->addDirective(Directive::CONNECT, [
                'https://*.qualtrics.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://*.qualtrics.com',
            ]);
    }
}
