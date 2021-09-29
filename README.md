# External Content

[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ProfessionalWiki/ExternalContent/CI)](https://github.com/ProfessionalWiki/ExternalContent/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/ProfessionalWiki/ExternalContent/branch/master/graph/badge.svg?token=GnOG3FF16Z)](https://codecov.io/gh/ProfessionalWiki/ExternalContent)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/ExternalContent/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/ExternalContent)
[![Psalm level](https://shepherd.dev/github/ProfessionalWiki/ExternalContent/level.svg)](psalm.xml)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/external-content/version.png)](https://packagist.org/packages/professional-wiki/external-content)
[![Download count](https://poser.pugx.org/professional-wiki/external-content/d/total.png)](https://packagist.org/packages/professional-wiki/external-content)

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

Configuration can be changed via "LocalSettings.php".

### Domain whitelist

List of allowed domains to embed content from. Leave empty to have no restriction.

Variable: `$wgExternalContentDomainWhitelist`

Default: `[]`

Example: `[ 'git.example.com', 'another.example.com' ]`

### File extension whitelist

List of allowed file extensions. Leave empty to have no restriction.

Variable: `$wgExternalContentDomainWhitelist`

Default: `[ 'md' ]`

Example: `[ 'md', 'txt' ]`

Caution: The extension currently only supports markdown: any retrieved file content will be rendered ask markdown.

### Enable embed function

If the `#embed` parser function should be enabled.

Variable: `$wgExternalContentEnableEmbedFunction`

Default: `true`

Example: `false` - disables the `#embed` parser function

### Enable bitbucket function

If the `#bitbucket` parser function should be enabled.

Variable: `$wgExternalContentEnableBitbucketFunction`

Default: `true`

Example: `false` - disables the `#bitbucket` parser function

### Basic Auth credentials

Per-domain Basic Auth credentials.

Variable: `$wgExternalContentBasicAuthCredentials`

Default: `[]`

Example:
```php
$wgExternalContentBasicAuthCredentials = [
	'git.example.com' => [ 'ExampleUser', 'ExamplePassword' ],
	'another.example.com' => [ getenv( 'BITBUCKET_USER' ), getenv( 'BITBUCKET_PASSWORD' ) ]
];
```

The above example shows how you can get credentials from ENV vars, which might be preferred over 
storing them as plaintext in LocalSettings.php.

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

## Release notes

### Version 1.0.0 - 2021-09-30

Initial release for MediaWiki 1.35+ with these features:

* Embedding of markdown files via `#embed` parser function
* Special support for Bitbucket URLs via the` #bitbucket` parser function
* Restricting of source domains via the `$wgExternalContentDomainWhitelist` setting
* Restricting of file extensions via the `$wgExternalContentDomainWhitelist` setting
* Support for Basic Auth via the `$wgExternalContentBasicAuthCredentials` setting
* Ability to turn off `#embed` via the `$wgExternalContentEnableEmbedFunction` setting
* Ability to turn off `#bitbucket` via the `$wgExternalContentEnableBitbucketFunction` setting
* Ability to refresh all embedded content via the `RefreshExternalContent.php` maintenance script
* Ability to view pages with embedded content via the `Pages with external content` category
* Ability to view pages with broken embedded content via the `Pages with broken external content` category
