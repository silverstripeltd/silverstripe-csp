<?php

namespace Silverstripe\CSP\Requirements;

use Silverstripe\CSP\NonceGenerator;

trait ContentSecurityPolicy
{
    /*
     * We're adding in the nonces to the custom scripts
     */
    public function getCustomScripts(): array
    {
        return $this->addNonceToArray(parent::getCustomScripts());
    }

    public function getCustomCSS(): array
    {
        return $this->addNonceToArray(parent::getCustomCSS());
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
