#!/usr/bin/env bash
# Wrap the self-contained app fragment (SRC) into a standalone PWA index.html.
# SRC stays the single source of truth (also publishable as a claude.ai Artifact).
set -uo pipefail
root="$(git rev-parse --show-toplevel 2>/dev/null)" || exit 0
cd "$root" || exit 0

# ---------------- CONFIG ----------------
SRC="app.html"
TITLE="Multi Tracker"
THEME_LIGHT="#17915A"
THEME_DARK="#111311"
BG_LIGHT="#F4F4F1"
BG_DARK="#111311"
# ----------------------------------------
[ -f "$SRC" ] || exit 0

{
cat <<HEAD
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>${TITLE}</title>
<meta name="theme-color" content="${THEME_LIGHT}" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="${THEME_DARK}" media="(prefers-color-scheme: dark)">
<link rel="manifest" href="./manifest.webmanifest">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="${TITLE}">
<link rel="apple-touch-icon" href="./icon-180.png">
<link rel="icon" type="image/svg+xml" href="./icon.svg">
<link rel="icon" type="image/png" sizes="192x192" href="./icon-192.png">
<style>html,body{margin:0;background:${BG_LIGHT}}@media(prefers-color-scheme:dark){html,body{background:${BG_DARK}}}</style>
</head>
<body>
<!-- Generated from ${SRC} — edit that file, not this one. -->
HEAD
cat "$SRC"
cat <<'TAIL'
<script>
if ('serviceWorker' in navigator) {
  var __refreshing = false;
  navigator.serviceWorker.addEventListener('controllerchange', function () {
    if (__refreshing) return; __refreshing = true; location.reload();
  });
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('./sw.js').then(function (reg) {
      reg.update();
      setInterval(function () { reg.update(); }, 30 * 60 * 1000);
    }).catch(function () {});
  });
  document.addEventListener('visibilitychange', function () {
    if (document.visibilityState === 'visible' && navigator.serviceWorker.controller) {
      navigator.serviceWorker.getRegistration().then(function (r) { if (r) r.update(); });
    }
  });
}
</script>
</body>
</html>
TAIL
} > index.html
