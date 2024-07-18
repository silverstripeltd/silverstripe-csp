<?php

namespace Silverstripe\CSP\Policies;

use InvalidArgumentException;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Environment;
use SilverStripe\Core\Injector\Injectable;
use Silverstripe\CSP\Directive;
use Silverstripe\CSP\Keyword;
use Silverstripe\CSP\NonceGenerator;
use Silverstripe\CSP\Value;

abstract class Policy
{
    use Injectable;

    protected array $directives = [];

    protected bool $reportOnly = false;

    abstract public function configure();

    /**
     * @param string $directive
     * @param string|array|bool $values
     * @return self
     */
    public function addDirective(string $directive, $values): self
    {
        $this->guardAgainstInvalidDirectives($directive);
        $this->guardAgainstInvalidValues(self::wrap($values));

        if ($values === Value::NO_VALUE) {
            $this->directives[$directive][] = Value::NO_VALUE;

            return $this;
        }

        $values = self::wrap($values);

        if (in_array(Keyword::NONE, $values, true)) {
            $this->directives[$directive] = [$this->sanitizeValue(Keyword::NONE)];

            return $this;
        }

        $this->directives[$directive] = array_filter($this->directives[$directive] ?? [], function ($value) {
            return $value !== $this->sanitizeValue(Keyword::NONE);
        });

        foreach ($values as $value) {
            $sanitizedValue = $this->sanitizeValue($value);

            if (! in_array($sanitizedValue, $this->directives[$directive] ?? [])) {
                $this->directives[$directive][] = $sanitizedValue;
            }
        }

        return $this;
    }

    public function clearDirective(string $directive): self
    {
        $this->guardAgainstInvalidDirectives($directive);
        unset($this->directives[$directive]);

        return $this;
    }

    public function reportOnly(): self
    {
        $this->reportOnly = true;

        return $this;
    }

    public function enforce(): self
    {
        $this->reportOnly = false;

        return $this;
    }

    /**
     * Add reporting directives to the policy, so that violations can be sent to
     * the uri defined as CSP_REPORT_TO in the environment.
     *
     * @param string $uri - the uri to send the reports to, or empty to remove reporting
     * @return self
     */
    public function reportTo(string $uri, string $reportToUri = ''): self
    {
        // if the string is empty, we can assume we need to _remove_ reporting
        if (empty($uri)) {
            unset($this->directives[Directive::REPORT]);
            unset($this->directives[Directive::REPORT_TO]);

            return $this;
        }

        // Add the report-uri directive - this is deprecated, but still supported by most browsers
        $this->directives[Directive::REPORT] = [$reportToUri ?: $uri];

        // Add the report-to directive - this is the new standard, but not yet supported by all browsers
        // the syntax for this will be fixed when the header is added
        $this->directives[Directive::REPORT_TO] = [$uri];

        return $this;
    }

    /*
     * Update this to only apply a policy to specific routes
     */
    public function shouldBeApplied(HTTPRequest $request, HTTPResponse $response): bool
    {
        return true;
    }

    public function addNonceForDirective(string $directive): self
    {
        return $this->addDirective(
            $directive,
            sprintf("'nonce-%s'", NonceGenerator::get())
        );
    }

    /**
     * Apply the CSP header to the response
     *
     * @param HTTPResponse $response
     * @return void
     */
    public function applyTo(HTTPResponse $response)
    {
        $this->configure();

        $reportOnly = Environment::getEnv('CSP_REPORT_ONLY') === 'enabled';

        $headerName = $this->reportOnly || $reportOnly
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        if ($response->getHeader($headerName)) {
            return;
        }

        // optionally add reporting directives
        $this->applyReporting($response);

        $response->addHeader($headerName, (string) $this);
        $response->addHeader('csp-name', ClassInfo::shortName(static::class));
    }

    public function __toString()
    {
        $directives = [];

        foreach ($this->directives as $directive => $values) {
            $valueString = implode(' ', $values);
            $directives[] =
                empty($valueString)
                    ? "{$directive}"
                    : "{$directive} {$valueString}";
        }

        return implode('; ', $directives);
    }

    /*
     * Takes an array of `Fragment` implementations and adds them to the policy
     */
    public function addFragments(array $fragments): self
    {
        foreach ($fragments as $fragment) {
            call_user_func_array([$fragment, 'addTo'], [$this]);
        }

        return $this;
    }

