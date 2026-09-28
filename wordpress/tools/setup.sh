#!/bin/sh
# One-time local setup: WordPress + languages + plugins + theme + bilingual content.
# Usage (from Git Bash):  sh tools/setup.sh
set -e
cd "$(dirname "$0")/.."
export MSYS_NO_PATHCONV=1
. ./.env
WP="docker compose run --rm -T cli"

echo "Waiting for WordPress files..."
for i in $(seq 1 40); do
  if $WP core version >/dev/null 2>&1; then break; fi
  sleep 3
done

if ! $WP core is-installed >/dev/null 2>&1; then
  $WP core install --url=http://localhost:8088 --title="Nome Cognome" \
    --admin_user="$WP_ADMIN_USER" --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email=admin@nomecognome.test --skip-email
fi

retry() { for n in 1 2 3 4; do "$@" && return 0; echo "retry $n: $*"; sleep 4; done; return 1; }

$WP rewrite structure '/%postname%/' --hard
retry $WP language core install it_IT ar
$WP site switch-language it_IT
retry $WP plugin install polylang contact-form-7 seo-by-rank-math --activate
for p in polylang contact-form-7 seo-by-rank-math; do retry $WP language plugin install "$p" it_IT ar || true; done
$WP plugin delete akismet hello >/dev/null 2>&1 || true
$WP theme activate traduttore
$WP user meta update "$WP_ADMIN_USER" locale ar
$WP option update rank_math_registration_skip 1
$WP option update rank_math_wizard_completed 1
$WP eval-file /seed/seed.php
$WP rewrite flush --hard
echo "Done: http://localhost:8088"
