<?php

namespace Silverstripe\CSP\Requirements;

use SilverStripe\Control\Director;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\ORM\DataObject;

/**
 * @property string File
 * @property string Integrity
 * @property string ModifiedTimec
 */
class SRIRecord extends DataObject
{
    private static string $table_name = 'CSP_SRI';
    private static string $singular_name = 'Subresource integrity record';
    private static string $plural_name = 'Subresource integrity records';

    private static array $db = [
        'File' => 'Varchar(255)',
        'Integrity'  => 'Varchar(255)',
        'ModifiedTime'  => 'Varchar(255)',
    ];

    private static array $summary_fields = [
        'File',
        'LastEdited'
    ];

    private static array $indexes = [
        'File' => true
    ];

    public static function findOrCreate(string $file): ?SRIRecord
    {
        $mTime = self::getFileModifiedTime($file);

        if (!$mTime) {
            return null;
        }

        $record = SRIRecord::get()->filter([
            'File' => $file,
            'ModifiedTime' => $mTime,
        ]);

        if (!$record || !$record->isInDB()) {
            $record = SRIRecord::create(['File' => $file]);
            $record->write();
        }

        return $record;
    }

    private static function getFileModifiedTime(string $file): ?string
    {
        $absolutePath = Director::getAbsFile($file);

        if (!is_file($absolutePath)) {
            return null;
        }

        return filemtime($absolutePath);
    }

    public function hasIntegrity(): bool
    {
        return strlen(trim($this->Integrity ?? '')) > 0;
    }

    /**
     * Generate the SRI for the file on save
     */
    public function onBeforeWrite(): void
    {
        parent::onBeforeWrite();

        // Shouldn't happen but you never know
        if (!$this->File) {
            return;
        }

        $integrity = $this->createIntegrity($this->File);

        $this->Integrity = $integrity;
    }

    public function createIntegrity(string $file): ?string
    {
        // We're not going to set these for files on other servers, the assumption here is that users
        // who require this level of security will have manually added them in using the third parties
        // resource hash rather than ours. (Removing the need for this to worry about updating ones)
        if (!Director::is_site_url($file)) {
            return null;
        }

        $filePath = ModuleResourceLoader::singleton()->resolvePath($file);
        $filePath = Director::getAbsFile($filePath);

        if (!file_exists($filePath)) {
            return null;
        }

        $body = file_get_contents($filePath);

        if (!$body) {
            return null;
        }

        $hash = hash('sha384', $body, true);

        return sprintf('sha384-%s', base64_encode($hash));
    }
}
