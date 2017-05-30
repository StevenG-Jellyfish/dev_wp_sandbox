<?php

$path = pathinfo(__FILE__) ['dirname'];

include ($path . '/config.php');

wp_enqueue_script('wpb_composer_front_js');

$fullwidth_start = $output = $fullwidth_end = '';

$wrapper_attributes = array();

$id = !empty($id) ? (' id="' . $id . '"') : '';

global $post;
$page_layout = get_post_meta($post->ID, '_layout', true);

if (isset($_REQUEST['layout']) && !empty($_REQUEST['layout'])) {
    $page_layout = esc_html($_REQUEST['layout']);
}

$padding = !empty($padding) ? $padding : $column_padding;

$row_classes[] = $visibility;
$row_classes[] = 'mk-fullwidth-' . $fullwidth;
$row_classes[] = ($attached == 'true') ? 'add-padding-' . $padding : '';
$row_classes[] = 'attched-' . $attached;
$row_classes[] = $el_class;
$row_classes[] = get_viewport_animation_class($animation);
$row_classes[] = vc_shortcode_custom_css_class($css, ' ');
//$row_classes[] = get_row_css_class();
$row_classes[] = $equal_columns == 'true' ? ' equal-columns' : '';
$row_classes[] = 'js-master-row';
$row_classes[] = ( 'yes' === $disable_element ) ? 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md' : '';

// Prallax video & image
$has_video_bg = ( ! empty( $video_bg ) && ! empty( $video_bg_url ) && vc_extract_youtube_id( $video_bg_url ) );

$parallax_speed = $parallax_speed_bg;
if ( $has_video_bg ) {
	$parallax = $video_bg_parallax;
	$parallax_speed = $parallax_speed_video;
	$parallax_image = $video_bg_url;
	$row_classes[] = 'vc_video-bg-container';
	wp_enqueue_script( 'vc_youtube_iframe_api_js' );
}

if ( ! empty( $parallax ) ) {
	wp_enqueue_script( 'vc_jquery_skrollr_js' );
	$wrapper_attributes[] = 'data-vc-parallax="' . esc_attr( $parallax_speed ) . '"'; // parallax speed
	$row_classes[] = 'vc_general vc_parallax vc_parallax-' . $parallax;
	if ( false !== strpos( $parallax, 'fade' ) ) {
		$row_classes[] = 'js-vc_parallax-o-fade';
		$wrapper_attributes[] = 'data-vc-parallax-o-fade="on"';
	} elseif ( false !== strpos( $parallax, 'fixed' ) ) {
		$row_classes[] = 'js-vc_parallax-o-fixed';
	}
}

if ( ! empty( $parallax_image ) ) {
	if ( $has_video_bg ) {
		$parallax_image_src = $parallax_image;
	} else {
		$parallax_image_id = preg_replace( '/[^\d]/', '', $parallax_image );
		$parallax_image_src = wp_get_attachment_image_src( $parallax_image_id, 'full' );
		if ( ! empty( $parallax_image_src[0] ) ) {
			$parallax_image_src = $parallax_image_src[0];
		}
	}
	$wrapper_attributes[] = 'data-vc-parallax-image="' . esc_attr( $parallax_image_src ) . '"';
}
if ( ! $parallax && $has_video_bg ) {
	$wrapper_attributes[] = 'data-vc-video-bg="' . esc_attr( $video_bg_url ) . '"';
}

// Full width option
if($fullwidth == 'true'){ ?>
	</div></div></div>
	<?php if(is_singular('post')) { ?>
		</div>
	<?php }
} ?>

<div<?php echo $id; ?> <?php echo implode( ' ', $wrapper_attributes ) ?> class="wpb_row vc_row vc_row-fluid <?php echo implode(' ', $row_classes); ?>">
	<?php if($fullwidth == 'true' && $fullwidth_content == 'false') { ?>
		<div class="mk-grid">
	<?php } ?>	
			<?php echo wpb_js_remove_wpautop($content); ?>
	<?php if($fullwidth == 'true' && $fullwidth_content == 'false') { ?>	
		</div>	
	<?php } ?>
</div>

<?php if($fullwidth == 'true') { ?>

	<div class="mk-main-wrapper-holder">
		<div class="theme-page-wrapper <?php echo $page_layout; ?>-layout mk-grid vc_row-fluid no-padding">
			<div class="theme-content no-padding">

			<?php if (is_singular('post')) { ?>
			        <div class="single-content">
			<?php }
}
