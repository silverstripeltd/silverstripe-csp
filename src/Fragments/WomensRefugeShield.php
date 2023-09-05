<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to use the remote js for the womens refuge shield https://shielded.co.nz/
 */
class WomensRefugeShield implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, 'https://staticcdn.co.nz')
            ->addDirective(Directive::IMG, [
                'https://shielded.co.nz',
                'https://staticcdn.co.nz',
            ])
            ->addDirective(Directive::FRAME, 'https://staticcdn.co.nz');
    }
}
