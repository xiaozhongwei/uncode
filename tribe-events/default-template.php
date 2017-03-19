<?php 

get_header(); 

if(is_singular('tribe_events')){
	tribe_events_before_html(); 
	tribe_get_view(); 
	tribe_events_after_html();
} else {


/**
 * DATA COLLECTION - START
 *
 */

/** Init variables **/
$limit_width = $limit_content_width = $the_content = $main_content = $layout = $sidebar_style = $sidebar_bg_color = $sidebar = $sidebar_size = $sidebar_sticky = $sidebar_padding = $sidebar_inner_padding = $sidebar_content = $navigation_content = $page_custom_width = $row_classes = $main_classes = $footer_classes = '';
$with_builder = false;
$index_has_navigation = false;

$post_type = 'tribe_events_index';
$single_post_width = ot_get_option('_uncode_' . $post_type . '_single_width');
$single_text_length = ot_get_option('_uncode_' . $post_type . '_single_text_length');
set_query_var( 'single_post_width', $single_post_width );
if ($single_text_length !== '') set_query_var( 'single_text_length', $single_text_length );

/** Get general datas **/
$style = ot_get_option('_uncode_general_style');
if (isset($metabox_data['_uncode_specific_bg_color'][0]) && $metabox_data['_uncode_specific_bg_color'][0] !== '') {
	$bg_color = $metabox_data['_uncode_specific_bg_color'][0];
} else $bg_color = ot_get_option('_uncode_general_bg_color');
$bg_color = ($bg_color == '') ? ' style-'.$style.'-bg' : ' style-'.$bg_color.'-bg';

/** Get page width info **/
$boxed = ot_get_option('_uncode_boxed');

if ($boxed !== 'on') {
	/** Use generic page width **/
	$generic_content_full = ot_get_option('_uncode_'.$post_type.'_layout_width');
	if ($generic_content_full === '') {
		$main_content_full = ot_get_option('_uncode_body_full');
		if ($main_content_full !== 'on') $limit_content_width = ' limit-width';
	} else {
		if ($generic_content_full === 'limit') {
			$generic_custom_width = ot_get_option('_uncode_'.$post_type.'_layout_width_custom');
			if ($generic_custom_width[1] === 'px') {
				$page_custom_width[0] = 12 * round(($generic_custom_width[0]) / 12);
			}
			if (is_array($generic_custom_width) && !empty($generic_custom_width)) {
				$page_custom_width = ' style="max-width: '.implode('', $generic_custom_width).'; margin: auto;"';
			}
		}
	}
}

if (!empty($metabox_data)) {
	$media = get_post_meta($post->ID, '_uncode_featured_media', 1);
	$media_display = get_post_meta($post->ID, '_uncode_featured_media_display', 1);
	$featured_image = get_post_thumbnail_id($post->ID);
	if ($featured_image === '') $featured_image = $media;
}


/** Collect header data **/
if (isset($metabox_data['_uncode_header_type'][0]) && $metabox_data['_uncode_header_type'][0] !== '')
{
	$page_header_type = $metabox_data['_uncode_header_type'][0];
	if ($page_header_type !== 'none')
	{
		$meta_data = uncode_get_specific_header_data($metabox_data, $post_type, $featured_image);
		$metabox_data = $meta_data['meta'];
		$show_title = $meta_data['show_title'];
	}
}
else
{
	$page_header_type = ot_get_option('_uncode_' . $post_type . '_header');
	if ($page_header_type !== '' && $page_header_type !== 'none')
	{
		$metabox_data['_uncode_header_type'] = array($page_header_type);
		$meta_data = uncode_get_general_header_data($metabox_data, $post_type);
		$metabox_data = $meta_data['meta'];
		$show_title = $meta_data['show_title'];
	}
}

/** Get layout info **/
$activate_sidebar = ot_get_option('_uncode_'.$post_type.'_activate_sidebar');
if ($activate_sidebar !== 'off') {
	$layout = ot_get_option('_uncode_'.$post_type.'_sidebar_position');
	if ($layout === '') $layout = 'sidebar_right';
	$sidebar = ot_get_option('_uncode_'.$post_type.'_sidebar');
	$sidebar_style = ot_get_option('_uncode_'.$post_type.'_sidebar_style');
	$sidebar_size = ot_get_option('_uncode_'.$post_type.'_sidebar_size');
	$sidebar_sticky = ot_get_option('_uncode_' . $post_type . '_sidebar_sticky');
	$sidebar_sticky = ($sidebar_sticky === 'on') ? ' sticky-element sticky-sidebar' : '';
	$sidebar_fill = ot_get_option('_uncode_'.$post_type.'_sidebar_fill');
	$sidebar_bg_color = ot_get_option('_uncode_'.$post_type.'_sidebar_bgcolor');
	$sidebar_bg_color = ($sidebar_bg_color !== '') ? ' style-' . $sidebar_bg_color . '-bg' : '';
}

if ($sidebar_style === '') $sidebar_style = $style;

/**
 * DATA COLLECTION - END
 *
 */

$posts_counter = $wp_query->post_count;

/** Build header **/
if ($page_header_type !== '' && $page_header_type !== 'none')
{
	$page_title = ot_get_option('_uncode_'.$post_type.'_header_title_text');
	$page_header = new unheader($metabox_data, $page_title);

	$header_html = $page_header->html;
	if ($header_html !== '') {
		echo '<div id="page-header">';
		echo do_shortcode( shortcode_unautop( $page_header->html ) );
		echo '</div>';
	}
}
echo '<script type="text/javascript">UNCODE.initHeader();</script>';

ob_start();
tribe_events_before_html(); 
tribe_get_view(); 
tribe_events_after_html();
$the_content = ob_get_clean();

	if ($layout === 'sidebar_right' || $layout === 'sidebar_left')
	{

		/** Build structure with sidebar **/

		if ($sidebar_size === '') $sidebar_size = 4;
		$main_size = 12 - $sidebar_size;
		$expand_col = '';

		/** Collect paddings data **/

		$footer_classes = ' no-top-padding double-bottom-padding';

		if ($sidebar_bg_color !== '')
		{
			if ($sidebar_fill === 'on')
			{
				$sidebar_inner_padding.= ' std-block-padding';
				$sidebar_padding.= $sidebar_bg_color;
				$expand_col = ' unexpand';
				if ($limit_content_width === '')
				{
					$row_classes.= ' no-h-padding col-no-gutter no-top-padding';
					$footer_classes = ' std-block-padding no-top-padding';
					$main_classes.= ' std-block-padding';
				}
				else
				{
					$row_classes.= ' no-top-padding';
					$main_classes.= ' double-top-padding';
				}
			}
			else
			{
				$row_classes .= ' double-top-padding';
  			$row_classes .= ' double-bottom-padding';
				$sidebar_inner_padding.= $sidebar_bg_color . ' single-block-padding';
			}
		}
		else
		{
			$row_classes.= ' col-std-gutter double-top-padding';
			$main_classes.= ' double-bottom-padding';
		}

		$row_classes.= ' no-bottom-padding';
		$sidebar_inner_padding.= ' double-bottom-padding';

		/** Build sidebar **/

		$sidebar_content = "";
		ob_start();
		if ($sidebar !== '')
		{
			dynamic_sidebar($sidebar);
		}
		else
		{
			dynamic_sidebar();
		}
		$sidebar_content = ob_get_clean();

		/** Create html with sidebar **/

		$the_content = '<div class="post-content style-' . $style . $main_classes . '">' . $the_content . '</div>';

		$main_content = '<div class="col-lg-' . $main_size . '">
											' . $the_content . '
										</div>';

		$the_content = '<div class="row-container">
        							<div class="row row-parent' . $row_classes . $limit_content_width . '"' . $page_custom_width . '>
												<div class="row-inner">
													' . (($layout === 'sidebar_right') ? $main_content : '') . '
													<div class="col-lg-' . $sidebar_size . '">
														<div class="uncol style-' . $sidebar_style . $expand_col . $sidebar_padding . (($sidebar_fill === 'on' && $sidebar_bg_color !== '') ? '' : $sidebar_sticky) . '">
															<div class="uncoltable' . (($sidebar_fill === 'on' && $sidebar_bg_color !== '') ? $sidebar_sticky : '') . '">
																<div class="uncell' . $sidebar_inner_padding . '">
																	<div class="uncont">
																		' . $sidebar_content . '
																	</div>
																</div>
															</div>
														</div>
													</div>
													' . (($layout === 'sidebar_left') ? $main_content : '') . '
												</div>
											</div>
										</div>';
	} else {

		/** Create html without sidebar **/
		$the_content = '<div class="post-content"' . $page_custom_width . '>' . uncode_get_row_template($the_content, $limit_width, $limit_content_width, $style, '', 'double', true, 'double') . '</div>';

	}


	/** Display post html **/
	echo '<div class="page-body' . $bg_color . '">
          <div class="post-wrapper">
          	<div class="post-body">' . do_shortcode($the_content) . '</div>
          </div>
        </div>';


}
get_footer();
