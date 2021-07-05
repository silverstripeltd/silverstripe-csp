<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Keyword;
use Silverstripe\CSP\Policies\Policy;
use Silverstripe\CSP\Scheme;

/**
 * Quite often it's hard to know all image sources that are safe, therefore
 * we can opt to go for HTTPs only images and make the assumption that this
 * kind of attack would not have a large impact on our sites
 *
 * Exceptions to this could be:
 * A site where icons are used heavily for user actions so replacing
 * the icon to delete a user with saving for the user could result in an admin
 * performing an action by accident)
 */
class ImagesOverHTTPs implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::IMG, [
                Keyword::SELF,
                Scheme::DATA,
                Scheme::HTTPS,
            ]);
    }
}
