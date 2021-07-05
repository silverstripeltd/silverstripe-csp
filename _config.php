<?php

use SilverStripe\Core\Injector\Injector;
use Silverstripe\CSP\Requirements\CSPBackend;
use SilverStripe\View\Requirements;

$backend = Injector::inst()->get(CSPBackend::class);
Requirements::set_backend($backend);
