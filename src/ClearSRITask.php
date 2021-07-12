<?php

namespace Silverstripe\CSP;

use SilverStripe\Control\HTTPRequest;
use Silverstripe\CSP\Requirements\SRIRecord;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataObjectSchema;
use SilverStripe\ORM\DB;

class ClearSRITask extends BuildTask
{
    protected $title = 'Clear SRI hashes';
    protected $description = 'This allow you to reset the tokens incase they change on the server';

    /**
     * @param HTTPRequest|mixed $request
     */
    public function run($request): void
    {
        $tableName = DataObjectSchema::singleton()->tableName(SRIRecord::class);
        DB::get_conn()->clearTable($tableName);
        echo 'Records cleared';
    }
}
