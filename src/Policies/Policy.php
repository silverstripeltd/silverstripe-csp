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

    public function reportTo(string $uri): self
    {
        $this->directives[Directive::REPORT] = [$uri];
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

        $reportTo = Environment::getEnv('CSP_REPORT_TO');
        if (!array_key_exists(Directive::REPORT, $this->directives) && $reportTo) {
            $this->reportTo($reportTo);
        }

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

        return implode(';', $directives);
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
}