    /**
     * If the given value is not an array and not null, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    public static function wrap($value): array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    protected function guardAgainstInvalidDirectives(string $directive)
    {
        if (!Directive::isValid($directive)) {
            throw new InvalidArgumentException(sprintf(
                'The directive `%s` is not valid in a CSP header.',
                $directive
            ));
        }
    }

    protected function guardAgainstInvalidValues(array $values)
    {
        if (in_array(Keyword::NONE, $values, true) && count($values) > 1) {
            throw new InvalidArgumentException('The keyword none can only be used on its own');
        }
    }

    protected function isHash(string $value): bool
    {
        $acceptableHashingAlgorithms = [
            'sha256-',
            'sha384-',
            'sha512-',
        ];

        foreach ($acceptableHashingAlgorithms as $needle) {
            if ($needle !== '' && strncmp($value, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    protected function isKeyword(string $value): bool
    {
        return in_array($value, Keyword::all());
    }

    protected function sanitizeValue(string $value): string
    {
        if (
            $this->isKeyword($value)
            || $this->isHash($value)
        ) {
            return "'{$value}'";
        }

        return $value;
    }

    /**
     * Add the reporting directives to the policy if the address is set
     * as an environment variable.
     *
     * @param HTTPResponse $response - the response to add the header to
     * @return void
     */
    private function applyReporting(HTTPResponse $response): void
    {
        $reportTo = Environment::getEnv('CSP_REPORT_TO');
        $reportToUri = Environment::getEnv('CSP_REPORT_TO_URI');

        $hasEnvironmentVariable = !is_null($reportTo) && $reportTo !== false;

        // if we have the environment variable, assume we want both directives
        if ($hasEnvironmentVariable) {
            $hasMultipleUrls = str_contains($reportTo, ',');

            // if we are handling multiple urls we need to only add a single directive
            if ($hasMultipleUrls) {
                $reportToArray = explode(',', $reportTo);
                $this->directives[Directive::REPORT_TO] = $reportToArray;
                $this->applyReportTo($response);
                return;
            }

            // otherwise add both
            $this->reportTo($reportTo, $reportToUri);
            $this->applyReportTo($response);
            return;
        }

        // if we don't have the environment variable,
        // check if we have the directives manually set
        $hasReportDirective = array_key_exists(Directive::REPORT, $this->directives);
        $hasReportToDirective = array_key_exists(Directive::REPORT_TO, $this->directives);

        // no directives, no further processing needed
        if (!$hasReportDirective && !$hasReportToDirective) {
            return;
        }

        // if the report-to directive is set, we need to add the header and process the value
        if ($hasReportToDirective) {
            $this->applyReportTo($response);
            return;
        }
    }

    /**
     * Add the necessary extras for the report-to directive
     *
     * @param HTTPResponse $response - the response to add the header to
     * @return void
     */
    private function applyReportTo(HTTPResponse $response): void
    {
        $hasReportToDirective = array_key_exists(Directive::REPORT_TO, $this->directives);

        // if the environment variable is not set, and the directive is not set, we can't add the header
        if (!$hasReportToDirective) {
            return;
        }

        // get the directive value
        $reportTo = $this->directives[Directive::REPORT_TO];

        // if the directive is not set, we can't add the header
        if (is_null($reportTo) || $reportTo === false || $reportTo === '') {
            return;
        }

        $endpoints = [];
        foreach ($reportTo as $uri) {
            // tidy up
            $uri = trim($uri);

            // if the value is not a url, we can't add the header
            if (!filter_var($uri, FILTER_VALIDATE_URL)) {
                continue;
            }

            // if the value is a url, we can use it as the endpoint
            $endpoints[] = [
                'url' => $uri,
            ];
        }

        // if we don't have any endpoints, we can't add the header
        if (count($endpoints) === 0) {
            return;
        }

        // set a standard group name to use
        $groupName = 'csp-endpoint';

        // add the group name to the directive, replacing the invalid urls
        $this->directives[Directive::REPORT_TO] = [$groupName];

        // set the amount of time the users-browser should store the endpoint
        $ttl = Environment::getEnv('CSP_REPORT_TO_TTL') ?: 10886400; // 126 days

        // add the reponse header
        $response->addHeader('Report-To', json_encode([
            'group' => $groupName,
            'max_age' => $ttl,
            'endpoints' => $endpoints,
        ], JSON_UNESCAPED_SLASHES));
    }
}
