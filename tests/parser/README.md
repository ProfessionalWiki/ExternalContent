# Parser Tests

This directory contains parser tests for the ExternalContent extension.

## Version Compatibility

Due to changes in MediaWiki's Codex message box implementation between versions, the HTML output differs slightly:

- **MediaWiki 1.43**: `<div class="cdx-message cdx-message--block cdx-message--error">`
- **MediaWiki 1.44+**: `<div class="cdx-message--error cdx-message cdx-message--block">`

The difference is only in CSS class ordering, which does not affect functionality or visual appearance.

## Test Files

- `parserTests.txt` - Tests for MediaWiki 1.44 and later (current format)
- `parserTests-mw143.txt` - Tests for MediaWiki 1.43 (legacy format)

## Running Tests

### Using Make (recommended)

For MediaWiki 1.44+:
```bash
make parser
```

For MediaWiki 1.43:
```bash
make parser-mw143
```

### Direct PHP execution

For MediaWiki 1.44+:
```bash
php ../../tests/parser/parserTests.php --file=tests/parser/parserTests.txt
```

For MediaWiki 1.43:
```bash
php ../../tests/parser/parserTests.php --file=tests/parser/parserTests-mw143.txt
```

## CI/CD Configuration

Update your GitHub Actions or CI configuration to run the appropriate test file based on the MediaWiki version:

```yaml
- name: Run parser tests
  run: |
    if [[ "${{ matrix.mw }}" == "REL1_43" ]]; then
      php tests/parser/parserTests.php --file=extensions/ExternalContent/tests/parser/parserTests-mw143.txt
    else
      php tests/parser/parserTests.php --file=extensions/ExternalContent/tests/parser/parserTests.txt
    fi
```

## Alternative Approach

If you prefer to maintain only one test file, you can use less strict assertions in your PHPUnit integration tests. See `tests/Unit/Adapters/ParserFunctionEmbedPresenterTest.php` for an example that uses `logicalOr` to accept multiple possible outputs:

```php
$this->assertThat(
    $parserFunctionReturnValue[0],
    $this->logicalOr(
        $this->stringContains( '<div class="errorbox">' ),
        $this->stringContains( 'mw-message-box-error' ),
        $this->stringContains( 'cdx-message--error' )
    )
);
```
