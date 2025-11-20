<?php
/*
Plugin Name: Tomatillo Design ~ Yakstretch Cover Block
Description: Custom block for displaying content on top of a rotating slideshow. Great for "hero" sections.
Plugin URI: https://github.com/tomatillodesign/yak-card-deck
Author: Tomatillo Design
Version: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/



// Examples below!
// See documentation:
// https://www.advancedcustomfields.com/resources/blocks/
// https://www.advancedcustomfields.com/resources/acf_register_block_type/
//
// IMPORTANT: Remember to create your own custom fields in ACF and set them to the correct Block
// See the attached ACF .json for getting started (import this into ACF using the ACF-->Tools)
//


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Gracefully check if ACF PRO is active
function yakstretch_check_acf_pro_dependency() {
	if ( ! class_exists( 'ACF' ) || ! function_exists( 'acf_get_setting' ) || ! acf_get_setting( 'pro' ) ) {
		add_action( 'admin_notices', 'yakstretch_show_acf_pro_missing_notice' );

		// Only deactivate if user has permission and is in admin
		if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
}

// Show admin notice about missing ACF PRO
function yakstretch_show_acf_pro_missing_notice() {
	echo '<div class="notice notice-error"><p><strong>Yak Stretch Cover Block:</strong> This plugin requires <a href="https://www.advancedcustomfields.com/pro/" target="_blank">ACF PRO</a> to be installed and activated. The plugin has been deactivated.</p></div>';
}

// Run dependency check on plugin load
add_action( 'admin_init', 'yakstretch_check_acf_pro_dependency' );

// Also check at activation
register_activation_hook( __FILE__, 'yakstretch_check_acf_pro_dependency' );


add_action( 'init', function() {
	register_block_type( __DIR__ . '/blocks/yakstretch/block.json' );
});




wp_register_script(
	'yakstretch-script',
	plugin_dir_url( __FILE__ ) . 'blocks/yakstretch/yakstretch.js',
	wp_script_is( 'tomatillo-avif-swap', 'registered' ) ? [ 'tomatillo-avif-swap' ] : [],
	'1.2',
	true
);
add_action( 'enqueue_block_assets', function () {
	if ( has_block( 'yak/yakstretch-cover' ) ) {
		wp_enqueue_script( 'yakstretch-script' );

		wp_enqueue_style(
			'yakstretch-style',
			plugin_dir_url( __FILE__ ) . 'blocks/yakstretch/yakstretch_cover.css',
			[],
			'1.0.0'
		);
	}
});



add_action( 'enqueue_block_editor_assets', function() {
	wp_enqueue_style(
		'yakstretch-editor-style',
		plugin_dir_url( __FILE__ ) . 'blocks/yakstretch/editor.css',
		[],
		'1.0.0'
	);

	wp_enqueue_script(
		'yakstretch-editor-script',
		plugin_dir_url( __FILE__ ) . 'blocks/yakstretch/editor.js',
		[],
		'1.2',
		true
	);
});





add_action( 'acf/init', function() {
	acf_add_local_field_group([
		'key' => 'group_yakstretch',
		'title' => 'YakStretch Cover Settings',
		'fields' => [

			[
				'key' => 'field_yakstretch_background_type',
				'label' => 'Background Type',
				'name' => 'background_type',
				'type' => 'select',
				'choices' => [
					'images' => 'Images (slideshow)',
					'video' => 'Single Video',
				],
				'default_value' => 'images',
				'instructions' => 'Choose between rotating image gallery or single background video.',
			],
			[
				'key' => 'field_yakstretch_gallery',
				'label' => 'Background Gallery',
				'name' => 'gallery',
				'type' => 'gallery',
				'required' => 1,
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'images',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_randomize',
				'label' => 'Randomize Order',
				'name' => 'randomize',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 0,
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'images',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_position',
				'label' => 'Content Placement',
				'name' => 'content_placement',
				'type' => 'select',
				'choices' => [
					'top-left' => 'Top Left',
					'top-center' => 'Top Center',
					'top-right' => 'Top Right',
					'center-left' => 'Center Left',
					'center-center' => 'Center Center',
					'center-right' => 'Center Right',
					'bottom-left' => 'Bottom Left',
					'bottom-center' => 'Bottom Center',
					'bottom-right' => 'Bottom Right',
				],
				'default_value' => 'bottom-center',
			],
			[
				'key' => 'field_yakstretch_delay',
				'label' => 'Delay Between Images (ms)',
				'name' => 'delay',
				'type' => 'number',
				'default_value' => 6000,
				'append' => 'ms',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'images',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_fade',
				'label' => 'Fade Duration (ms)',
				'name' => 'fade',
				'type' => 'number',
				'default_value' => 1000,
				'append' => 'ms',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'images',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_overlay_style',
				'label' => 'Overlay Style',
				'name' => 'overlay_style',
				'type' => 'select',
				'choices' => [
					'flat' => 'Flat Color',
					'gradient' => 'Gradient to Transparent',
				],
				'default_value' => 'flat',
			],
			[
				'key' => 'field_yakstretch_overlay_color',
				'label' => 'Overlay Color',
				'name' => 'overlay_color',
				'type' => 'color_picker',
			],
			[
				'key' => 'field_yakstretch_overlay_opacity',
				'label' => 'Overlay Opacity %',
				'name' => 'overlay_opacity',
				'type' => 'range',
				'instructions' => '0% is fully transparent, 100% is solid',
				'append' => '%',
				'default_value' => 50,
				'min' => 0,
				'max' => 100,
				'step' => 1,
			],
			[
				'key' => 'field_yakstretch_image_padding_left',
				'label' => 'Image Padding Left',
				'name' => 'image_padding_left',
				'type' => 'range',
				'instructions' => 'How much space to leave on the left side of the image. 100% = fully pushed right, 0% = flush left.',
				'append' => '%',
				'default_value' => 0,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_position',
							'operator' => '==',
							'value' => 'top-left',
						],
						[
							'field' => 'field_yakstretch_overlay_style',
							'operator' => '==',
							'value' => 'gradient',
						],
						[
							'field' => 'field_yakstretch_overlay_opacity',
							'operator' => '==',
							'value' => '100',
						],
					],
					[
						[
							'field' => 'field_yakstretch_position',
							'operator' => '==',
							'value' => 'center-left',
						],
						[
							'field' => 'field_yakstretch_overlay_style',
							'operator' => '==',
							'value' => 'gradient',
						],
						[
							'field' => 'field_yakstretch_overlay_opacity',
							'operator' => '==',
							'value' => '100',
						],
					],
					[
						[
							'field' => 'field_yakstretch_position',
							'operator' => '==',
							'value' => 'bottom-left',
						],
						[
							'field' => 'field_yakstretch_overlay_style',
							'operator' => '==',
							'value' => 'gradient',
						],
						[
							'field' => 'field_yakstretch_overlay_opacity',
							'operator' => '==',
							'value' => '100',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_height_desktop',
				'label' => 'Min Height (Desktop)',
				'name' => 'min_height_desktop',
				'type' => 'select',
				'choices' => [
					'none' => 'None',
					'300px' => '300px',
					'500px' => '500px',
					'67vh' => '67vh',
					'100vh' => '100vh',
				],
				'default_value' => '500px',
			],
			[
				'key' => 'field_yakstretch_height_mobile',
				'label' => 'Min Height (Mobile)',
				'name' => 'min_height_mobile',
				'type' => 'select',
				'choices' => [
					'none' => 'None',
					'200px' => '200px',
					'300px' => '300px',
					'400px' => '400px',
					'67vh' => '67vh',
					'100vh' => '100vh',
				],
				'default_value' => '300px',
			],
			[
				'key' => 'field_yakstretch_play_pause',
				'label' => 'Show Play/Pause Button',
				'name' => 'show_play_pause',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 0,
				'instructions' => 'Display an accessible play/pause button for users to control image rotation or video playback. Respects "prefers-reduced-motion" setting.',
			],
			[
				'key' => 'field_yakstretch_video_source',
				'label' => 'Video Source',
				'name' => 'video_source',
				'type' => 'radio',
				'choices' => [
					'media_library' => 'Media Library',
					'external_url' => 'External URL (YouTube/Vimeo)',
				],
				'default_value' => 'media_library',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_file',
				'label' => 'Video File',
				'name' => 'video_file',
				'type' => 'file',
				'return_format' => 'id',
				'library' => 'all',
				'min_size' => '',
				'max_size' => '',
				'mime_types' => 'mp4,webm,ogg',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
						[
							'field' => 'field_yakstretch_video_source',
							'operator' => '==',
							'value' => 'media_library',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_poster',
				'label' => 'Video Poster Image',
				'name' => 'video_poster',
				'type' => 'image',
				'return_format' => 'id',
				'preview_size' => 'medium',
				'library' => 'all',
				'instructions' => 'Poster image shown before video loads (HTML5 poster attribute). Optional but recommended.',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
						[
							'field' => 'field_yakstretch_video_source',
							'operator' => '==',
							'value' => 'media_library',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_url',
				'label' => 'Video URL',
				'name' => 'video_url',
				'type' => 'url',
				'instructions' => 'Enter YouTube or Vimeo URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID or https://vimeo.com/VIDEO_ID)',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
						[
							'field' => 'field_yakstretch_video_source',
							'operator' => '==',
							'value' => 'external_url',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_fallback_image',
				'label' => 'Fallback Image',
				'name' => 'video_fallback_image',
				'type' => 'image',
				'return_format' => 'id',
				'preview_size' => 'medium',
				'library' => 'all',
				'instructions' => 'Fallback image shown if video fails to load, on mobile (if disabled), or when reduced motion is enabled.',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_autoplay',
				'label' => 'Autoplay Video',
				'name' => 'video_autoplay',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 1,
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_loop',
				'label' => 'Loop Video',
				'name' => 'video_loop',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 1,
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_video_muted',
				'label' => 'Mute Video',
				'name' => 'video_muted',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 1,
				'instructions' => 'Video must be muted for autoplay to work in most browsers.',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_disable_video_on_mobile',
				'label' => 'Disable Video on Mobile',
				'name' => 'disable_video_on_mobile',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 0,
				'instructions' => 'Show fallback image instead of video on mobile devices (better for data usage and performance).',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
					],
				],
			],
			[
				'key' => 'field_yakstretch_mobile_fallback_image',
				'label' => 'Mobile Fallback Image',
				'name' => 'mobile_fallback_image',
				'type' => 'image',
				'return_format' => 'id',
				'preview_size' => 'medium',
				'library' => 'all',
				'instructions' => 'Image shown on mobile devices when video is disabled. If not set, uses the general fallback image.',
				'conditional_logic' => [
					[
						[
							'field' => 'field_yakstretch_background_type',
							'operator' => '==',
							'value' => 'video',
						],
						[
							'field' => 'field_yakstretch_disable_video_on_mobile',
							'operator' => '==',
							'value' => '1',
						],
					],
				],
			],

		],
		'location' => [
			[
				[
					'param' => 'block',
					'operator' => '==',
					'value' => 'yak/yakstretch-cover',
				],
			],
		],
	]);
});



function yakstretch_hex_to_rgba($hex, $opacity = 1) {
	$hex = str_replace('#', '', $hex);
	if (strlen($hex) === 3) {
		$r = hexdec($hex[0] . $hex[0]);
		$g = hexdec($hex[1] . $hex[1]);
		$b = hexdec($hex[2] . $hex[2]);
	} else {
		$r = hexdec(substr($hex, 0, 2));
		$g = hexdec(substr($hex, 2, 2));
		$b = hexdec(substr($hex, 4, 2));
	}
	return "rgba($r, $g, $b, $opacity)";
}

/**
 * Get optimized image URL (AVIF/WebP) for Yakstretch
 * Integrates with Tomatillo Design AVIF Everywhere plugin
 * 
 * @param int $attachment_id WordPress attachment ID
 * @return string|false Optimized image URL or false if not available
 */
