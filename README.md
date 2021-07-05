# Silverstripe Content Security Policy

Make your site _like_ super secure with CSP headers and SRI tags on your scripts

## Requirements

* SilverStripe ^4 (Developed against 4.7 no guarantees to older versions)

## Installation
First you install it, then you configure it. Like lego!

```
composer require adrhumphreys/silverstripe-csp
```

## Documentation
First you're going to want to create a **Policy** you'll likely want to extend the **Basic** policy as a good starting ground.

This will look something like:
```php
class ContentSecurityPolicy extends Basic
{
    public function configure(): void
    {
        parent::configure();
    }
}
```

You can then start adding in **fragments**. These are small pieces of code that resemble a CSP for a service. For example YouTube's is:
```php
class YouTube implements Fragment
{
    public static function addTo(Policy $policy): void
    {
        $policy
            ->addDirective(Directive::SCRIPT, [
                'www.youtube.com',
                's.ytimg.com',
            ])
            ->addDirective(Directive::FRAME, "*.youtube.com");
    }
}
```

If you need to add a new fragment or update an existing one please make a pull request to the repo. You've either found a potential issue for all our projects using this, or you're adding a new service which other project can benefit from

Adding fragments to your policy looks like this (in configure func from above):
```php
public function configure(): void
{
    parent::configure();
    $this->addFragments([YouTube::class]);
}
```
_Usually you'll define `private const FRAGMENTS = []` and add them in there so it's clear at the beginning what fragments you're adding._

To set the **report to**, we usually use an env var named `CSP_REPORT_TO`. You can also call `$this->reportTo()` in your policies configure func if required (perhaps you want the report URI based on the policy applied).

To add the policy to the list of applied policies you'll want to add some yaml config:
```yaml
Silverstripe\CSP\CSPMiddleware:
  policies:
    - 'Silverstripe\CSP\Policies\CMS'
    - 'App\ContentSecurityPolicy'

```
In the above we've added it to be checked after the CMS policy that is included by default.

To make the policy **report only** you can either add the env var `CSP_REPORT_ONLY=true` or code it in your policy, for example:
```php
public function configure(): void
{
    parent::configure();
    if (Director::isDev()) {
        $this->reportOnly();
    }
}
```

## SRI
We also support SRI in this module, you can enable this via yaml:
```yaml
Silverstripe\CSP\CSPMiddleware:
  sri_enabled: true
```
This will add SRI hashes to resources added through the requirements. It will not do this to the resources added through `insertHeadTags`. It will also not create this for files that are dynamically created (e.g. tinymce files)

We won't add SRI hashes for external resources, if this is required then you should be adding them in yourself after being provided them by the external resource (we don't do this as we can't control when or how often those are recalculated)

**dev/build** will clear the SRI records (we keep these to ensure we don't generate them per request). This has been added through the `DevBuildExtension`

## Todo list:
- Add the ability to hash inline scripts (this would sit in `CSPBackend`)
- Add Google analytics and tag manager (this will happen shortly)
- Add unit tests

## Maintainers
 * Adrian <adrhumphreys@gmail.com>

## Bugtracker
Bugs are tracked in the issues section of this repository. Before submitting an issue please read over
existing issues to ensure yours is unique.

If the issue does look like a new bug:

 - Create a new issue
 - Describe the steps required to reproduce your issue, and the expected outcome. Unit tests, screenshots
 and screencasts can help here.
 - Describe your environment as detailed as possible: SilverStripe version, Browser, PHP version,
 Operating System, any installed SilverStripe modules.

Please report security issues to the module maintainers directly. Please don't file security issues in the bugtracker.

## Development and contribution
If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

## License
See [License](license.md)
