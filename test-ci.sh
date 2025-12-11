#!/bin/bash
# Helper script to test CI workflow locally with act
# Usage: ./test-ci.sh [job-name] [mw-version] [php-version]
# e.g. act -j test --matrix mw:REL1_44 --matrix php:8.4 --matrix experimental:false -v

set -e

# Verify act is installed
if ! command -v act &> /dev/null; then
  echo "Error: 'act' is not installed. See: https://github.com/nektos/act"
  exit 1
fi

JOB="${1:-test}"
MW_VERSION="${2:-REL1_43}"
PHP_VERSION="${3:-8.3}"

echo "Testing job: $JOB with MediaWiki $MW_VERSION and PHP $PHP_VERSION"

case "$JOB" in
    test)
        act -j test \
            --matrix mw:"$MW_VERSION" \
            --matrix php:"$PHP_VERSION" \
            --matrix experimental:false \
            --verbose
        ;;
    static-analysis)
        act -j static-analysis --verbose
        ;;
    code-style)
        act -j code-style --verbose
        ;;
    all)
        echo "Running all jobs with $MW_VERSION..."
        act -j static-analysis --verbose
        act -j code-style --verbose
        act -j test --matrix mw:"$MW_VERSION" --matrix php:"$PHP_VERSION" --matrix experimental:false --verbose
        ;;
    list)
        echo "Available jobs:"
        act -l
        ;;
    *)
        echo "Unknown job: $JOB"
        echo "Available jobs: test, static-analysis, code-style, all, list"
        exit 1
        ;;
esac