function yakstretch_get_optimized_image_url($attachment_id) {
	// Check if AVIF Everywhere plugin is active
	if (!function_exists('get_post_meta')) {
		return false;
	}
	
	// Try AVIF first (preferred format)
	$avif_url = get_post_meta($attachment_id, '_avif_url', true);
	if ($avif_url) {
		// Convert URL to file path and check if file exists
		$upload_dir = wp_get_upload_dir();
		$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $avif_url);
		if (file_exists($file_path)) {
			return esc_url_raw($avif_url);
		}
	}
	
	// Fallback to WebP
	$webp_url = get_post_meta($attachment_id, '_webp_url', true);
	if ($webp_url) {
		// Convert URL to file path and check if file exists
		$upload_dir = wp_get_upload_dir();
		$file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url);
		if (file_exists($file_path)) {
			return esc_url_raw($webp_url);
		}
	}
	
	// No optimized version available
	return false;
}

/**
 * Get video URL from media library attachment ID
 * 
 * @param int $attachment_id WordPress attachment ID
 * @return string|false Video URL or false if not available
 */
function yakstretch_get_video_url($attachment_id) {
	if (empty($attachment_id) || !is_numeric($attachment_id)) {
		return false;
	}
	
	$video_url = wp_get_attachment_url($attachment_id);
	return $video_url ? esc_url_raw($video_url) : false;
}

