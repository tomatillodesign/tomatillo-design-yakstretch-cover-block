<?php
/**
 * YakStretch Cover Block Template
 *
 * Renders a rotating background image block with overlay and InnerBlocks content.
 */

// $is_preview = is_admin() && function_exists( 'acf_is_block_editor' ) && acf_is_block_editor();
$is_preview = ! empty( $block['data']['is_preview'] );

// Unique block ID and class
$block_id = 'yakstretch-' . $block['id'];
$align_class = isset($block['align']) ? 'align' . $block['align'] : '';
$custom_class = isset($block['className']) ? esc_attr($block['className']) : '';

$wrapper_classes = implode(' ', array_filter([
	'yakstretch-cover-block',
	$align_class,
	$custom_class,
	$is_preview ? 'is-editor-preview' : '',
]));

// Fields
<<<<<<< HEAD
// CRITICAL: For ACF Blocks, data is stored in $block['data'], not post meta
// Read directly from block data FIRST, then try get_field() as fallback
$block_data = $block['data'] ?? [];

// Check multiple possible locations for gallery data
// ACF stores block data under field KEY (field_yakstretch_gallery) in editor
$gallery_ids = null;

// Check field_yakstretch_gallery FIRST (what editor uses and saves to)
if (isset($block_data['field_yakstretch_gallery'])) {
	$gallery_ids_temp = $block_data['field_yakstretch_gallery'];
	
	// Filter out invalid values before checking if empty
	if (is_array($gallery_ids_temp)) {
		$gallery_ids_temp = array_filter($gallery_ids_temp, function($id) {
			return !empty($id) && $id !== 0 && $id !== '0';
		});
		$gallery_ids_temp = array_values($gallery_ids_temp);
	}
	
	if (!empty($gallery_ids_temp)) {
		$gallery_ids = $gallery_ids_temp;
	}
}

// Fallback to gallery name if field_yakstretch_gallery wasn't found or was invalid
if (empty($gallery_ids) && isset($block_data['gallery'])) {
	$gallery_ids_temp = $block_data['gallery'];
	
	// Filter out invalid values
	if (is_array($gallery_ids_temp)) {
		$gallery_ids_temp = array_filter($gallery_ids_temp, function($id) {
			return !empty($id) && $id !== 0 && $id !== '0';
		});
		$gallery_ids_temp = array_values($gallery_ids_temp);
	}
	
	if (!empty($gallery_ids_temp)) {
		$gallery_ids = $gallery_ids_temp;
	}
}

// Last resort: Try get_field()
if (empty($gallery_ids)) {
	$gallery_from_get_field = get_field('gallery');
	
	if (!empty($gallery_from_get_field) && is_array($gallery_from_get_field)) {
		// Extract IDs if it's an array of objects
		$gallery_ids = array_map(function($item) {
			return is_array($item) ? ($item['ID'] ?? $item['id'] ?? null) : $item;
		}, $gallery_from_get_field);
		$gallery_ids = array_filter($gallery_ids);
		$gallery_ids = array_values($gallery_ids);
	}
}

// Convert IDs to attachment objects
$images = [];

if (!empty($gallery_ids) && is_array($gallery_ids)) {
	// Filter out invalid values (like '0' string)
	$gallery_ids = array_filter($gallery_ids, function($id) {
		return !empty($id) && $id !== '0' && $id !== 0;
	});
	$gallery_ids = array_values($gallery_ids); // Re-index array
	
	if (!empty($gallery_ids)) {
		// Check if first item is numeric (ID) or already an object
		$first_item = reset($gallery_ids);
		
		if (is_numeric($first_item)) {
			// Convert IDs to attachment objects
			foreach ($gallery_ids as $attachment_id) {
				$attachment_id = (int) $attachment_id; // Ensure integer
				
				if ($attachment_id > 0) {
					// Try ACF's function first
					if (function_exists('acf_get_attachment')) {
						$attachment = acf_get_attachment($attachment_id);
						if ($attachment) {
							$images[] = $attachment;
							continue;
						}
					}
					
					// Fallback: build attachment object manually
					$attachment_url = wp_get_attachment_image_url($attachment_id, 'full');
					if ($attachment_url) {
						$images[] = [
							'ID' => $attachment_id,
							'id' => $attachment_id,
							'url' => $attachment_url,
							'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true) ?: '',
							'title' => get_the_title($attachment_id) ?: '',
							'filename' => basename($attachment_url),
						];
					}
				}
			}
		} elseif (is_array($first_item) && isset($first_item['ID'])) {
			// Already attachment objects
			$images = $gallery_ids;
		}
	}
}

