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
// CRITICAL: For ACF Blocks, data is stored in $block['data'], not post meta
// Read directly from block data FIRST, then try get_field() as fallback
$block_data = $block['data'] ?? [];

// Get background type (default to 'images' for backward compatibility)
$background_type = 'images';
if (isset($block_data['field_yakstretch_background_type'])) {
	$background_type = $block_data['field_yakstretch_background_type'];
} elseif (isset($block_data['background_type'])) {
	$background_type = $block_data['background_type'];
} else {
	$background_type = get_field('background_type') ?: 'images';
}
$background_type = in_array($background_type, ['images', 'video']) ? $background_type : 'images';

// Video-related variables (only used when background_type = 'video')
$video_data = [
	'source' => null,
	'url' => null,
	'embed_url' => null,
	'poster_url' => null,
	'fallback_url' => null,
	'mobile_fallback_url' => null,
	'autoplay' => true,
	'loop' => true,
	'muted' => true,
	'disable_on_mobile' => false,
];

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

// Handle video mode
if ($background_type === 'video') {
	// Get video source
	$video_source = 'media_library';
	if (isset($block_data['field_yakstretch_video_source'])) {
		$video_source = $block_data['field_yakstretch_video_source'];
	} elseif (isset($block_data['video_source'])) {
		$video_source = $block_data['video_source'];
	} else {
		$video_source = get_field('video_source') ?: 'media_library';
	}
	$video_data['source'] = $video_source;
	
	// Get video options
	$video_data['autoplay'] = true;
	$video_data['loop'] = true;
	$video_data['muted'] = true;
	$video_data['disable_on_mobile'] = false;
	
	if (isset($block_data['field_yakstretch_video_autoplay'])) {
		$video_data['autoplay'] = (bool) $block_data['field_yakstretch_video_autoplay'];
	} else {
		$video_data['autoplay'] = get_field('video_autoplay') !== false ? (bool) get_field('video_autoplay') : true;
	}
	
	if (isset($block_data['field_yakstretch_video_loop'])) {
		$video_data['loop'] = (bool) $block_data['field_yakstretch_video_loop'];
	} else {
		$video_data['loop'] = get_field('video_loop') !== false ? (bool) get_field('video_loop') : true;
	}
	
	if (isset($block_data['field_yakstretch_video_muted'])) {
		$video_data['muted'] = (bool) $block_data['field_yakstretch_video_muted'];
	} else {
		$video_data['muted'] = get_field('video_muted') !== false ? (bool) get_field('video_muted') : true;
	}
	
	if (isset($block_data['field_yakstretch_disable_video_on_mobile'])) {
		$video_data['disable_on_mobile'] = (bool) $block_data['field_yakstretch_disable_video_on_mobile'];
	} else {
		$video_data['disable_on_mobile'] = (bool) get_field('disable_video_on_mobile');
	}
	
	// Get video URL
	if ($video_source === 'media_library') {
		$video_file_id = null;
		if (isset($block_data['field_yakstretch_video_file'])) {
			$video_file_id = $block_data['field_yakstretch_video_file'];
		} elseif (isset($block_data['video_file'])) {
			$video_file_id = $block_data['video_file'];
		} else {
			$video_file_id = get_field('video_file');
		}
		
		if ($video_file_id) {
			$video_data['url'] = yakstretch_get_video_url($video_file_id);
			
			// Get poster image
			$poster_id = null;
			if (isset($block_data['field_yakstretch_video_poster'])) {
				$poster_id = $block_data['field_yakstretch_video_poster'];
			} elseif (isset($block_data['video_poster'])) {
				$poster_id = $block_data['video_poster'];
			} else {
				$poster_id = get_field('video_poster');
			}
			
			if ($poster_id) {
				$poster_url = yakstretch_get_optimized_image_url($poster_id);
				if (!$poster_url) {
					$poster_url = wp_get_attachment_image_url($poster_id, 'full');
				}
				$video_data['poster_url'] = $poster_url ? esc_url_raw($poster_url) : null;
			}
		}
	} else {
		// External URL
		$video_url = '';
		if (isset($block_data['field_yakstretch_video_url'])) {
			$video_url = $block_data['field_yakstretch_video_url'];
		} elseif (isset($block_data['video_url'])) {
			$video_url = $block_data['video_url'];
		} else {
			$video_url = get_field('video_url');
		}
		
		if ($video_url) {
			$video_data['embed_url'] = yakstretch_parse_video_url(
				$video_url,
				$video_data['autoplay'],
				$video_data['loop'],
				$video_data['muted']
			);
			$video_data['url'] = esc_url_raw($video_url);
		}
	}
	
	// Get fallback images
	$fallback_id = null;
	if (isset($block_data['field_yakstretch_video_fallback_image'])) {
		$fallback_id = $block_data['field_yakstretch_video_fallback_image'];
	} elseif (isset($block_data['video_fallback_image'])) {
		$fallback_id = $block_data['video_fallback_image'];
	} else {
		$fallback_id = get_field('video_fallback_image');
	}
	
	if ($fallback_id) {
		$fallback_url = yakstretch_get_optimized_image_url($fallback_id);
		if (!$fallback_url) {
			$fallback_url = wp_get_attachment_image_url($fallback_id, 'full');
		}
		$video_data['fallback_url'] = $fallback_url ? esc_url_raw($fallback_url) : null;
	}
	
	// Get mobile fallback image
	if ($video_data['disable_on_mobile']) {
		$mobile_fallback_id = null;
		if (isset($block_data['field_yakstretch_mobile_fallback_image'])) {
			$mobile_fallback_id = $block_data['field_yakstretch_mobile_fallback_image'];
		} elseif (isset($block_data['mobile_fallback_image'])) {
			$mobile_fallback_id = $block_data['mobile_fallback_image'];
		} else {
			$mobile_fallback_id = get_field('mobile_fallback_image');
		}
		
		if ($mobile_fallback_id) {
			$mobile_fallback_url = yakstretch_get_optimized_image_url($mobile_fallback_id);
			if (!$mobile_fallback_url) {
				$mobile_fallback_url = wp_get_attachment_image_url($mobile_fallback_id, 'full');
			}
			$video_data['mobile_fallback_url'] = $mobile_fallback_url ? esc_url_raw($mobile_fallback_url) : null;
		}
	}
}
$first_image_url      = $has_images ? (yakstretch_get_optimized_image_url($images[0]['ID']) ?: esc_url($images[0]['url'])) : '';
$content_position     = get_field('content_placement') ?: 'bottom center';
$overlay_style        = get_field('overlay_style') ?: 'flat';
$overlay_hex          = get_field('overlay_color') ?: '#000000';
$overlay_opacity_raw  = get_field('overlay_opacity') ?: 50;
$overlay_opacity      = $overlay_opacity_raw / 100;
$min_height_desktop   = get_field('min_height_desktop') ?: '500px';
$min_height_mobile    = get_field('min_height_mobile') ?: '300px';
$show_play_pause      = get_field('show_play_pause') ?: false;

