<?php

namespace Silverstripe\CSP\Requirements;

trait SubresourceIntegrity
{
    public function getJavascript(): array
    {
        return $this->addIntegrityToArray(parent::getJavascript());
    }

    public function getCSS(): array
    {
        return $this->addIntegrityToArray(parent::getCSS());
    }

    private function addIntegrityToArray(array $scripts): array
    {
        $results = [];

        foreach ($scripts as $id => $script) {
            $sri = SRIRecord::findOrCreate($id);

            if ($sri && $sri->hasIntegrity()) {
                $script['integrity'] = $script['integrity'] ?? $sri->Integrity;
            }

            $results[$id] = $script;
        }

        return $results;
    }
}