// FALLBACK: Try get_field() if block data didn't work
if (empty($images)) {
	$images_from_get_field = get_field('gallery');
	if (!empty($images_from_get_field) && is_array($images_from_get_field)) {
		$images = $images_from_get_field;
	}
}

// Ensure $images is always an array
if (!is_array($images)) {
	$images = [];
}

$has_images = ! empty($images);
$first_image_url      = $has_images ? (yakstretch_get_optimized_image_url($images[0]['ID']) ?: esc_url($images[0]['url'])) : '';
$content_position     = get_field('content_placement') ?: 'bottom center';
$overlay_style        = get_field('overlay_style') ?: 'flat';
$overlay_hex          = get_field('overlay_color') ?: '#000000';
$overlay_opacity_raw  = get_field('overlay_opacity') ?: 50;
$overlay_opacity      = $overlay_opacity_raw / 100;
$min_height_desktop   = get_field('min_height_desktop') ?: '500px';
$min_height_mobile    = get_field('min_height_mobile') ?: '300px';
$show_play_pause      = get_field('show_play_pause') ?: false;

$image_padding_left = (float) get_field('image_padding_left') ?: 0;
$image_padding_left_unit = $image_padding_left . '%';
$image_width_unit = $image_padding_left > 0 ? 'calc(100% - ' . $image_padding_left_unit . ')' : '100%';

// CSS class for content placement
$placement_class = 'yakstretch-content-' . str_replace(' ', '-', strtolower($content_position));

// CSS class for gradient direction
$gradient_direction_class = '';
if ( $overlay_style === 'gradient' ) {
	$dir_map = [
		'top'    => 'top-to-bottom',
		'bottom' => 'bottom-to-top',
		'left'   => 'left-to-right',
		'right'  => 'right-to-left',
		'center' => 'center-radial',
	];

	foreach ( $dir_map as $key => $class ) {
		if ( stripos( $content_position, $key ) !== false ) {
			$gradient_direction_class = 'yakstretch-gradient-' . $class;
			break;
		}
	}

	if ( ! $gradient_direction_class ) {
		$gradient_direction_class = 'yakstretch-gradient-default';
	}
}

$overlay_rgba = yakstretch_hex_to_rgba($overlay_hex, $overlay_opacity);

// Editor-only style attribute
$dynamic_preview_style = '';
if ( $is_preview ) {
	$selector = '#' . $block_id;
	$image_offset = $image_padding_left > 0 ? 'calc(100% - ' . $image_padding_left_unit . ')' : '100%';

	$dynamic_preview_style = "

		#{$block_id} .yakstretch-image-wrapper {
			outline: 2px solid magenta;
		}
		#{$block_id} .yakstretch-image-rotator {
			outline: 2px dashed cyan;
			background-color: rgba(0, 255, 255, 0.1);
		}

		{$selector} {
			position: relative;
			background: none;
		}
		{$selector} .yakstretch-image-wrapper::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			width: {$image_padding_left_unit};
			height: 100%;
			background-color: {$overlay_rgba};
			z-index: -1;
		}
		{$selector} .yakstretch-image-wrapper {
			background-image: url('<?php echo esc_url($first_image_url); ?>');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			background-color: transparent;
			width: 100%;
			height: 100%;
			position: relative;
			overflow: hidden;
		}
		{$selector} .yakstretch-image-rotator {
			width: {$image_offset};
			height: 100%;
			position: absolute;
			top: 0;
			right: 0;
			z-index: -2;
		}
	";
}


?>

