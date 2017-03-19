<?php

if(!function_exists('wellspring_mikado_events_assets')) {
	/**
	 * Loads all necessary styles for events calendar plugin
	 */
	function wellspring_mikado_events_assets() {

		wp_enqueue_style( 'uncode-wellspring_mikado_events_calendar', get_template_directory_uri() . '/library/css/events-calendar.css');

		wp_enqueue_style( 'uncode-wellspring_mikado_events_calendar_responsive', get_template_directory_uri() . '/library/css/events-calendar-responsive.css');
    }

	add_action('wp_enqueue_scripts', 'wellspring_mikado_events_assets');
}


if(!function_exists('wellspring_mikado_events_archive_sidebar_layout')) {
	/**
	 * Resets sidebar layout for events archive page
	 * @param $layout
	 *
	 * @return string
	 */
	function wellspring_mikado_events_archive_sidebar_layout($layout) {
        if(is_post_type_archive('tribe_events')) {
	        $layout = '';
        }

	    return $layout;
    }

	add_filter('wellspring_mikado_sidebar_layout', 'wellspring_mikado_events_archive_sidebar_layout');
}

// if(!function_exists('wellspring_mikado_events_archive_sidebar')) {
// 	/**
// 	 * Resets sidebar for events archive page
// 	 * @param $sidebar
// 	 *
// 	 * @return string
// 	 */
// 	function wellspring_mikado_events_archive_sidebar($sidebar) {
//         if(is_post_type_archive('tribe_events')) {
// 	        $sidebar = '';
//         }

// 	    return $sidebar;
//     }

// 	add_filter('wellspring_mikado_sidebar', 'wellspring_mikado_events_archive_sidebar');
// }

// if(!function_exists('wellspring_mikado_events_archive_title_text')) {
// 	/**
// 	 * Hooks to title text filter and alters it for events calendar page
// 	 * @param $text
// 	 *
// 	 * @return string
// 	 */
// 	function wellspring_mikado_events_archive_title_text($text) {
// 	    if(is_post_type_archive('tribe_events')) {
// 		    $text = esc_html__('Events Calendar', 'wellspring');
// 	    }

//         return $text;
//     }

// 	add_filter('wellspring_mikado_title_text', 'wellspring_mikado_events_archive_title_text');
// }

if(!function_exists('wellspring_mikado_events_tooltip_image')) {
	/**
	 * Hooks to tribe_events_template_data_array and changes tooltip image size
	 * @param $json
	 * @param $event
	 *
	 * @return mixed
	 */
	function wellspring_mikado_events_tooltip_image($json, $event) {
		if(isset($json['imageTooltipSrc'])) {
			$image_tool_arr = wp_get_attachment_image_src( get_post_thumbnail_id( $event->ID ), 'medium' );
			$image_tool_src = $image_tool_arr[0];

			$json['imageTooltipSrc'] = $image_tool_src;
		}

	    return $json;
    }

	add_filter('tribe_events_template_data_array', 'wellspring_mikado_events_tooltip_image', 10, 2);
}