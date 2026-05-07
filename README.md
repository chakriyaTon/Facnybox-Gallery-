# Fancybox Gallery — WordPress Shortcode

A lightweight, responsive WordPress gallery shortcode powered by [Fancybox v6](https://fancyapps.com/fancybox/) and ACF (Advanced Custom Fields).

---

## Requirements

- WordPress theme (add code to your theme files)
- [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/) plugin — **required**
- Internet connection for Fancybox CDN (or self-host the assets)

---

## Installation

1. Copy the shortcode function from `function.php` into your theme's `functions.php`
2. Copy `fancybox-gallery.css` into your theme's `css/` folder
3. Copy `navigate.js` into your theme's `js/` folder
4. The `wp_enqueue_scripts` block in `function.php` handles loading all assets automatically

---

## File Structure

```
fancybox-gallery/
├── README.md                 ← this file
├── function.php              ← shortcode + asset enqueue (copy into functions.php)
├── fancybox-gallery.css      ← gallery styles  →  theme/css/fancybox-gallery.css
└── navigate.js               ← Fancybox init   →  theme/js/navigate.js
```

---

## Shortcode Usage

### Default grid (3 images, 1 big + 2 stacked)
```
[fgal_gallery]
```

### Custom ACF field
```
[fgal_gallery field="gallery_room"]
```

### Featured layout (1 big + 4 small)
```
[fgal_gallery field="gallery_all" layout="featured"]
```

### Columns mode — control how many images and rows to show
```
[fgal_gallery field="gallery_all_columns" columns="6" cols="3"]
```

| Parameter | Default | Description |
|-----------|---------|-------------|
| `field`   | `gallery` | ACF gallery field name |
| `layout`  | `grid` | `grid` or `featured` |
| `columns` | *(auto)* | Total images to show |
| `cols`    | `4` | Images per row (columns mode) |

---

## Layouts

**grid** — 1 large image on the left, 2 smaller images stacked on the right.

**featured** — 1 large image on the left, 4 smaller images in a 2×2 grid on the right.

**columns mode** — triggered when `columns` attribute is set. Displays images in a uniform grid with the number of columns controlled by `cols`.

---

## Mobile Behaviour

On screens ≤ 768 px, all layouts collapse to a single full-width image. A badge button (`Gallery N +`) appears on the first image showing how many more photos are available. Tapping opens the full Fancybox lightbox.

---

## Notes

- All hidden images are still rendered in the DOM (with `display:none`) so Fancybox can navigate through the full album.
- The gallery badge on desktop appears on the **last visible** image; on mobile it appears on the **first** image.
- Images use WordPress's built-in `wp_get_attachment_image()` for proper srcset/lazy loading.
