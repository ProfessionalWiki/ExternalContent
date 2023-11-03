# External Content

[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/ProfessionalWiki/ExternalContent/ci.yml?branch=master)](https://github.com/ProfessionalWiki/ExternalContent/actions?query=workflow%3ACI)
[![codecov](https://codecov.io/gh/ProfessionalWiki/ExternalContent/branch/master/graph/badge.svg?token=GnOG3FF16Z)](https://codecov.io/gh/ProfessionalWiki/ExternalContent)
[![Type Coverage](https://shepherd.dev/github/ProfessionalWiki/ExternalContent/coverage.svg)](https://shepherd.dev/github/ProfessionalWiki/ExternalContent)
[![Psalm level](https://shepherd.dev/github/ProfessionalWiki/ExternalContent/level.svg)](psalm.xml)
[![Latest Stable Version](https://poser.pugx.org/professional-wiki/external-content/version.png)](https://packagist.org/packages/professional-wiki/external-content)
[![Download count](https://poser.pugx.org/professional-wiki/external-content/d/total.png)](https://packagist.org/packages/professional-wiki/external-content)
[![License](https://img.shields.io/packagist/l/professional-wiki/external-content)](LICENSE)

[MediaWiki] extension that allows embedding external content, specified by URL, into your wiki pages.

External Content has been created and is maintained by [Professional Wiki].

- [Usage](#usage)
- [Installation](#installation)
- [Configuration](#configuration)
- [Development](#development)
- [Release notes](#release-notes)

## Usage

<a href="https://www.youtube.com/watch?v=U65NhBwnCMU"><img src=".github/youtube.png" width="430px" title="Play video" /></a>

### Embedding external content

External content can be embedded via the `#embed` [parser function]. This function takes a URL.
Markdown and code syntax highlighting are supported. To render markdown, no additional parameters are needed. To use code
syntax highlighting, refer to the [parameters](#code-syntax-highlighting-parameters) below.

Example:

```
{{#embed:https://example.com/fluffy/kittens.md}}
```

There is special handling for GitHub URLs, removing the need to provide the raw file URL:

* github.com/org/repo/blob/master/hi.md => raw.githubusercontent.com/org/repo/master/hi.md
* github.com/org/repo/tree/master/src => defaults to README.md in the directory
* github.com/org/repo => defaults to the README.md in the repository root on the `master` branch

### Embedding Bitbucket content

Content from Bitbucket can be embedded via the `#bitbucket` [parser function].

This function takes a URL and includes the following Bitbucket-specific behavior:
* Validation that the URL matches the Bitbucket repository structure
* `/browse` URLs are automatically turned into `/raw` URLs
* Pointing to the repository root will automatically retrieve `README.md`

Example:

```
{{#bitbucket:https://git.example.com/projects/HI/repos/cats/browse}}
{{#bitbucket:https://git.example.com/projects/HI/repos/cats/raw/README.md?at=refs%2Fheads%2Fmaster}}
```

### Display parameters

Both `#embed` and `#bitbucket` can be customized with these parameters:

* `lang`: (Optional) One of the [supported languages]. Only necessary if the language is not detected from the file extension.
* `lineNumbers`: (Optional) Show line numbers.
* `showLines`: (Optional) Show only specific lines. It can be a single line number or a range separated with a hyphen (-). Multiple line numbers or ranges can be separated by commas.
* `render`: (Optional) render Markdown (this is the default behavior unless `$wgExternalContentRenderMarkdownByDefault` is changed)

Examples:

Show Markdown file contents in a code block:
```
{{#embed:https://example.com/fluffy/kittens.md|lang=markdown}}
```

Show code block with line numbers:
```
{{#embed:https://example.com/fluffy/kittens.php|lang=php|line}}
```

Show only specific lines in a code block:
```
{{#embed:https://example.com/fluffy/kittens.php|lang=php|showLines=1-3,8}}
```

Render file as Markdown:
```
{{#embed:https://example.com/fluffy/kittens.php|render}}
```

### Refreshing external content

To refresh all the pages containing one of the parser functions added by this extension, run

    php extensions/ExternalContent/maintenance/RefreshExternalContent.php

Parameters: none

## Installation

Platform requirements:

* [PHP] 8.0 or later (tested up to 8.1)
* [MediaWiki] 1.35 or later (tested up to 1.39 and master)

The recommended way to install External Content is using [Composer] with
[MediaWiki's built-in support for Composer][Composer install].

On the commandline, go to your wikis root directory. Then run these two commands:

```shell script
COMPOSER=composer.local.json composer require --no-update professional-wiki/external-content:~2.0
```
```shell script
composer update professional-wiki/external-content --no-dev -o
```

Then enable the extension by adding the following to the bottom of your wikis [LocalSettings.php] file:

```php
wfLoadExtension( 'ExternalContent' );
```

You can verify the extension was enabled successfully by opening your wiki's Special:Version page in your browser.

## Configuration

Configuration can be changed via [LocalSettings.php].

### Rendering markdown

By default, markdown is rendered rather than shown in a code block.

Variable: `$wgExternalContentRenderMarkdownByDefault`

Default: `true` - markdown is rendered (unless otherwise specified by the user)

Example: `false` - markdown is shown as a code block (unless otherwise specified by the user)

### Domain whitelist

List of allowed domains to embed content from. Leave empty to have no restriction.

Variable: `$wgExternalContentDomainWhitelist`

Default: `[]`

Example: `[ 'git.example.com', 'another.example.com' ]`

### File extension whitelist

List of allowed file extensions. Leave empty to have no restriction.

Variable: `$wgExternalContentFileExtensionWhitelist`

Default: `[]`

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
storing them as plaintext in [LocalSettings.php].

### Connection details

The content of files is fetched via MediaWiki's native HTTP client. This process is affected by
various [HTTP client variables](https://www.mediawiki.org/wiki/Category:HTTP_client_variables).

### Search

In stock MediaWiki with no extensions, embedded content is not searchable. To make embedded content
show up in search results, install Elasticseach and the [CirrusSearch extension].

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

### Version 2.0.1 - 2023-11-02

* Fixed behavior of copy button for code blocks with line numbers and/or only specific lines shown
* Improved display of long lines in code blocks by adding line wrapping

### Version 2.0.0 - 2023-10-30

* Raised minimum PHP version from 7.4 to 8.0
* Added code syntax highlighting
    * Added `lang` parameter
    * Added `lineNumbers` parameter
    * Added `showLines` parameter
    * Added `render` parameter
    * Added copy button to code blocks
    * Added edit link to Bitbucket code blocks

### Version 1.3.0 - 2022-01-08

* Improved handling of relative links. They now point to the "browse" version when embedding using a "browse" URL,
  rather than using the "raw" version.

### Version 1.2.0 - 2021-12-02

* Added support for [extended syntax markdown](https://www.markdownguide.org/extended-syntax/)

### Version 1.1.0 - 2021-11-01

* Added normalization for github.com URLs to the `#embed` parser function

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

[Professional Wiki]: https://professional.wiki/en/mediawiki-development
[MediaWiki]: https://www.mediawiki.org
[PHP]: https://www.php.net
[Composer]: https://getcomposer.org
[Composer install]: https://professional.wiki/en/articles/installing-mediawiki-extensions-with-composer
[parser function]: https://www.mediawiki.org/wiki/Help:Magic_words
[LocalSettings.php]: https://www.mediawiki.org/wiki/Manual:LocalSettings.php
[CirrusSearch extension]: https://www.mediawiki.org/wiki/Extension:CirrusSearch
[supported languages]: https://prismjs.com/#supported-languages
