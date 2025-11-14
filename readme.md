# YakStretch Cover Block

A custom ACF-powered WordPress block that rotates background images with configurable overlays and flexible content placement. Built for performance, accessibility, and a smooth editorial experience.

---

## Features

- ✅ Rotating background images (fade-in/fade-out)
- 🎲 Optional randomization
- 🎨 Overlay styles: flat or directional gradient
- 🧭 9-position content placement (e.g. top-left, center-center, bottom-right)
- 📐 Min-height control (desktop and mobile)
- 🧱 Full `InnerBlocks` support (add any block content)
- 🖼 Editor preview with real-time ACF field sync
- 🧩 Native block alignment support (`full`, `wide`, etc.)
- ⏯️ **NEW:** WCAG accessible play/pause button
- 🖼️ **NEW:** AVIF/WebP image optimization integration
- 🎯 **NEW:** True WYSIWYG editor experience
- 🔧 **NEW:** Enhanced security and performance

---

## Requirements

- WordPress 6.0+
- PHP 7.4+
- [Advanced Custom Fields PRO](https://www.advancedcustomfields.com/pro/) (for block registration and fields)

---

## Installation

1. Clone or download this repo into your WordPress `/wp-content/plugins/` directory.
2. Activate the plugin via the WordPress admin.
3. Ensure ACF Pro is installed and active.
4. Edit any post or page using the Block Editor.
5. Insert the **YakStretch Cover** block and configure the block settings in the sidebar.

---

## Field Settings

The block exposes the following ACF fields in the sidebar:

- **Gallery** (Image array)
- **Randomize** (true/false)
- **Content Placement** (`top left`, `center center`, `bottom right`, etc.)
- **Delay** (ms between image switches)
- **Fade Duration** (ms for crossfade)
- **Overlay Style** (`flat`, `gradient`)
- **Overlay Color** (supports alpha)
- **Overlay Opacity** (0-100%)
- **Min Height (Desktop)** (e.g. `500px` or `100vh`)
- **Min Height (Mobile)** (e.g. `300px`)
- **Show Play/Pause Button** (true/false) - *NEW in v1.1*

---

## Developer Notes

- CSS follows a layered architecture for theme integration (`@layer components` etc.)
- JavaScript uses fade logic and image preloading to ensure smooth transitions
- The first image loads instantly (no fade) for performance and accessibility
- Editor-side logic handles background rendering via MutationObserver to support live ACF field updates
- **NEW:** Integrates with Tomatillo Design AVIF Everywhere plugin for optimized image delivery
- **NEW:** WCAG compliant play/pause controls with motion preference support
- **NEW:** Enhanced security with proper input sanitization and output escaping
- **NEW:** Improved performance with image caching and timer management

---

## Version 1.1 Updates

### 🎯 **Major Improvements**
- **AVIF/WebP Integration**: Automatic optimization with Tomatillo Design AVIF Everywhere plugin
- **Play/Pause Button**: WCAG accessible controls with motion preference support
- **True WYSIWYG**: Editor now shows actual image rotation and gradient effects
- **Enhanced Security**: Comprehensive input sanitization and XSS prevention
- **Performance Boost**: Image caching, timer management, and memory leak prevention

### 🔧 **Technical Enhancements**
- **Image Optimization**: Uses AVIF → WebP → Original fallback chain
- **Accessibility**: Screen reader support, keyboard navigation, focus management
- **Editor Experience**: Real-time preview with proper gradient rendering
- **Code Quality**: Improved error handling, debounced updates, proper cleanup

### 🧪 **Testing & Debugging**
- **Debug Mode**: HTML comments show image format usage when WP_DEBUG is enabled
- **Test Shortcode**: `[yakstretch_test_avif]` for integration verification
- **Performance Monitoring**: Built-in file size comparison and optimization tracking

---

## Compatibility

- **WordPress**: 6.0+
- **PHP**: 7.4+
- **ACF Pro**: Required for block registration
- **AVIF Plugin**: Optional but recommended for performance (Tomatillo Design AVIF Everywhere)

---

## Roadmap

- [ ] Add rotation preview toggle in editor
- [ ] Add autoplay pause on hover
- [ ] Add per-image link or caption support
- [ ] Add optional background-blur layer

---

## License

[GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)

---

## Credits

Built with ❤️ by [Tomatillo Design](https://tomatillodesign.com) for the [Yak Theme](https://github.com/tomatillodesign/yak).
