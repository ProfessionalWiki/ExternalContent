# External Content

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ProfessionalWiki/ExternalContent/CI)](https://github.com/ProfessionalWiki/ExternalContent/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/ProfessionalWiki/ExternalContent/branch/master/graph/badge.svg?token=GnOG3FF16Z)](https://codecov.io/gh/ProfessionalWiki/ExternalContent)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/ExternalContent/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/ExternalContent)
[![Psalm level](https://shepherd.dev/github/ProfessionalWiki/ExternalContent/level.svg)](psalm.xml)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/external-content/version.png)](https://packagist.org/packages/professional-wiki/external-content)
[![Download count](https://poser.pugx.org/professional-wiki/external-content/d/total.png)](https://packagist.org/packages/professional-wiki/external-content)
[![License](https://img.shields.io/packagist/l/professional-wiki/external-content)](LICENSE)

MediaWiki extension that allows embedding external content, specified by URL, into your wiki pages.

THIS EXTENSION IS UNDER DEVELOPMENT AND NOT READY FOR USAGE

## Usage

### `#embed` function

Embed a file by URL. Currently only markdown is supported. 

Example: 

```
{{#embed:https://example.com/fluffy/kittens.md}}
```

### `#bitbucket` function

Embed a Bitbucket hosted file by URL. Currently only markdown is supported.

Only valid Bitbucket URLs are accepted. Pointing to a repository root will get you `README.md`. 

Example:

```
{{#bitbucket:https://git.example.com/projects/HI/repos/kittens/browse}}
```

### `RefreshExternalContent.php` script

To refresh all the pages containing one of the parser functions added by this extension, run

    php extensions/ExternalContent/maintenance/RefreshExternalContent.php

Parameters: none

## Configuration

These configuration settings are available and can be changed via "LocalSettings.php":

* `$wgExternalContentDomainWhitelist` – List of allowed domains to embed content from. Leave empty to have no restriction.
* `$wgExternalContentEnableEmbedFunction` - If the `#embed` parser function should be enabled. Defaults to `true`.
* `$wgExternalContentEnableBitbucketFunction` - If the `#bitbucket` parser function should be enabled. Defaults to `true`.

## Installation

Platform requirements:

* PHP 7.4 or later
* MediaWiki 1.35 or later

The recommended way to install External Content is using [Composer](https://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer).

On the commandline, go to your wikis root directory. Then run these two commands:

```shell script
COMPOSER=composer.local.json composer require --no-update professional-wiki/external-content:~1.0
composer update professional-wiki/external-content --no-dev -o
```

Then enable the extension by adding the following to the bottom of your wikis `LocalSettings.php` file:

```php
wfLoadExtension( 'ExternalContent' );
```

You can verify the extension was enabled successfully by opening your wikis Special:Version page in your browser.

## Development

To ensure the dev dependencies get installed, have this in your `composer.local.json`:

```json
{
	"require": {
		"vimeo/psalm": "^4.10",
		"phpstan/phpstan": "^0.12.99"
	},
	"extra": {
		"merge-plugin": {
			"include": [
				"extensions/ExternalContent/composer.json"
			]
		}
	}
}
```

### Running tests and CI checks

You can use the `Makefile` by running make commands in the `ExternalContent` directory.

* `make ci`: Run everything
* `make test`: Run all tests
* `make cs`: Run all style checks and static analysis

Alternatively, you can execute commands from the MediaWiki root directory:

* PHPUnit: `php tests/phpunit/phpunit.php -c extensions/ExternalContent/`
* Style checks: `vendor/bin/phpcs -p -s --standard=extensions/ExternalContent/phpcs.xml`
* PHPStan: `vendor/bin/phpstan analyse --configuration=extensions/ExternalContent/phpstan.neon --memory-limit=2G`
* Psalm: `php vendor/bin/psalm --config=extensions/ExternalContent/psalm.xml`
