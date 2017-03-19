<?php

/**
 * Initialize CPT
 */

function uncode_notification_post_type()
{
	$base = 'notification';
	$label = __('Notification', 'uncode');

	// creating (registering) the custom type
	register_post_type('notification',
		// let's now add all the options for this post type
		array(
			'labels' => array(
				'name' => $label,
				 /* This is the Title of the Group */
				'singular_name' => sprintf(esc_html__('%s Post', 'uncode' ), $label),
				 /* This is the individual type */
				'all_items' => sprintf(esc_html__('All %s', 'uncode' ), $label),
				 /* the all items menu item */
				'add_new' => esc_html__('Add New', 'uncode') ,
				 /* The add new menu item */
				'add_new_item' => sprintf(esc_html__('Add New %s', 'uncode' ), $label),
				 /* Add New Display Title */
				'edit' => esc_html__('Edit', 'uncode') ,
				 /* Edit Dialog */
				'edit_item' => sprintf(esc_html__('Edit %s', 'uncode' ), $label),
				 /* Edit Display Title */
				'new_item' => sprintf(esc_html__('New %s', 'uncode' ), $label),
				 /* New Display Title */
				'view_item' => sprintf(esc_html__('View %s', 'uncode' ), $label),
				 /* View Display Title */
				'search_items' => sprintf(esc_html__('Search %s', 'uncode' ), $label),
				 /* Search Custom Type Title */
				'not_found' => esc_html__('Nothing found in the Database.', 'uncode') ,
				 /* This displays if there are no entries yet */
				'not_found_in_trash' => esc_html__('Nothing found in Trash', 'uncode') ,
				 /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
			) ,
			 /* end of arrays */
			'public' => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui' => true,
			'query_var' => false,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-megaphone',
			 /* this is what order you want it to appear in on the left hand side menu */
			 /* you can specify its url slug */
			'has_archive' => false,
			'capability_type' => 'post',
			'hierarchical' => false,

			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array(
				'title',
				'page-attributes'
			)
		)
	 /* end of options */
	);
	/* end of register post type */
}

add_action('init', 'uncode_notification_post_type');


