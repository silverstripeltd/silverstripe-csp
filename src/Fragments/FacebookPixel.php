<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a Facebook pixel related resources
 */
class FacebookPixel implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://connect.facebook.net',
                'https://www.facebook.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://connect.facebook.net',
                'https://www.facebook.com',
            ])
            ->addDirective(Directive::CONNECT, [
                'https://connect.facebook.net',
                'https://www.facebook.com',
                'https://www.instagram.com',
            ])
            ->addDirective(Directive::FRAME, [
                'https://www.facebook.com',
            ])
            ->addDirective(Directive::FORM_ACTION, [
                'https://www.facebook.com/tr/',
            ])
            ->addDirective(Directive::IMG, [
                'https://connect.facebook.net',
                'https://www.facebook.com',
            ]);
    }
}
