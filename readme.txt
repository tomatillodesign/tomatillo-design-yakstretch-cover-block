=== YakStretch Cover Block ===
Contributors: tomatillodesign  
Tags: background, cover block, acf, block editor, image rotator, overlay, gradient, gutenberg, avif, accessibility  
Requires at least: 6.0  
Tested up to: 6.5  
Requires PHP: 7.4  
Stable tag: 1.2.0  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

A customizable Gutenberg block that rotates background images with fade effects, overlay options, and flexible content positioning — powered by ACF.

== Description ==

YakStretch Cover Block is a custom ACF-powered Gutenberg block that renders rotating full-width background images behind editable block content.

Built for performance and flexibility, it includes:

- Image rotation with fade timing
- **NEW:** Video background support (media library or YouTube/Vimeo URLs)
- Optional randomization
- Flat or gradient overlay styles
- 9-position content placement (top left, bottom center, etc.)
- Adjustable min-height (desktop and mobile)
- Full editor preview with live field sync
- Theme-aware CSS layering and accessibility-conscious design
- WCAG accessible play/pause button
- AVIF/WebP image optimization integration
- Enhanced security and performance

This block is ideal for hero sections, promotional covers, or immersive full-bleed content.

== Features ==

* Rotating background images (fade in/out)
* **NEW:** Video backgrounds (self-hosted or YouTube/Vimeo)
* Random or sequential display
* Flat or gradient overlays (with adjustable color and opacity)
* Fully editable content (uses `InnerBlocks`)
* Min-height settings for desktop and mobile
* Editor preview with real-time ACF updates
* Block alignment support (full, wide, etc.)
* WCAG accessible play/pause button
* AVIF/WebP image optimization (50-80% smaller files)
* True WYSIWYG editor experience
* Enhanced security and performance

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/yakstretch-cover-block/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure Advanced Custom Fields (Pro) is installed and active
4. Edit a post or page using the block editor
5. Insert the **YakStretch Cover** block and configure its settings from the block sidebar

== Frequently Asked Questions ==

= Does this require ACF Pro? =  
Yes, it uses ACF's block registration features which are part of ACF Pro.

= Can I use it without JavaScript? =  
No — the image rotation requires JavaScript. If JavaScript is disabled, the first image in the gallery will remain visible.

= Can I show video backgrounds instead of images? =  
Yes! Version 1.2 adds full video background support. You can choose between image rotation or a single video background. Videos can be uploaded from your media library or embedded via YouTube/Vimeo URLs. The play/pause button controls video playback when a video is active.

== Screenshots ==

1. YakStretch block with editor preview
2. Overlay style and position control
3. Gallery and animation settings
4. Live rotating background on frontend

== Changelog ==

= 1.2 =
* **NEW:** Video background support (media library or YouTube/Vimeo URLs)
* **NEW:** Background type selector (Images or Video)
* **NEW:** Video autoplay, loop, and mute controls
* **NEW:** Video poster image support
* **NEW:** Fallback image for video backgrounds (loading, errors, reduced motion)
* **NEW:** Mobile video disable option with separate fallback image
* **NEW:** Play/pause button now controls video playback when video is active
* **IMPROVED:** Content alignment respects placement settings when image padding is set
* **IMPROVED:** Overlay positioning works correctly with video backgrounds
* **IMPROVED:** Editor preview shows static image for video backgrounds

= 1.1 =
* **NEW:** WCAG accessible play/pause button with motion preference support
* **NEW:** AVIF/WebP image optimization integration (Tomatillo Design AVIF Everywhere)
* **NEW:** True WYSIWYG editor experience with live image rotation
* **NEW:** Enhanced security with comprehensive input sanitization
* **NEW:** Performance improvements with image caching and timer management
* **NEW:** Debug mode and testing tools for AVIF integration verification
* **IMPROVED:** Editor gradient rendering now works properly
* **IMPROVED:** Better error handling and memory leak prevention
* **IMPROVED:** Enhanced accessibility with screen reader support

= 1.0.0 =
* Initial public release
* ACF field integration
* Image rotation with fade + randomize
* Gradient/flat overlay support
* Content position utilities
* Editor preview with mutation-aware refresh

== Upgrade Notice ==

= 1.2 =
Major update adding video background support. You can now use self-hosted videos or YouTube/Vimeo embeds as backgrounds, with full fallback image support and mobile optimization. Recommended for all users.

= 1.1 =
Major update with AVIF optimization, accessibility improvements, and enhanced editor experience. Recommended for all users.

= 1.0.0 =
First release — add rotating cover blocks to your Gutenberg layouts.

== License ==

This plugin is licensed under the GPLv2 or later.

