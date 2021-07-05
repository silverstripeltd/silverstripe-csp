<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * This is swiftype currently with the expectation that images are loaded over HTTPs and enabled via
 * @see ImagesOverHTTPs but we should update this to have the image src added at a later stage (e.g.
 * when we go to implement it on a project that requires it)
 */
class Swiftype implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::CONNECT, '*.swiftype.com')
            ->addDirective(Directive::SCRIPT, 'api.swiftype.com');
    }
}