function uncode_notification_options(){
	$fields = array(
		array(
			'label' => '<i class="fa fa-menu fa-fw"></i> ' . esc_html__('Content', 'uncode') ,
			'id' => '_uncode_general_tab',
			'type' => 'tab',
		) ,
		run_array_mb(array(
			'id' => '_show_title',
			'label' => esc_html__('Show Title', 'uncode') ,
			'desc' => esc_html__('Set to off will not show title.', 'uncode'),
			'std' => 'on',
			'type' => 'on-off',
		)),
		// run_array_mb(array(
		// 	'id' => '_content_type',
		// 	'label' => esc_html__('Content Type', 'uncode') ,
		// 	'type' => 'select',
		// 	'std' => 'post',
		// 	'choices' => array(
		// 		array(
		// 			'value' => 'post',
		// 			'label' => esc_html__('Post', 'uncode') ,
		// 		) ,
		// 		// array(
		// 		// 	'value' => 'event',
		// 		// 	'label' => esc_html__('Event', 'uncode') ,
		// 		// ) ,
		// 		array(
		// 			'value' => 'custom',
		// 			'label' => esc_html__('custom', 'uncode') ,
		// 		) ,
		// 	)
		// )),
		// run_array_mb(array(
		// 	'id' => '_post',
		// 	'condition' => '_content_type:is(post)',
		// 	'label' => esc_html__('Post', 'uncode') ,
		// 	'type' => 'post-select',
		// ), '_content_type:is(post)'),
		run_array_mb(array(
			'id' => '_text',
			'label' => esc_html__('Custom Text', 'uncode') ,
			'type' => 'textarea-simple',
		)),
		run_array_mb(array(
			'id' => '_link_type',
			'label' => esc_html__('Link Type', 'uncode') ,
			'type' => 'select',
			'std' => 'post',
			'choices' => array(
				array(
					'value' => 'post',
					'label' => esc_html__('Post', 'uncode') ,
				) ,
				// array(
				// 	'value' => 'event',
				// 	'label' => esc_html__('Event', 'uncode') ,
				// ) ,
				array(
					'value' => 'custom',
					'label' => esc_html__('Custom', 'uncode') ,
				) ,
				array(
					'value' => 'none',
					'label' => esc_html__('None', 'uncode') ,
				) ,
			)
		)),
		run_array_mb(array(
			'id' => '_link_post',
			'label' => esc_html__('Link to Post', 'uncode') ,
			'std' => '',
			'type' => 'post-select',
		), '_link_type:is(post)'),
		run_array_mb(array(
			'id' => '_link_custom',
			'label' => esc_html__('Custom Link (with http://)', 'uncode') ,
			'type' => 'text',
		), '_link_type:is(custom)'),
		run_array_mb(array(
			'id' => '_link_target',
			'label' => esc_html__('Link Target', 'uncode') ,
			'desc' => esc_html__('Set to blank will open in a new browser tab.', 'uncode') ,
			'type' => 'select',
			'std' => 'blank',
			'choices' => array(
				array(
					'value' => 'self',
					'label' => esc_html__('Self', 'uncode') ,
				) ,
				array(
					'value' => 'blank',
					'label' => esc_html__('Blank', 'uncode') ,
				) ,
			)
		),'_link_type:not(none)'),
		array(
			'label' => '<i class="fa fa-menu fa-fw"></i> ' . esc_html__('Show', 'uncode') ,
			'id' => '_uncode_show_tab',
			'type' => 'tab',
		) ,
		run_array_mb(array(
			'id' => '_show_type',
			'label' => esc_html__('Show Type', 'uncode') ,
			'type' => 'select',
			'std' => 'time',
			'choices' => array(
				array(
					'value' => 'show',
					'label' => esc_html__('Show always', 'uncode') ,
				) ,
				array(
					'value' => 'time',
					'label' => esc_html__('Time Range', 'uncode') ,
				) ,
				array(
					'value' => 'hide',
					'label' => esc_html__('Hide', 'uncode') ,
				) ,
			)
		)),
		run_array_mb(array(
			'id' => '_visibility',
			'label' => esc_html__('Visibility', 'uncode') ,
			'type' => 'select',
			'std' => 'all',
			'choices' => array(
				array(
					'value' => 'all',
					'label' => esc_html__('All pages', 'uncode') ,
				) ,
				array(
					'value' => 'home',
					'label' => esc_html__('Only Home page', 'uncode') ,
				),
			)
		)),
		run_array_mb(array(
			'condition' => '_show_type:is(time)',
			'id' => '_start_time',
			'label' => esc_html__('Start Time', 'uncode') ,
			'desc' => __('It will not show before start time.'),
			'type' => 'date-time-picker',
		), '_show_type:is(time)'),
		run_array_mb(array(
			'condition' => '_show_type:is(time)',
			'id' => '_end_time',
			'label' => esc_html__('End Time', 'uncode') ,
			'desc' => __('If empty the notification will always shows after start time.'),
			'type' => 'date-time-picker',
		), '_show_type:is(time)'),
		array(
			'label' => '<i class="fa fa-menu fa-fw"></i> ' . esc_html__('Style', 'uncode') ,
			'id' => '_uncode_style_tab',
			'type' => 'tab',
		) ,
		run_array_mb(array(
			'id' => '_type',
			'label' => esc_html__('Type', 'uncode') ,
			'type' => 'select',
			'std' => 'info',
			'choices' => array(
				array(
					'value' => 'info',
					'label' => esc_html__('Info', 'uncode') ,
				) ,
				array(
					'value' => 'success',
					'label' => esc_html__('Success', 'uncode') ,
				),
				array(
					'value' => 'notice',
					'label' => esc_html__('notice', 'uncode') ,
				),
				array(
					'value' => 'error',
					'label' => esc_html__('error', 'uncode') ,
				),
				array(
					'value' => 'simple',
					'label' => esc_html__('simple', 'uncode') ,
				),
			)
		)),
		// run_array_mb(array(
		// 	'id' => '_style_type',
		// 	'label' => esc_html__('Style Type', 'uncode') ,
		// 	'type' => 'select',
		// 	'choices' => array(
		// 		array(
		// 			'value' => 'default',
		// 			'label' => esc_html__('Default', 'uncode') ,
		// 		) ,
		// 		array(
		// 			'value' => 'custom',
		// 			'label' => esc_html__('Custom', 'uncode') ,
		// 		) ,
		// 	)
		// )),
		// run_array_mb(array(
		// 	'id' => '_background',
		// 	'label' => esc_html__('Background', 'uncode') ,
		// 	'type' => 'background',
		// ), '_style_type:is(custom)'),
		// run_array_mb(array(
		// 	'id' => '_color',
		// 	'label' => esc_html__('Color', 'uncode') ,
		// 	'type' => 'colorpicker',
		// ), '_style_type:is(custom)'),
	);

	$uncode_page_array = array(
		'id' => '_uncode_notification_options',
		'title' => esc_html__('Notification Options', 'uncode') ,
		'desc' => '',
		'pages' => array(
			'notification'
		) ,
		'context' => 'normal',
		'priority' => 'default',
		'fields' => $fields
	);

	if (function_exists('ot_register_meta_box')) ot_register_meta_box($uncode_page_array);
}

add_action('admin_init', 'uncode_notification_options');

