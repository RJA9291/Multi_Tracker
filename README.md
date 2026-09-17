# Multi Tracker

A self-contained, installable habit tracker PWA. First tab: **Habit** — a monthly check-grid,
overall stats, per-habit analysis, top habits, and mood + sleep charts. More tabs planned
(Work Task, Fitness, Finance, Diet, Sleep).

## Live app
Install on a phone: open the GitHub Pages URL in the browser and **Add to Home Screen**.

`https://RJA9291.github.io/Multi_Tracker/`

## Structure
- `app.html` — the single source of truth: a self-contained fragment (title + style + markup +
  script). Also publishable as a claude.ai Artifact. **Edit this file only.**
- `index.html` — generated from `app.html` by `.claude/build-pages.sh` (adds PWA head, manifest
  link, service-worker registration). Do not hand-edit.
- `manifest.webmanifest`, `sw.js`, `icon.svg`, `icon-180/192/512.png` — PWA assets.
- `.claude/` — build + auto-push tooling (a Stop hook rebuilds `index.html` and pushes on change).

Data is stored locally in the browser (`localStorage`). Use the ⋯ menu to export / import a JSON
backup. Data does not sync across devices in this version.
