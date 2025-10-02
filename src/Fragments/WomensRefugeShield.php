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
            ->addDirective(Directive::SCRIPT, [
                'staticcdn.co.nz',
            ])
            ->addDirective(Directive::IMG, [
                'shielded.co.nz',
                'staticcdn.co.nz',
            ])
            ->addDirective(Directive::FONT, [
                'staticcdn.co.nz',
            ])
            ->addDirective(Directive::FRAME, [
                'staticcdn.co.nz',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'staticcdn.co.nz',
            ]);
    }
}
