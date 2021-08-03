<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * Allows execution of the womens refuge shield button
 * which loads its assets from its own CDN.
 *
 * @Link https://shielded.co.nz/
 */
class WomensRefuge implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://staticcdn.co.nz',
            ])
            ->addDirective(Directive::FRAME, [
                'https://staticcdn.co.nz',
            ])
            ->addDirective(Directive::IMG, [
                'https://staticcdn.co.nz',
            ]);
    }
}
