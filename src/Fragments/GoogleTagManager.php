<?php

namespace Silverstripe\CSP\Fragments;

use SilverStripe\Core\Config\Configurable;
use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Keyword;
use Silverstripe\CSP\Policies\Policy;
use Silverstripe\CSP\Scheme;

/**
 * https://developers.google.com/tag-manager/web/csp
 */
class GoogleTagManager implements Fragment
{
    use Configurable;

    private static bool $whitelist_google_regional_domains = false;

    public static function addTo(Policy $policy): void
    {
        self::undocumented($policy);
        self::enableGTM($policy);
        self::customJavascriptVars($policy);
        self::previewMode($policy);
        self::analytics($policy);
        self::optimize($policy);
        self::adConversions($policy);
        self::adRemarketing($policy);
    }

    /*
     * CSP reported directive URIs that were not covered in the google docs
     * and were continually over reporting CSP URI infringements.
     *
     * https://developers.google.com/web/fundamentals/security/csp#implementation_details
     */
    public static function undocumented(Policy $policy): void
    {
        $policy
            ->addDirective(
                Directive::FRAME,
                [
                    'https://*.doubleclick.net',
                    'https://stats.g.doubleclick.net',
                ]
            )
            ->addDirective(Directive::CONNECT, [
                'https://adservice.google.com',
                'https://www.google.com',
                'https://*.doubleclick.net',
            ]);

        // Google uses localised regional endpoint domains for their services
        // if seeing regional google domain report violations
        // setting this config will whitelist all img-src to allow 'https:'.
        if (self::config()->get('whitelist_google_regional_domains') === true) {
            $policy->addDirective(Directive::IMG, Scheme::HTTPS);
        }
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#enabling_the_google_tag_manager_snippet
     */
    public static function enableGTM(Policy $policy): void
    {
        $policy
            // Preferred approach is nonce the GoogleTagManager gtag.js script
            // however to provide a default backup for other digital marketing tools like Adobe tag manager
            // which can call gtag.js script without applying a nonce, whitelisting the GTM domain is required.
            ->addDirective(Directive::SCRIPT, 'https://www.googletagmanager.com')
            ->addDirective(Directive::FRAME, 'https://www.googletagmanager.com')
            ->addDirective(Directive::IMG, 'https://www.googletagmanager.com');
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#custom_javascript_variables
     */
    public static function customJavascriptVars(Policy $policy): void
    {
        $policy->addDirective(Directive::SCRIPT, Keyword::UNSAFE_EVAL);
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#preview_mode
     */
    public static function previewMode(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, 'https://tagmanager.google.com')
            ->addDirective(Directive::STYLE, [
                'https://tagmanager.google.com',
                'https://fonts.googleapis.com',
            ])
            ->addDirective(Directive::IMG, [
                'https://ssl.gstatic.com',
                'https://www.gstatic.com',
            ])
            ->addDirective(Directive::FONT, [
                'https://fonts.gstatic.com',
                Scheme::DATA,
            ]);
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#universal_analytics_google_analytics
     */
    public static function analytics(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://www.google-analytics.com',
                'https://ssl.google-analytics.com',
            ])
            ->addDirective(Directive::IMG, 'https://www.google-analytics.com')
            ->addDirective(Directive::CONNECT, 'https://www.google-analytics.com');
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#google_optimize
     */
    public static function optimize(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, 'https://www.google-analytics.com');
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#google_ads_conversions
     */
    public static function adConversions(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://www.googleadservices.com',
                'https://www.google.com',
            ])
            ->addDirective(Directive::IMG, [
                'https://googleads.g.doubleclick.net',
                'https://www.google.com',
            ]);
    }

    /*
     * https://developers.google.com/tag-manager/web/csp#google_ads_conversions
     */
    public static function adRemarketing(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'https://www.googleadservices.com',
                'https://googleads.g.doubleclick.net',
                'https://www.google.com',
            ])
            ->addDirective(Directive::IMG, 'https://www.google.com')
            ->addDirective(Directive::FRAME, 'https://bid.g.doubleclick.net');
    }
}
