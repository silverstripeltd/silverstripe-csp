<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Policies\Policy;

/**
 * Googles Recaptcha
 * https://developers.google.com/recaptcha/docs/faq#im-using-content-security-policy-csp-on-my-website.-how-can-i-configure-it-to-work-with-recaptcha
 */
class Recaptcha implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'www.google.com/recaptcha/',
                'www.gstatic.com/recaptcha/',
            ])
            ->addDirective(Directive::FRAME, [
                'www.google.com/recaptcha/',
                'recaptcha.google.com/recaptcha/',
            ]);
    }
}
