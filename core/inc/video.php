<?php

/**
 * Initialize CPT
 */

function uncode_video_post_type()
{
	$base = 'video';
	$label = __('Video', 'uncode');

	// creating (registering) the custom type
	register_post_type('video',
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
			'query_var' => true,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-format-video',
			 /* this is what order you want it to appear in on the left hand side menu */
			'rewrite' => array(
				'slug' => $base,
				'with_front' => false
			) ,
			 /* you can specify its url slug */
			'has_archive' => false,
			'capability_type' => 'post',
			'hierarchical' => false,

			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array(
				'title',
				'author',
				'thumbnail',
				'excerpt',
				'page-attributes'
			)
		)
	 /* end of options */
	);
	/* end of register post type */

	register_taxonomy(
		'video_category',
		'video',
		array(
			'hierarchical' => false,
			'public' => false,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'labels' => array(
				'name' => sprintf(esc_html__( '%s Categories', 'uncode' ), $label), /* name of the custom taxonomy */
				'singular_name' => sprintf(esc_html__('%s Category', 'uncode'), $label), /* single taxonomy name */
				'search_items' =>  sprintf(esc_html__( 'Search %s Categories', 'uncode'), $label), /* search title for taxomony */
				'all_items' => sprintf(esc_html__( 'All %s Categories', 'uncode'), $label), /* all title for taxonomies */
				'parent_item' => sprintf(esc_html__( 'Parent %s Category', 'uncode'), $label), /* parent title for taxonomy */
				'parent_item_colon' => sprintf(esc_html__( 'Parent %s Category:', 'uncode'), $label), /* parent taxonomy title */
				'edit_item' => sprintf(esc_html__( 'Edit %s Category', 'uncode'), $label), /* edit custom taxonomy title */
				'update_item' => sprintf(esc_html__( 'Update %s Category', 'uncode'), $label), /* update title for taxonomy */
				'add_new_item' => sprintf(esc_html__( 'Add New %s Category', 'uncode'), $label), /* add new title for taxonomy */
				'new_item_name' => sprintf(esc_html__( 'New %s Category Name', 'uncode'), $label) /* name title for taxonomy */
			)
		)
	);

}

add_action('init', 'uncode_video_post_type');

function uncode_video_options(){


	$fields = array(
		array(
			'label' => '<i class="fa fa-menu fa-fw"></i> ' . esc_html__('Video', 'uncode') ,
			'id' => '_uncode_video_tab',
			'type' => 'tab',
		) ,
		run_array_mb(array(
			'id' => '_youku_url',
			'desc' => __('Youku use web page url, qq use flash url.'),
			'label' => esc_html__('Youku url or QQ url', 'uncode') ,
			'type' => 'text',
		)),
	);


	$uncode_page_array = array(
		'id' => '_uncode_video_options',
		'title' => esc_html__('Video Options', 'uncode') ,
		'desc' => '',
		'pages' => array(
			'video'
		) ,
		'context' => 'normal',
		'priority' => 'default',
		'fields' => $fields
	);

	if (function_exists('ot_register_meta_box')) ot_register_meta_box($uncode_page_array);
}

add_action('admin_init', 'uncode_video_options');
