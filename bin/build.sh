#!/usr/bin/env bash

set -e

# Extract version from tag
if [ -z "$1" ]; then
  echo "Error: No tag provided"
  exit 1
fi

TAG_NAME=$1
TAG_NAME=${TAG_NAME#refs/tags/}
VERSION=${TAG_NAME#v}

echo "Building version: ${VERSION} from tag: ${TAG_NAME}"

# Build files
composer install --no-dev --prefer-dist

# NPM packages.
npm install
npm run package

# Make Readme
echo 'Generate readme.'
curl -L https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php | php

# Change version string.
echo "Updating version to ${VERSION}"
sed -i.bak "s/\* Version: .*/\* Version: ${VERSION}/g" ./anyway-feedback.php
sed -i.bak "s/^Stable Tag: .*/Stable Tag: ${VERSION}/g" ./readme.txt

# Clean up backup files
rm -f ./anyway-feedback.php.bak ./readme.txt.bak
