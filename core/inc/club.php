<?php

/**
 * Initialize CPT
 */

function uncode_club_post_type()
{
	$base = 'club';
	$label = __('Club', 'uncode');

	// creating (registering) the custom type
	register_post_type('club',
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
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 8,
			'menu_icon' => 'dashicons-universal-access',
			 /* this is what order you want it to appear in on the left hand side menu */
			 /* you can specify its url slug */
			'has_archive' => false,
			'capability_type' => 'post',
			'hierarchical' => false,

			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'custom-fields',
				'comments',
				'revisions',
				'page-attributes'
			)
		)
	 /* end of options */
	);
	/* end of register post type */
}

add_action('init', 'uncode_club_post_type');
