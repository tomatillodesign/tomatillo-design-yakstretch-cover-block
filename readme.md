# YakStretch Cover Block

A custom ACF-powered WordPress block that rotates background images with configurable overlays and flexible content placement. Built for performance, accessibility, and a smooth editorial experience.

---

## Features

- ✅ Rotating background images (fade-in/fade-out)
- 🎥 **NEW:** Video background support (self-hosted or YouTube/Vimeo)
- 🎲 Optional randomization
- 🎨 Overlay styles: flat or directional gradient
- 🧭 9-position content placement (e.g. top-left, center-center, bottom-right)
- 📐 Min-height control (desktop and mobile)
- 🧱 Full `InnerBlocks` support (add any block content)
- 🖼 Editor preview with real-time ACF field sync
- 🧩 Native block alignment support (`full`, `wide`, etc.)
- ⏯️ WCAG accessible play/pause button (controls images or video)
- 🖼️ AVIF/WebP image optimization integration
- 🎯 True WYSIWYG editor experience
- 🔧 Enhanced security and performance

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

### Background Type
- **Background Type** (`Images` or `Video`) - *NEW in v1.2*

### Image Mode (when Background Type = Images)
- **Gallery** (Image array)
- **Randomize** (true/false)
- **Delay** (ms between image switches)
- **Fade Duration** (ms for crossfade)

### Video Mode (when Background Type = Video) - *NEW in v1.2*
- **Video Source** (`Media Library` or `URL`)
- **Video File** (video upload from media library)
- **Video URL** (YouTube or Vimeo URL)
- **Video Poster** (optional preview image)
- **Video Fallback Image** (shown while loading, on error, or with reduced motion)
- **Mobile Fallback Image** (optional separate image for mobile devices)
- **Video Autoplay** (true/false, default: true)
- **Video Loop** (true/false, default: true)
- **Video Muted** (true/false, default: true)
- **Disable Video on Mobile** (true/false, shows fallback image on mobile)

### Common Settings
- **Content Placement** (`top left`, `center center`, `bottom right`, etc.)
- **Overlay Style** (`flat`, `gradient`)
- **Overlay Color** (supports alpha)
- **Overlay Opacity** (0-100%)
- **Min Height (Desktop)** (e.g. `500px` or `100vh`)
- **Min Height (Mobile)** (e.g. `300px`)
- **Show Play/Pause Button** (true/false) - controls image rotation or video playback

---

## Developer Notes

- CSS follows a layered architecture for theme integration (`@layer components` etc.)
- JavaScript uses fade logic and image preloading to ensure smooth transitions
- The first image loads instantly (no fade) for performance and accessibility
- Editor-side logic handles background rendering via MutationObserver to support live ACF field updates
- **Video Backgrounds**: Supports self-hosted videos (`<video>`) and external embeds (YouTube/Vimeo `<iframe>`)
- **Video Fallbacks**: Automatic fallback image display for loading, errors, reduced motion, and mobile
- **Privacy**: YouTube embeds use `youtube-nocookie.com` domain
- Integrates with Tomatillo Design AVIF Everywhere plugin for optimized image delivery
- WCAG compliant play/pause controls with motion preference support
- Enhanced security with proper input sanitization and output escaping
- Improved performance with image caching and timer management

---

## Version 1.2 Updates

### 🎥 **Major New Feature: Video Backgrounds**
- **Background Type Selection**: Choose between image rotation or single video background
- **Video Sources**: Upload from media library or embed via YouTube/Vimeo URLs
- **Video Controls**: Autoplay, loop, and mute options (all enabled by default)
- **Poster Images**: Optional preview image before video loads
- **Fallback Images**: Automatic fallback for loading states, errors, and reduced motion preferences
- **Mobile Optimization**: Option to disable video on mobile with separate fallback image
- **Play/Pause Integration**: Existing play/pause button now controls video playback

### 🔧 **Technical Enhancements**
- **Video Embedding**: Supports self-hosted `<video>` elements and YouTube/Vimeo iframes
- **Privacy**: Uses `youtube-nocookie.com` for YouTube embeds
- **Accessibility**: Respects `prefers-reduced-motion` with fallback images
- **Editor Preview**: Static preview image for video backgrounds (no video playback in editor)
- **Content Alignment**: Fixed content positioning to respect placement settings with image padding

### 🎯 **Improvements**
- **Overlay Positioning**: Fixed overlay and content width constraints
- **Z-Index Layering**: Proper stacking order for video, fallback, overlay, and content
- **Robust Field Reading**: Dynamic settings work correctly even when fields are conditionally hidden

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
