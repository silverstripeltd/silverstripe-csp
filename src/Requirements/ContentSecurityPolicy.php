<?php

namespace Silverstripe\CSP\Requirements;

use Silverstripe\CSP\Nonce\NonceGenerator;

trait ContentSecurityPolicy
{
    /**
     * The custom scripts with the nonce they are written with, as `['nonce' => ..., 'src' => ...]`
     * arrays. getCustomScripts() keeps the framework's return type (script strings).
     */
    public function getCustomScriptsWithNonce(): array
    {
        return $this->addNonceToArray($this->getCustomScripts());
    }

    /**
     * The custom CSS with the nonce it is written with, as `['nonce' => ..., 'src' => ...]`
     * arrays. getCustomCSS() keeps the framework's return type (CSS strings).
     */
    public function getCustomCSSWithNonce(): array
    {
        return $this->addNonceToArray($this->getCustomCSS());
    }

    private function addNonceToArray(array $sources): array
    {
        $results = [];
        $nonce = NonceGenerator::get();

        foreach ($sources as $id => $source) {
            $results[$id] = [
                'nonce' => $nonce,
                'src' => $source,
            ];
        }

        return $results;
    }
}
