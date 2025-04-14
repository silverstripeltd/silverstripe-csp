<?php

namespace Silverstripe\CSP\Requirements;

use SilverStripe\Core\Config\Configurable;
use SilverStripe\ORM\DataObjectSchema;
use SilverStripe\ORM\DB;
use SilverStripe\Core\Extension;

class DevBuildExtension extends Extension
{
    use Configurable;

    private static bool $enabled = true;

    private static bool $done = false;

    public function onAfterBuild(): void
    {
        if (!static::config()->get('enabled')) {
            return;
        }

        if (!self::$done) {
            $tableName = DataObjectSchema::singleton()->tableName(SRIRecord::class);
            DB::get_conn()->clearTable($tableName);
            self::$done = true;
        }
    }
}
