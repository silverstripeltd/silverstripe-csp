<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Policies\Policy;

/**
 * A fragment represents a "fragment" of a policy. For example you may have youtube embeds
 * therefore you use the YouTube fragment to support them. The idea here is that by creating
 * one maintainable list of fragments we can update the fragments "all at once" on one place
 * and then run a composer update for all effected clients rather than needing to find each
 * clients config and reading/parsing it
 */
interface Fragment
{
    public static function addTo(Policy $policy): void;
}