// Get image_padding_left (works for both images and video modes)
$image_padding_left = 0;
if (isset($block_data['field_yakstretch_image_padding_left'])) {
	$image_padding_left = (float) $block_data['field_yakstretch_image_padding_left'];
} elseif (isset($block_data['image_padding_left'])) {
	$image_padding_left = (float) $block_data['image_padding_left'];
} else {
	$image_padding_left = (float) get_field('image_padding_left') ?: 0;
}
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
     data-yakstretch-mode="<?php echo esc_attr($background_type); ?>"
     data-has-gallery="<?php echo esc_attr($has_images ? '1' : '0'); ?>"
	 >

	<?php if ( $image_padding_left > 0 ) : ?>
		<div class="yakstretch-overlay-solid-left"
			style="left: 0; width: <?php echo esc_attr($image_padding_left_unit); ?>; height: 100%; background-color: <?php echo esc_attr($overlay_rgba); ?>;">
		</div>
	<?php endif; ?>

	<?php if ($background_type === 'images') : ?>
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
			<div class="yakstretch-image-rotator"
				style="width: <?php echo esc_attr($image_width_unit); ?>; right: 0;"
				data-images='<?php echo esc_attr(wp_json_encode($image_urls)); ?>'
				data-delay='<?php echo esc_attr(get_field('delay') ?: 6000); ?>'
				data-fade='<?php echo esc_attr(get_field('fade') ?: 1000); ?>'
				data-randomize='<?php echo get_field('randomize') ? '1' : '0'; ?>'>
			</div>
		</div>
	<?php elseif ($background_type === 'video') : ?>
		<?php
		// Determine which fallback image to use
		$active_fallback_url = $video_data['mobile_fallback_url'] ?: $video_data['fallback_url'];
		?>
		
		<?php if ($active_fallback_url) : ?>
			<div class="yakstretch-video-fallback"
				style="background-image: url('<?php echo esc_url($active_fallback_url); ?>'); width: <?php echo esc_attr($image_width_unit); ?>; right: 0;"
				data-fallback-url="<?php echo esc_attr($video_data['fallback_url']); ?>"
				data-mobile-fallback-url="<?php echo esc_attr($video_data['mobile_fallback_url']); ?>">
			</div>
		<?php endif; ?>
		
		<div class="yakstretch-video-wrapper"
			style="width: <?php echo esc_attr($image_width_unit); ?>; right: 0;"
			data-video-source="<?php echo esc_attr($video_data['source']); ?>"
			data-video-url="<?php echo esc_attr($video_data['url']); ?>"
			data-video-embed-url="<?php echo esc_attr($video_data['embed_url']); ?>"
			data-video-poster="<?php echo esc_attr($video_data['poster_url']); ?>"
			data-video-autoplay="<?php echo $video_data['autoplay'] ? '1' : '0'; ?>"
			data-video-loop="<?php echo $video_data['loop'] ? '1' : '0'; ?>"
			data-video-muted="<?php echo $video_data['muted'] ? '1' : '0'; ?>"
			data-disable-mobile-video="<?php echo $video_data['disable_on_mobile'] ? '1' : '0'; ?>">
			
			<?php if ($video_data['source'] === 'media_library' && $video_data['url']) : ?>
				<video
					<?php echo $video_data['autoplay'] ? 'autoplay' : ''; ?>
					<?php echo $video_data['loop'] ? 'loop' : ''; ?>
					<?php echo $video_data['muted'] ? 'muted' : ''; ?>
					playsinline
					<?php if ($video_data['poster_url']) : ?>
						poster="<?php echo esc_url($video_data['poster_url']); ?>"
					<?php endif; ?>
					class="yakstretch-video-element">
					<source src="<?php echo esc_url($video_data['url']); ?>" type="video/mp4">
					Your browser does not support the video tag.
				</video>
			<?php elseif ($video_data['source'] === 'external_url' && $video_data['embed_url']) : ?>
				<div class="yakstretch-video-embed">
					<iframe
						src="<?php echo esc_url($video_data['embed_url']); ?>"
						frameborder="0"
						allow="autoplay; encrypted-media"
						allowfullscreen
						loading="lazy">
					</iframe>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

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

	<?php
		// Constrain content width to overlay side when padding is set
		$content_width_style = '';
		if ( $image_padding_left > 0 ) {
			// Content should stay within the overlay area (left side)
			$content_width_style = 'max-width: ' . $image_padding_left_unit . ';';
		}
	?>
	<div class="yakstretch-content <?php echo esc_attr($placement_class); ?>"
		<?php if ( $content_width_style ) : ?>
			style="<?php echo esc_attr($content_width_style); ?>"
		<?php endif; ?>>
		<?php if ( $is_preview && $background_type === 'images' && ! $has_images ) : ?>
			<p style="color: white; padding: 1rem;"><em>No images selected yet.</em></p>
		<?php elseif ( $is_preview && $background_type === 'video' && ! $video_data['url'] && ! $video_data['embed_url'] ) : ?>
			<p style="color: white; padding: 1rem;"><em>No video selected yet.</em></p>
		<?php endif; ?>
		<InnerBlocks />
	</div>

	<?php if ( $show_play_pause && (($background_type === 'images' && $has_images) || ($background_type === 'video' && ($video_data['url'] || $video_data['embed_url']))) ) : ?>
		<button 
			type="button" 
			class="yakstretch-play-pause-btn" 
			aria-label="<?php echo $background_type === 'video' ? 'Pause video' : 'Pause image rotation'; ?>"
			title="<?php echo $background_type === 'video' ? 'Pause video' : 'Pause image rotation'; ?>"
			data-yakstretch-pause="true"
			data-yakstretch-mode="<?php echo esc_attr($background_type); ?>">
			<span class="yakstretch-btn-icon" aria-hidden="true"></span>
			<span class="yakstretch-btn-text"><?php echo $background_type === 'video' ? 'Pause' : 'Pause'; ?></span>
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
