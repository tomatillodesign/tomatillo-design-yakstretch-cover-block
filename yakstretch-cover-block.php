<?php
/*
Plugin Name: Tomatillo Design ~ Yakstretch Cover Block
Description: Custom block for displaying content on top of a rotating slideshow. Great for "hero" sections.
Plugin URI: https://github.com/tomatillodesign/yak-card-deck
Author: Tomatillo Design
Version: 1.1
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
	'1.1',
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
		'1.1',
		true
	);
});





add_action( 'acf/init', function() {
	acf_add_local_field_group([
		'key' => 'group_yakstretch',
		'title' => 'YakStretch Cover Settings',
		'fields' => [

			[
				'key' => 'field_yakstretch_gallery',
				'label' => 'Background Gallery',
				'name' => 'gallery',
				'type' => 'gallery',
				'required' => 1,
			],
			[
				'key' => 'field_yakstretch_randomize',
				'label' => 'Randomize Order',
				'name' => 'randomize',
				'type' => 'true_false',
				'ui' => 1,
				'default_value' => 0,
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
			],
			[
				'key' => 'field_yakstretch_fade',
				'label' => 'Fade Duration (ms)',
				'name' => 'fade',
				'type' => 'number',
				'default_value' => 1000,
				'append' => 'ms',
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
				'instructions' => 'Display an accessible play/pause button for users to control image rotation. Respects "prefers-reduced-motion" setting.',
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
