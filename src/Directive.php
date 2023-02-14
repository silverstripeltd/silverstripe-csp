<?php

namespace Silverstripe\CSP;

use ReflectionClass;

abstract class Directive
{
    public const BASE = 'base-uri';
    public const BLOCK_ALL_MIXED_CONTENT = 'block-all-mixed-content';
    public const CHILD = 'child-src';
    public const CONNECT = 'connect-src';
    public const DEFAULT = 'default-src';
    public const FONT = 'font-src';
    public const FORM_ACTION = 'form-action';
    public const FRAME = 'frame-src';
    public const FRAME_ANCESTORS = 'frame-ancestors';
    public const IMG = 'img-src';
    public const MANIFEST = 'manifest-src';
    public const MEDIA = 'media-src';
    public const OBJECT = 'object-src';
    public const PLUGIN = 'plugin-types';
    public const PREFETCH = 'prefetch-src';
    public const REPORT = 'report-uri';
    public const REPORT_TO = 'report-to';
    public const SANDBOX = 'sandbox';
    public const SCRIPT = 'script-src';
    public const SCRIPT_ATTR = 'script-src-attr';
    public const SCRIPT_ELEM = 'script-src-elem';
    public const STYLE = 'style-src';
    public const STYLE_ATTR = 'style-src-attr';
    public const STYLE_ELEM = 'style-src-elem';
    public const UPGRADE_INSECURE_REQUESTS = 'upgrade-insecure-requests';
    public const WEB_RTC = 'webrtc-src';
    public const WORKER = 'worker-src';

    public static function isValid(string $directive): bool
    {
        $constants = (new ReflectionClass(static::class))->getConstants();

        return in_array($directive, $constants);
    }
}
