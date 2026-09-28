#!/bin/sh
# Rebuilds ../index.html from 01-head.html (styles) + 02-app.js (page) and the shared content
# in ../wordpress/seed/content.json and ../wordpress/theme/assets/icons.json.
# Usage:  sh mockup/build.sh   (needs Node.js)
set -e
D="$(cd "$(dirname "$0")" && pwd)"
R="$D/.."
{ echo "const DATA="; cat "$R/wordpress/seed/content.json"; echo ";const ICONS="; cat "$R/wordpress/theme/assets/icons.json"; echo ";"; cat "$D/02-app.js"; } > "$D/app.bundle.js"
node --check "$D/app.bundle.js"
{
  printf '<!doctype html>\n<html lang="it">\n<head>\n<meta charset="utf-8">\n'
  printf '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">\n'
  printf '<meta name="robots" content="noindex">\n'
  printf '<meta name="description" content="Concept della home page: traduttore e interprete arabo – italiano.">\n'
  printf '<meta property="og:title" content="Traduttore e interprete arabo – italiano · concept">\n'
  printf '<meta property="og:description" content="Home page in italiano e arabo · scegli il colore">\n'
  printf '<meta name="theme-color" content="#0B0F17">\n'
  cat "$D/01-head.html"
  printf '</head>\n<body>\n<div id="app"></div>\n<script>\n'
  cat "$D/app.bundle.js"
  printf '\n</script>\n</body>\n</html>\n'
} > "$R/index.html"
rm "$D/app.bundle.js"
echo "index.html: $(wc -c < "$R/index.html") bytes"
