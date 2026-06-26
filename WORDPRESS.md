# Putting the Summer 2026 Drop page into WordPress

The page is a **fully static, self-contained build** — HTML + inline CSS + one vanilla-JS block, plus `fonts/` and `images/`. No server, no build step, no plugins required. The calculator and the API form both run entirely in the browser (the form POSTs straight to Google Forms), so it drops into WordPress cleanly.

There are two integration paths. Pick based on whether you (TVG) have theme access.

---

## Path A — Theme page template (recommended for production; needs TVG)

Best fidelity and the right long-term home on `haleswx.com`.

1. Copy `index.html`'s `<style>` block, the `<body>` markup, and the `<script>` block into a custom full-width page template in the `hale-theme` (e.g. `template-summer-2026-drop.php`), or enqueue them.
2. Move `fonts/` and `images/` into the theme (e.g. `assets/summer-2026-drop/`) and update the asset URLs:
   - `url("fonts/…")` → `url("<?php echo get_template_directory_uri(); ?>/assets/summer-2026-drop/fonts/…")`
   - `src="images/…"` → the same theme path.
3. Assign the template to a new page (e.g. `/summer-2026-drop`). Keep it unlisted at launch (it's `noindex`).

Everything else (calculator, form, responsive breakpoints) works as-is.

## Path B — Custom HTML block (self-serve, no theme access)

Fastest way to get it live on an existing WP page without touching the theme.

1. Create a new page, add a single **Custom HTML** block (Gutenberg) — *not* a paragraph/classic block, which strips `<style>`/`<script>`.
2. Paste the contents of **`wordpress/embed.html`**. That file is the same page as a fragment (no `<html>`/`<head>`), with all `fonts/` and `images/` URLs rewritten to **absolute URLs on the GitHub Pages host**, so the assets resolve with nothing to upload.
3. Publish. Set the page to noindex in your SEO plugin until launch.

> Note: `embed.html` points fonts/images at `https://cameronehrlich.github.io/hale-summer-2026-drop/…`. That's fine for the unlisted LinkedIn/email launch. For the permanent production page, move to Path A so the assets live on `haleswx.com` and don't depend on the preview host.

---

## What's already handled
- **Fonts** — the three real `hale-theme` webfonts (Marlfield, DM Sans, DM Mono) are bundled.
- **Calculator** — workbook math (HE360 Block 3 / 450 km); "Example configurations" presets; `$`-prefixed, comma-formatted cost + sat count.
- **API form** — POSTs to the Hale Google Form (no-cors, fire-and-forget) with name/email validation, then swaps to a "Request received" confirmation.
- **Responsive** — breakpoints at 900 / 820 / 760 / 560 px (decorative photos hidden on small screens, grids collapse to one column).
- **noindex** — set, so it won't be indexed before the official launch.

## Still to confirm before public launch
- IP-address handling / privacy disclosure (currently the form does not collect IP).
- Delete the two test rows in the Google Form responses ("TEST — …").
- For production, rehost assets on `haleswx.com` (Path A).
