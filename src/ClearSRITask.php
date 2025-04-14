<?php

namespace Silverstripe\CSP;

use SilverStripe\Control\HTTPRequest;
use Silverstripe\CSP\Requirements\SRIRecord;
use SilverStripe\Dev\BuildTask;
use SilverStripe\ORM\DataObjectSchema;
use SilverStripe\ORM\DB;
use SilverStripe\PolyExecution\PolyOutput;
use Symfony\Component\Console\Input\InputInterface;

class ClearSRITask extends BuildTask
{
    protected string $title = 'Clear SRI hashes';
    protected static string $description = 'This allow you to reset the tokens incase they change on the server';

    protected function execute(InputInterface $input, PolyOutput $output): int
    {
        $tableName = DataObjectSchema::singleton()->tableName(SRIRecord::class);
        DB::get_conn()->clearTable($tableName);
        echo 'Records cleared';

        return 0;
    }
}
