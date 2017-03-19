<?php

function disable_tribe_add_on_compatibility_errors($text) {
	return '';
}

add_filter( 'tribe_add_on_compatibility_errors', 'disable_tribe_add_on_compatibility_errors');

function disable_tribe_google_map($url) {
        return '';
}

add_filter( 'tribe_events_google_maps_api', 'disable_tribe_google_map');
add_filter( 'tribe_events_pro_google_maps_api', 'disable_tribe_google_map');

if(class_exists('Tribe__Events__Main')) {
	include_once get_template_directory() . '/core/events-calendar/lib/events-query.php';
	include_once get_template_directory() . '/core/events-calendar/events-functions.php';

	if(tribe_get_option( 'tribeEventsTemplate', 'default' ) !== ''){
		tribe_update_option( 'tribeEventsTemplate', '' );
	}
	if(tribe_get_option( 'stylesheetOption' ) !== 'tribe'){
		tribe_update_option( 'stylesheetOption', 'tribe' );
	}

	add_image_size('events_square', 550, 550, true);
	add_image_size('events_landscape', 800, 600, true);
	add_image_size('events_portrait', 600, 800, true);

}

function tribe_events_rewrite_fix($bases, $method) {
   $bases = array_merge($bases, array(
       'month' => '(?:month|%e6%9c%88%e4%bb%bd|月)',
       'today' => '(?:today|%e4%bb%8a%e5%a4%a9|今天)',
       'week' => '(?:week|周|週)',
       'photo' => '(?:photo|图片)',
       'all' => '(?:all|%e5%85%a8%e9%83%a8|全部)',
       'day' => '(?:day|%e6%97%a5%e6%9c%9f|日期)',  
   ));
   return $bases;
}

add_filter('tribe_events_rewrite_i18n_slugs', 'tribe_events_rewrite_fix');

