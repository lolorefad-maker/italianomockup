// Builds theme/style.css = theme header + base.css (mockup design) + icon variables + wp.css (block mapping).
// Usage: node tools/build-css.js [path-to-mockup-parts]   (the parts path is only needed the first time)
const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const assets = path.join(root, 'theme', 'assets');
const basePath = path.join(assets, 'base.css');

if (process.argv[2]) {
  const parts = ['01-head.html', '02-style.html', '03-style.html'].map(f => fs.readFileSync(path.join(process.argv[2], f), 'utf8'));
  const css = parts.map(s => [...s.matchAll(/<style>([\s\S]*?)<\/style>/g)].map(m => m[1]).join('\n')).join('\n');
  fs.writeFileSync(basePath, css.trim() + '\n');
}

const icons = JSON.parse(fs.readFileSync(path.join(assets, 'icons.json'), 'utf8'));
const want = ['arrow', 'wa', 'check', 'checkC', 'doc', 'seal', 'headset', 'bubbles', 'book', 'quote', 'phone', 'mail', 'pin', 'clock', 'lock', 'camera'];
const vars = want.map(n => {
  const svg = icons[n].replace(/ class="[^"]*"/, ' xmlns="http://www.w3.org/2000/svg"').replace(/ aria-hidden="true"/, '');
  return `--i-${n}:url("data:image/svg+xml,${encodeURIComponent(svg).replace(/'/g, '%27')}")`;
}).join(';');

const header = `/*
Theme Name: Traduttore
Description: Tema bilingue (italiano / arabo, RTL) per traduttore e interprete arabo–italiano. Contenuti modificabili con l'editor a blocchi.
Version: 1.0.0
Requires at least: 6.4
Requires PHP: 8.0
Text Domain: traduttore
*/
`;

const out = header + fs.readFileSync(basePath, 'utf8') + `\n:root{${vars}}\n` + fs.readFileSync(path.join(assets, 'wp.css'), 'utf8');
fs.writeFileSync(path.join(root, 'theme', 'style.css'), out);
console.log('style.css', out.length, 'bytes');
