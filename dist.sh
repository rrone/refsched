#!/bin/bash

## Exit immediately if a command exits with a non-zero status.
set -e

## clear the screen
#printf "\033c"

echo "  Checkout master branch from Git repository..."
#git checkout master

echo "  Clear distribution folder..."
rm -f -r dist

echo "  Setup distribution folder..."
mkdir dist
mkdir dist/var
mkdir dist/src

echo "  Copying app folders to distribution..."
cp -f -r app dist/app
cp -f -r vendor dist/vendor
cp -f -r public dist/public
cp -f -r templates dist/templates
cp -f -r config dist/config
cp -f -r src/Action dist/src

echo "  Updating index to production..."
mv -f dist/public/app_prod.php dist/public/app.php

echo "  Removing OSX jetsam..."
find dist -type f -name '.DS_Store' -delete
find dist -type f -name 'app_*' -delete

echo "  Copying distribution to local Filezilla root..."
rm -f -r ~/Dropbox/_open/_ayso/s1/web/referee_site/dist
mkdir ~/Dropbox/_open/_ayso/s1/web/referee_site/dist
cp -f -r dist ~/Dropbox/_open/_ayso/s1/web/referee_site

echo "...complete"