/**
 * Parse YouTube or Vimeo URL and convert to embed format
 * 
 * @param string $url YouTube or Vimeo URL
 * @param bool $autoplay Whether to autoplay (default: true)
 * @param bool $loop Whether to loop (default: true)
 * @param bool $muted Whether to mute (default: true)
 * @return string|false Embed URL or false if URL is invalid
 */
function yakstretch_parse_video_url($url, $autoplay = true, $loop = true, $muted = true) {
	if (empty($url)) {
		return false;
	}
	
	$url = esc_url_raw($url);
	$video_id = '';
	$provider = '';
	
	// YouTube detection
	if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
		$video_id = $matches[1];
		$provider = 'youtube';
	}
	// Vimeo detection
	elseif (preg_match('/(?:vimeo\.com\/)(?:.*\/)?(\d+)/', $url, $matches)) {
		$video_id = $matches[1];
		$provider = 'vimeo';
	}
	
	if (empty($video_id) || empty($provider)) {
		return false;
	}
	
	// Build embed URL
	if ($provider === 'youtube') {
		// Use youtube-nocookie.com for privacy
		$embed_url = 'https://www.youtube-nocookie.com/embed/' . $video_id;
		$params = [];
		
		if ($autoplay) {
			$params[] = 'autoplay=1';
		}
		if ($muted) {
			$params[] = 'mute=1';
		}
		if ($loop) {
			$params[] = 'loop=1';
			// YouTube requires playlist parameter for loop to work
			$params[] = 'playlist=' . $video_id;
		}
		$params[] = 'controls=0';
		$params[] = 'modestbranding=1';
		$params[] = 'playsinline=1';
		$params[] = 'rel=0';
		
		if (!empty($params)) {
			$embed_url .= '?' . implode('&', $params);
		}
		
		return $embed_url;
	} elseif ($provider === 'vimeo') {
		$embed_url = 'https://player.vimeo.com/video/' . $video_id;
		$params = [];
		
		if ($autoplay) {
			$params[] = 'autoplay=1';
		}
		if ($muted) {
			$params[] = 'muted=1';
		}
		if ($loop) {
			$params[] = 'loop=1';
		}
		$params[] = 'background=1';
		$params[] = 'controls=0';
		$params[] = 'playsinline=1';
		
		if (!empty($params)) {
			$embed_url .= '?' . implode('&', $params);
		}
		
		return $embed_url;
	}
	
	return false;
}