<div id="<?php echo esc_attr($block_id); ?>"
     class="<?php echo esc_attr($wrapper_classes); ?>"
     data-yakstretch="1"
     data-has-gallery="<?php echo esc_attr($has_images ? '1' : '0'); ?>"
	 >

	<?php
	// Cache image URLs to avoid redundant processing
	$image_urls = [];
	$debug_info = []; // For debugging AVIF usage
	
	if (is_array($images)) {
		foreach ($images as $image) {
			// Try to get AVIF/WebP optimized URL first
			$optimized_url = yakstretch_get_optimized_image_url($image['ID']);
			$final_url = $optimized_url ?: esc_url_raw($image['url']);
			$image_urls[] = $final_url;
			
			// Debug info
			if (defined('WP_DEBUG') && WP_DEBUG) {
				$debug_info[] = [
					'id' => $image['ID'],
					'original' => basename($image['url']),
					'optimized' => $optimized_url ? basename($optimized_url) : 'none',
					'format' => $optimized_url ? pathinfo($optimized_url, PATHINFO_EXTENSION) : 'original'
				];
			}
		}
	}
	
	// Show debug info if WP_DEBUG is enabled
	if (defined('WP_DEBUG') && WP_DEBUG && !empty($debug_info)) {
		echo '<!-- Yakstretch AVIF Debug: ';
		foreach ($debug_info as $info) {
			echo "ID {$info['id']}: {$info['original']} → {$info['optimized']} ({$info['format']}) | ";
		}
		echo '-->';
	}
	?>

	<div class="yakstretch-image-wrapper">
		<?php if ( $image_padding_left > 0 ) : ?>
			<div class="yakstretch-overlay-solid-left"
				style="left: 0; width: <?php echo esc_attr($image_padding_left_unit); ?>; height: 100%; background-color: <?php echo esc_attr($overlay_rgba); ?>;">
			</div>
		<?php endif; ?>

		<div class="yakstretch-image-rotator"
			style="width: <?php echo esc_attr($image_width_unit); ?>; right: 0;"
			data-images='<?php echo esc_attr(wp_json_encode($image_urls)); ?>'
			data-delay='<?php echo esc_attr(get_field('delay') ?: 6000); ?>'
			data-fade='<?php echo esc_attr(get_field('fade') ?: 1000); ?>'
			data-randomize='<?php echo get_field('randomize') ? '1' : '0'; ?>'>
		</div>
	</div>

	<?php
		$gradient_left_style = '';
		if ( $image_padding_left > 0 ) {
			$gradient_left_style = 'left: ' . $image_padding_left_unit . ';';
		}
	?>

	<div class="yakstretch-overlay yakstretch-overlay-<?php echo esc_attr($overlay_style); ?>
		<?php echo esc_attr($gradient_direction_class); ?>"
		style="--yak-overlay-color: <?php echo esc_attr($overlay_rgba); ?>;
		<?php echo $gradient_left_style; ?>">
	</div>

	<div class="yakstretch-content <?php echo esc_attr($placement_class); ?>">
		<?php if ( $is_preview && ! $has_images ) : ?>
			<p style="color: white; padding: 1rem;"><em>No images selected yet.</em></p>
		<?php endif; ?>
		<InnerBlocks />
	</div>

	<?php if ( $show_play_pause && $has_images ) : ?>
		<button 
			type="button" 
			class="yakstretch-play-pause-btn" 
			aria-label="Pause image rotation"
			title="Pause image rotation"
			data-yakstretch-pause="true">
			<span class="yakstretch-btn-icon" aria-hidden="true"></span>
			<span class="yakstretch-btn-text">Pause</span>
		</button>
	<?php endif; ?>

	<style>
		/* Scoped min-height styles */
		#<?php echo esc_attr($block_id); ?> {
			min-height: <?php echo esc_attr($min_height_desktop); ?>;
			width: 100%;
		}
		@media (max-width: 767px) {
			#<?php echo esc_attr($block_id); ?> {
				min-height: <?php echo esc_attr($min_height_mobile); ?>;
			}
		}
	</style>
	<?php if ( $dynamic_preview_style ) : ?>
		<style><?php echo $dynamic_preview_style; ?></style>
	<?php endif; ?>


</div>
