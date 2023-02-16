<?php

namespace Silverstripe\CSP;

abstract class Scheme
{
    public const DATA = 'data:';
    public const HTTP = 'http:';
    public const HTTPS = 'https:';
    public const BLOB = 'blob:';
    public const WS = 'ws:';
}
