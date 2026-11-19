<?php

namespace Silverstripe\CSP\Requirements;

use SilverStripe\Dev\Deprecation;
use SilverStripe\View\HTML;
use SilverStripe\View\Requirements_Backend;

/**
 * insertHeadTags is not currently supported, ideally you should not use that method as it's
 * clunky and doesn't provide a clear method for checking for script/style dom nodes
 */
class CSPBackend extends Requirements_Backend
{
    use SubresourceIntegrity;
    use ContentSecurityPolicy;

    /**
     * Attributes given to customScriptWithAttributes(), keyed like the custom scripts.
     * Requirements_Backend keeps its own copy private, so it is tracked here as well.
     */
    private array $customScriptTagAttributes = [];

    /**
     * @inheritDoc
     */
    public function customScript($script, $uniquenessID = null)
    {
        if ($uniquenessID) {
            unset($this->customScriptTagAttributes[$uniquenessID]);
        }
        parent::customScript($script, $uniquenessID);
    }

    /**
     * @inheritDoc
     */
    public function customScriptWithAttributes(
        string $script,
        array $attributes = [],
        string|int|null $uniquenessID = null
    ) {
        parent::customScriptWithAttributes($script, $attributes, $uniquenessID);
        $key = $uniquenessID ?: array_key_last($this->customScript);
        $this->customScriptTagAttributes[$key] = $attributes;
    }

    /**
     * Good old monolithic functions...
     * EDITED: A comment should sit above anything changed mentioning what changed and why
     *         it will start with `EDITED`
     *
     * @inheritDoc
     */
    public function includeInHTML($content)
    {
        if (func_num_args() > 1) {
            Deprecation::notice(
                '5.0',
                '$templateFile argument is deprecated. includeInHTML takes a sole $content parameter now.'
            );
            $content = func_get_arg(1);
        }

        // Skip if content isn't injectable, or there is nothing to inject
        $tagsAvailable = preg_match('#</head\b#', $content);
        $hasFiles = $this->css || $this->javascript || $this->customCSS || $this->customScript || $this->customHeadTags;
        if (!$tagsAvailable || !$hasFiles) {
            return $content;
        }
        $requirements = '';
        $jsRequirements = '';

        // Combine files - updates $this->javascript and $this->css
        $this->processCombinedFiles();

        // Script tags for js links
        foreach ($this->getJavascript() as $file => $attributes) {
            // Build html attributes
            $htmlAttributes = [
                'type' => isset($attributes['type']) ? $attributes['type'] : "application/javascript",
                'src' => $this->pathForFile($file),
            ];
            if (!empty($attributes['async'])) {
                $htmlAttributes['async'] = 'async';
            }
            if (!empty($attributes['defer'])) {
                $htmlAttributes['defer'] = 'defer';
            }
            if (!empty($attributes['integrity'])) {
                $htmlAttributes['integrity'] = $attributes['integrity'];
            }
            if (!empty($attributes['crossorigin'])) {
                $htmlAttributes['crossorigin'] = $attributes['crossorigin'];
            }
            $jsRequirements .= HTML::createTag('script', $htmlAttributes);
            $jsRequirements .= "\n";
        }

        // Add all inline JavaScript *after* including external files they might rely on
        foreach ($this->getCustomScriptsWithNonce() as $key => $script) {
            $src = $script['src'];
            /**
             * EDITED: We added in the nonce here, on top of the attributes given to
             *         customScriptWithAttributes(), with a default type
             *         @see CSPBackend::getCustomScriptsWithNonce
             */
            $htmlAttributes = array_merge(
                ['type' => 'application/javascript'],
                $this->customScriptTagAttributes[$key] ?? []
            );
            $htmlAttributes['nonce'] = $script['nonce'];
            $jsRequirements .= HTML::createTag(
                'script',
                $htmlAttributes,
                /**
                 * EDITED: We added in the script src here (was just `$script` before)
                 *         @see CSPBackend::getCustomScriptsWithNonce
                 */
                "//<![CDATA[\n{$src}\n//]]>"
            );
            $jsRequirements .= "\n";
        }

        // CSS file links
        foreach ($this->getCSS() as $file => $params) {
            $htmlAttributes = [
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => $this->pathForFile($file),
            ];
            if (!empty($params['media'])) {
                $htmlAttributes['media'] = $params['media'];
            }
            if (!empty($params['integrity'])) {
                $htmlAttributes['integrity'] = $params['integrity'];
            }
            if (!empty($params['crossorigin'])) {
                $htmlAttributes['crossorigin'] = $params['crossorigin'];
            }
            $requirements .= HTML::createTag('link', $htmlAttributes);
            $requirements .= "\n";
        }

        // Literal custom CSS content
        foreach ($this->getCustomCSSWithNonce() as $css) {
            /**
             * EDITED: We added in the script src (was just `$css` before) and nonce
             *         @see CSPBackend::getCustomCSSWithNonce
             */
            $requirements .= HTML::createTag(
                'style',
                [
                    'type' => 'text/css',
                    'nonce' => $css['nonce'],
                ],
                "\n{$css['src']}\n"
            );
            $requirements .= "\n";
        }

        foreach ($this->getCustomHeadTags() as $customHeadTag) {
            $requirements .= "{$customHeadTag}\n";
        }

        // Inject CSS  into body
        $content = $this->insertTagsIntoHead($requirements, $content);

        // Inject scripts
        if ($this->getForceJSToBottom()) {
            $content = $this->insertScriptsAtBottom($jsRequirements, $content);
        } elseif ($this->getWriteJavascriptToBody()) {
            $content = $this->insertScriptsIntoBody($jsRequirements, $content);
        } else {
            $content = $this->insertTagsIntoHead($jsRequirements, $content);
        }
        return $content;
    }
}
