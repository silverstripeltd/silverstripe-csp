<?php

namespace Silverstripe\CSP\Policies;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Keyword;
use Silverstripe\CSP\Value;

/*
 * This is the default "safe" config which we usually extend from.
 */
class Basic extends Policy
{
    public function configure()
    {
        $this
            ->addDirective(Directive::BASE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::IMG, Keyword::SELF)
            ->addDirective(Directive::MEDIA, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::SCRIPT, Keyword::SELF)
            ->addDirective(Directive::STYLE, Keyword::SELF)
            ->addNonceForDirective(Directive::SCRIPT)
            ->addNonceForDirective(Directive::STYLE)
            ->addDirective(Directive::UPGRADE_INSECURE_REQUESTS, Value::NO_VALUE);
    }
}