/**
 * Test shortcode to check AVIF integration
 * Usage: [yakstretch_test_avif]
 */
add_shortcode('yakstretch_test_avif', function() {
	if (!current_user_can('manage_options')) {
		return 'Admin access required.';
	}
	
	// Get a sample image from media library
	$images = get_posts([
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'posts_per_page' => 3,
		'post_status' => 'inherit'
	]);
	
	if (empty($images)) {
		return 'No images found in media library.';
	}
	
	$output = '<h3>Yakstretch AVIF Integration Test</h3><table border="1" cellpadding="10">';
	$output .= '<tr><th>Image ID</th><th>Original</th><th>AVIF</th><th>WebP</th><th>File Sizes</th></tr>';
	
	foreach ($images as $image) {
		$id = $image->ID;
		$original_url = wp_get_attachment_url($id);
		$avif_url = get_post_meta($id, '_avif_url', true);
		$webp_url = get_post_meta($id, '_webp_url', true);
		
		// Get file sizes
		$upload_dir = wp_get_upload_dir();
		$original_size = file_exists(str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $original_url)) 
			? round(filesize(str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $original_url)) / 1024, 1) . ' KB'
			: 'N/A';
			
		$avif_size = $avif_url && file_exists(str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $avif_url))
			? round(filesize(str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $avif_url)) / 1024, 1) . ' KB'
			: 'N/A';
			
		$webp_size = $webp_url && file_exists(str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url))
			? round(filesize(str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url)) / 1024, 1) . ' KB'
			: 'N/A';
		
		$output .= '<tr>';
		$output .= '<td>' . $id . '</td>';
		$output .= '<td>' . basename($original_url) . '</td>';
		$output .= '<td>' . ($avif_url ? basename($avif_url) : 'Not available') . '</td>';
		$output .= '<td>' . ($webp_url ? basename($webp_url) : 'Not available') . '</td>';
		$output .= '<td>Original: ' . $original_size . '<br>AVIF: ' . $avif_size . '<br>WebP: ' . $webp_size . '</td>';
		$output .= '</tr>';
	}
	
	$output .= '</table>';
	
	// Test the helper function
	$test_id = $images[0]->ID;
	$optimized_url = yakstretch_get_optimized_image_url($test_id);
	$output .= '<h4>Helper Function Test:</h4>';
	$output .= '<p>Image ID ' . $test_id . ' optimized URL: <code>' . ($optimized_url ?: 'No optimized version') . '</code></p>';
	
	return $output;
});
