<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a vimeo video embeded on the site
 */
class Vimeo implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            // We want to allow scripts loaded from here as they recommend using their embed player
            ->addDirective(Directive::SCRIPT, 'player.vimeo.com')
            ->addDirective(Directive::FRAME, "player.vimeo.com")
            ->addDirective(Directive::CHILD, "player.vimeo.com");
    }
}
