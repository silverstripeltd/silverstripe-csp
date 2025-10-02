<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a ZScalerThree host
 */
class ZScalerThree implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://*.zscalerthree.net',
                'https://gateway.zscalerthree.net',
            ])
            ->addDirective(Directive::FRAME, [
                'https://*.zscalerthree.net',
                'https://gateway.zscalerthree.net',
                'https://gateway.zscalertwo.net',
                'https://gateway.zscaler.net',
            ]);
    }
}
