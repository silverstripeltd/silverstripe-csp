<?php

namespace Silverstripe\CSP\Policies;

use SilverStripe\Control\Director;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Vimeo;
use Silverstripe\CSP\Fragments\YouTube;
use Silverstripe\CSP\Keyword;
use Silverstripe\CSP\Scheme;

class CMS extends Policy
{
    public function configure()
    {
        $this
            // HTML Editor used to include media
            ->addFragments([
                YouTube::class,
                Vimeo::class,
            ])
            ->addDirective(Directive::BASE, Keyword::SELF)
            ->addDirective(Directive::CONNECT, Keyword::SELF)
            ->addDirective(Directive::DEFAULT, Keyword::SELF)
            ->addDirective(Directive::FORM_ACTION, Keyword::SELF)
            ->addDirective(Directive::FRAME, Keyword::SELF)
            ->addDirective(Directive::CHILD, Keyword::SELF)
            ->addDirective(Directive::IMG, [
                Keyword::SELF,
                Scheme::DATA,
            ])
            ->addDirective(Directive::MEDIA, Keyword::SELF)
            ->addDirective(Directive::OBJECT, Keyword::NONE)
            ->addDirective(Directive::SCRIPT, [
                Keyword::SELF,
                Keyword::UNSAFE_INLINE,
                Keyword::UNSAFE_EVAL,
            ])
            ->addDirective(Directive::STYLE, [
                Keyword::SELF,
                Keyword::UNSAFE_INLINE,
            ]);
    }

    public function shouldBeApplied(HTTPRequest $request, HTTPResponse $response): bool
    {
        $url = $request->getURL();

        if(strpos($url, 'admin') === 0) {
            return true;
        }

        return false;
    }
}
