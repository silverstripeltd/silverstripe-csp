<?php

namespace Silverstripe\CSP\Fragments;

use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Fragments\Fragment;
use Silverstripe\CSP\Policies\Policy;

/**
 * This allows you to have a TikTok Pixel.
 */
class TikTokPixel implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::FRAME, [
                'bytedance:',
                'sslocal:',
            ])
            ->addDirective(Directive::SCRIPT, [
                'https://analytics.tiktok.com',
            ])
            ->addDirective(Directive::SCRIPT_ELEM, [
                'https://analytics.tiktok.com',
            ])
            ->addDirective(Directive::CONNECT, [
                'https://analytics.tiktok.com',
            ])
            ->addDirective(Directive::IMG, [
                'https://analytics.tiktok.com',
            ]);
    }
}