function uncode_notification_scripts(){
	if(ot_get_option('_uncode_notification') === 'off') {
		return;
	}

	wp_enqueue_script( 'uncode-notification', get_template_directory_uri() . '/library/js/min/igrowl.min.js', array('jquery'), UNCODE_VERSION);
}

add_action('wp_enqueue_scripts', 'uncode_notification_scripts');

add_action('wp_footer', 'uncode_add_notification_to_page');

function uncode_add_notification_to_page() {
	if(ot_get_option('_uncode_notification') === 'off') {
		return;
	}

	$args = array(
		'post_type' => 'notification',
		'post_status' => 'publish',
		'nopaging' => true,
		'meta_query' => array(
			array(
				'key'     => '_show_type',
				'value'   => 'hide',
				'compare' => 'NOT LIKE',
			),
		)
	);

	$the_query = new WP_Query( $args );

	if ( !$the_query->have_posts() ) {
		return;
	}

	$notifications = array();

	while ( $the_query->have_posts() ) {
		$the_query->the_post();

		$id = get_the_id();

		if(get_post_meta($id, '_visibility', true) === 'home' && !is_front_page()){
			continue;
		}

		if (get_post_meta($id, '_show_type', true) === 'time') {
			$start = get_post_meta($id, '_start_time', true);
			$end = get_post_meta($id, '_end_time', true);
			$current = time();

			if($start && $current < strtotime($start)) {
				continue;
			}

			if($end && $current > strtotime($end)) {
				continue;
			}
		}

		$notification = array();


		$show_title= get_post_meta($id, '_show_title', true);
		$text = trim(esc_html(get_post_meta($id, '_text', true)));

		if($show_title === 'on') {
			$notification['title'] = get_the_title();
		}

		if($text) {
			$notification['text'] = $text;
		}

		$link = '';

		if (get_post_meta($id, '_link_type', true) === 'post') {
			$link_post_id = get_post_meta($id, '_link_post', true);

			if($link_post_id) {
				$link = get_permalink($link_post_id);
			}
		} else {
			$link = trim(get_post_meta($id, '_link_custom', true));
		}

		if($link) {
			$notification['link'] = $link;

			$link_target = get_post_meta($id, '_link_target', true);

			if($link_target) {
				$notification['target'] = $link_target;
			}
		}

		if (get_post_meta($id, '_style_type', true) === 'custom') {
			$background = get_post_meta($id, '_background', true);

			if($background) {
				$notification['background'] = $background;
			}


			$color = get_post_meta($id, '_color', true);

			if($color) {
				$notification['color'] = $color;
			}
		}


		$type = get_post_meta($id, '_type', true);

		if($type) {
			$notification['type'] = $type;
		}

		$notifications[] = $notification;
	}

	if(empty($notifications)){
		return;
	}
	
	 $max = ot_get_option('_uncode_notification_max');
	 $max = $max? $max: 3;
	// var_dump($max);
	if(count($notifications) > $max) {
		$notifications = array_slice($notifications, 0, $max);
	}

	$items = json_encode($notifications);

	if(ot_get_option('_uncode_notification_mobile') === 'off'){
		$mobile = 'false';
	} else {
		$mobile = 'true';
	}

	$delay = ot_get_option('_uncode_notification_delay', 5000);
	$placement_x = ot_get_option('_uncode_notification_placement_x', 'right');
	$placement_y = ot_get_option('_uncode_notification_placement_y', 'bottom');
	$offset_x = ot_get_option('_uncode_notification_offset_x', 20);
	$offset_y = ot_get_option('_uncode_notification_offset_y', 20);
	$spacing = ot_get_option('_uncode_notification_spacing', 20);

	echo <<<HTML
<script type="text/javascript">
jQuery(document).ready(function(){

	if(!{$mobile} && jQuery(window).width() < 768) {
		return;
	}
	jQuery.iGrowl.prototype.defaults = {
		type : 			'notice',
		target : 		'blank',
		icon : 			null,
		image : {
			src : null,
			class : null
		},
		small : 		false,
		delay : 		{$delay},
		spacing :  		{$spacing},
		placement : {
			x : 	'{$placement_x}',
			y :		'{$placement_y}'
		},
		offset : {
			x : 	{$offset_x},
			y : 	{$offset_y}
		},
		animation : 	true,
		animShow : 		'fadeInDown',
		animHide : 		'fadeOutUp',
	};
	var items = {$items};

	jQuery.each(items, function(index){
		var options = {};

		if(this.title){
			options.title = this.title;
		}

		if(this.text){
			options.message = this.text;
		}

		if(this.type){
			options.type = this.type;
		}

		if(this.link){
			options.link = this.link;

			if(this.target){
				options.target = this.target;
			}
		}


		jQuery.iGrowl(options);
	});
});
</script>
HTML;

	wp_reset_postdata();
}
