<?php

/**
 * uncode Theme Customizer
 *
 * @package uncode
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function uncode_customize_register($wp_customize)
{
	$wp_customize->get_setting('blogname')->transport = 'postMessage';
	$wp_customize->get_setting('blogdescription')->transport = 'postMessage';
	$wp_customize->get_setting('header_textcolor')->transport = 'postMessage';
}
add_action('customize_register', 'uncode_customize_register');

function uncode_custom_excerpt_length($length)
{
	return 20;
}
add_filter('excerpt_length', 'uncode_custom_excerpt_length', 999);

/**
 * Customization IRecommendThis
 */
if (class_exists('DOT_IRecommendThis'))
{
	class UNCODE_IRecommendThis extends DOT_IRecommendThis
	{
		function __construct($file)
		{
			remove_action('init', array(&$this,'add_widget_most_recommended_posts'));
			add_action('init', array(&$this,'uncode_add_widget_most_recommended_posts'));
			add_action( 'wp_ajax_uncode-dot-irecommendthis', array( &$this, 'uncode_ajax_callback' ) );
			add_action( 'wp_ajax_nopriv_uncode-dot-irecommendthis', array( &$this, 'uncode_ajax_callback' ) );
		}

		function dot_recommend_this($post_id, $text_zero_suffix = false, $text_one_suffix = false, $text_more_suffix = false, $action = 'get')
		{
			if (!is_numeric($post_id)) return;
			$text_zero_suffix = strip_tags($text_zero_suffix);
			$text_one_suffix = strip_tags($text_one_suffix);
			$text_more_suffix = strip_tags($text_more_suffix);

			switch ($action)
			{
				case 'get':
					$recommended = get_post_meta($post_id, '_recommended', true);
					if (!$recommended)
					{
						$recommended = 0;
						add_post_meta($post_id, '_recommended', $recommended, true);
					}

					if ($recommended == 0)
					{
						$suffix = $text_zero_suffix;
					}
					elseif ($recommended == 1)
					{
						$suffix = $text_one_suffix;
					}
					else
					{
						$suffix = $text_more_suffix;
					}

					/* Hides the count is the count is zero. */
					$options = get_option('dot_irecommendthis_settings');
					if (!isset($options['hide_zero'])) $options['hide_zero'] = '0';

					if (($recommended == 0) && $options['hide_zero'] == 1)
					{
						$output = '<span class="extras-wrap"><i class="fa fa-heart3"></i><span><span class="likes-counter">0</span> ' . esc_html__('Like', 'uncode') . '</span></span>';
						return $output;
					}
					else
					{
						$output = '<span class="extras-wrap"><i class="fa fa-heart3"></i><span><span class="likes-counter">' . $recommended . '</span> ' . esc_html__('Likes', 'uncode') . '</span></span>';
						return $output;
					}

					break;

				case 'update':

					$recommended = get_post_meta($post_id, '_recommended', true);

					$options = get_option('dot_irecommendthis_settings');
					if (!isset($options['disable_unique_ip'])) $options['disable_unique_ip'] = '0';

					/* Check if Unique IP saving is required or disabled */
					if ($options['disable_unique_ip'] != 0)
					{

						if (isset($_COOKIE['dot_irecommendthis_' . $post_id]))
						{
							return $recommended;
						}

						$recommended++;
						update_post_meta($post_id, '_recommended', $recommended);
						setcookie('dot_irecommendthis_' . $post_id, time() , time() + 3600 * 24 * 365, '/');
					}
					else
					{

						global $wpdb;
						$ip = $_SERVER['REMOTE_ADDR'];
						$voteStatusByIp = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "irecommendthis_votes WHERE post_id = %d AND ip = %s", $post_id, $ip));

						if (isset($_COOKIE['dot_irecommendthis_' . $post_id]) || $voteStatusByIp != 0)
						{
							return $recommended;
						}

						$recommended++;
						update_post_meta($post_id, '_recommended', $recommended);
						setcookie('dot_irecommendthis_' . $post_id, time() , time() + 3600 * 24 * 365, '/');
						$wpdb->query($wpdb->prepare("INSERT INTO " . $wpdb->prefix . "irecommendthis_votes VALUES ('', NOW(), %d, %s)", $post_id, $ip));
					}

					if ($recommended == 0)
					{
						$suffix = $text_zero_suffix;
					}
					elseif ($recommended == 1)
					{
						$suffix = $text_one_suffix;
					}
					else
					{
						$suffix = $text_more_suffix;
					}

					$output = '<i class="fa fa-heart3"></i><span>' . $recommended . '</span>';
					$dot_irt_html = apply_filters('dot_irt_before_count', $output);

					return $dot_irt_html;
					break;
			}
		}

		//dot_recommend_this

		function dot_recommend($id = null, $wrap = true)
		{

			global $wpdb;
			$ip = $_SERVER['REMOTE_ADDR'];
			$post_ID = $id ? $id : get_the_ID();
			global $post;

			$options = get_option('dot_irecommendthis_settings');
			if (!isset($options['text_zero_suffix'])) $options['text_zero_suffix'] = '';
			if (!isset($options['text_one_suffix'])) $options['text_one_suffix'] = '';
			if (!isset($options['text_more_suffix'])) $options['text_more_suffix'] = '';
			if (!isset($options['link_title_new'])) $options['link_title_new'] = '';
			if (!isset($options['link_title_active'])) $options['link_title_active'] = '';
			if (!isset($options['disable_unique_ip'])) $options['disable_unique_ip'] = '0';
			 //Check if Unique IP saving is required or disabled

			if ($options['disable_unique_ip'] != '1')
			{
				$voteStatusByIp = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "irecommendthis_votes WHERE post_id = %d AND ip = %s", $post_ID, $ip));
			}

			$output = self::dot_recommend_this($post_ID, $options['text_zero_suffix'], $options['text_one_suffix'], $options['text_more_suffix']);

			if ($options['disable_unique_ip'] != '0')
			{

				if (!isset($_COOKIE['dot_irecommendthis_' . $post_ID]))
				{
					$class = 'uncode-dot-irecommendthis';

					if ($options['link_title_new'] == '')
					{
						$title = esc_html__('Recommend this', 'uncode');
					}
					else
					{
						$title = $options['link_title_new'];
					}
				}
				else
				{
					$class = 'uncode-dot-irecommendthis active';
					if ($options['link_title_active'] == '')
					{
						$title = esc_html__('You already recommended this', 'uncode');
					}
					else
					{
						$title = $options['link_title_active'];
					}
				}
			}
			else
			{
				if (!isset($_COOKIE['dot_irecommendthis_' . $post_ID]) && $voteStatusByIp == 0)
				{
					$class = 'uncode-dot-irecommendthis';
					if ($options['link_title_new'] == '')
					{
						$title = esc_html__('Recommend this', 'uncode');
					}
					else
					{
						$title = $options['link_title_new'];
					}
				}
				else
				{
					$class = 'uncode-dot-irecommendthis active';
					if ($options['link_title_active'] == '')
					{
						$title = esc_html__('You already recommended this', 'uncode');
					}
					else
					{
						$title = $options['link_title_active'];
					}
				}
			}

			if ($wrap)
			{
				$dot_irt_html = '<a href="#" class="' . $class . '" id="dot-irecommendthis-' . $post_ID . '" title="' . $title . '">';
				$dot_irt_html.= apply_filters('dot_irt_before_count', $output);
				$dot_irt_html.= '</a>';
			}
			else
			{
				$dot_irt_html = '<span class="' . $class . '">';
				$dot_irt_html.= apply_filters('dot_irt_before_count', $output);
				$dot_irt_html.= '</span>';
			}

			return $dot_irt_html;
		}

		/*--------------------------------------------*
		 * AJAX Callback
		 *--------------------------------------------*/

		function uncode_ajax_callback($post_id)
		{
			$options = get_option( 'dot_irecommendthis_settings' );
			if( !isset($options['add_to_posts']) ) $options['add_to_posts'] = '1';
			if( !isset($options['add_to_other']) ) $options['add_to_other'] = '1';
			if( !isset($options['text_zero_suffix']) ) $options['text_zero_suffix'] = '';
			if( !isset($options['text_one_suffix']) ) $options['text_one_suffix'] = '';
			if( !isset($options['text_more_suffix']) ) $options['text_more_suffix'] = '';

			if( isset($_POST['recommend_id']) ) {
				// Click event. Get and Update Count
				$post_id = str_replace('dot-irecommendthis-', '', $_POST['recommend_id']);
				echo $this->dot_recommend_this($post_id, $options['text_zero_suffix'], $options['text_one_suffix'], $options['text_more_suffix'], 'update');
			} else {
				// AJAXing data in. Get Count
				$post_id = str_replace('dot-irecommendthis-', '', $_POST['post_id']);
				echo $this->dot_recommend_this($post_id, $options['text_zero_suffix'], $options['text_one_suffix'], $options['text_more_suffix'], 'get');
			}

			exit;

		}	//ajax_callback

		/*--------------------------------------------*
		 * Widget
		 *--------------------------------------------*/

		function uncode_add_widget_most_recommended_posts()
		{
			wp_unregister_sidebar_widget('most_recommended_posts');
		}
	}

	global $uncode_irecommendthis;

	// Initiation call of plugin
	$uncode_irecommendthis = new UNCODE_IRecommendThis(WP_PLUGIN_DIR . '/i-recommend-this/dot-irecommendthis.php');

	// register Most_recommended_posts widget
	function register_most_recommended_posts()
	{
		register_widget('Most_recommended_posts');
	}
	add_action('widgets_init', 'register_most_recommended_posts');
}

function register_theme_wigets()
{
	register_widget('Theme_Widget_SubNav');
	register_widget('Theme_Widget_Recent_Posts');
	register_widget('Theme_Widget_Popular_Posts');
}
add_action('widgets_init', 'register_theme_wigets');

/**
 * Sub Navigation Widget Class
 */
class Theme_Widget_SubNav extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => 'widget_subnav', 'description' => __( 'Displays a list of SubPages', 'uncode') );
		parent::__construct('subnav', __('Sub Navigation', 'uncode'), $widget_ops);
	}
	function widget( $args, $instance ) {
		extract( $args );
		
		$sortby = empty( $instance['sortby'] ) ? 'menu_order' : $instance['sortby'];
		$exclude = empty( $instance['exclude'] ) ? '' : $instance['exclude'];
		$parent_linkable = $instance['parent_linkable'] ? '1' : '0';
		global $post;
		$children=wp_list_pages( 'echo=0&child_of=' . $post->ID . '&title_li=' );
		if ($children) {
			$parent = $post->ID;
			$parent_linkable = 0;
		}else{
			$parent = $post->post_parent;
			if(!$parent){
				$parent_linkable = 0;
				$parent = $post->ID;
			}
		}
		$parent_title = get_the_title($parent);
		$title = apply_filters('widget_title', empty($instance['title']) ? $parent_title : $instance['title'], $instance, $this->id_base);
		$output = wp_list_pages( array('title_li' => '', 'echo' => 0, 'child_of' =>$parent, 'sort_column' => $sortby, 'exclude' => $exclude, 'depth' => 1) );
		if($parent_linkable){
			$title='<a href="'.get_permalink($parent).'">'.$title.'</a>';
		}
		if ( !empty( $output ) ) {
			echo $before_widget;
			if ( $title)
				echo '<div>'. $title . '</div>';
		?>
		<ul>
			<?php echo $output; ?>
		</ul>
		<?php
			echo $after_widget;
		}
	}
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( in_array( $new_instance['sortby'], array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$instance['sortby'] = $new_instance['sortby'];
		} else {
			$instance['sortby'] = 'menu_order';
		}
		$instance['exclude'] = strip_tags( $new_instance['exclude'] );
		$instance['parent_linkable'] = !empty($new_instance['parent_linkable']) ? 1 : 0;
		return $instance;
	}
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'sortby' => 'menu_order', 'title' => '', 'exclude' => '') );
		$parent_linkable = isset( $instance['parent_linkable'] ) ? (bool) $instance['parent_linkable'] : false;
		$title = esc_attr( $instance['title'] );
		$exclude = esc_attr( $instance['exclude'] );
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'uncode'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('sortby'); ?>"><?php _e( 'Sort by:', 'uncode'); ?></label>
			<select name="<?php echo $this->get_field_name('sortby'); ?>" id="<?php echo $this->get_field_id('sortby'); ?>" class="widefat">
				<option value="menu_order"<?php selected( $instance['sortby'], 'menu_order' ); ?>><?php _e('Page order', 'uncode'); ?></option>
				<option value="post_title"<?php selected( $instance['sortby'], 'post_title' ); ?>><?php _e('Page title', 'uncode'); ?></option>
				<option value="ID"<?php selected( $instance['sortby'], 'ID' ); ?>><?php _e( 'Page ID', 'uncode' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e( 'Exclude:', 'uncode' ); ?></label> <input type="text" value="<?php echo $exclude; ?>" name="<?php echo $this->get_field_name('exclude'); ?>" id="<?php echo $this->get_field_id('exclude'); ?>" class="widefat" />
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ,'uncode'); ?></small>
		</p>
		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('parent_linkable'); ?>" name="<?php echo $this->get_field_name('parent_linkable'); ?>"<?php checked( $parent_linkable ); ?> />
		<label for="<?php echo $this->get_field_id('parent_linkable'); ?>"><?php _e( 'Make Parent Linkable?', 'uncode' ); ?></label></p>
<?php
	}
}

/**
 * Recent_Posts Widget Class
 *
 * @since 2.8.0
 */
class Theme_Widget_Recent_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_recent_posts', 'description' => __( "Displays the recent posts on your site", 'uncode') );
		parent::__construct('recent_posts', __('Recent Posts', 'uncode'), $widget_ops);
		$this->alt_option_name = 'widget_recent_posts';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}
	
	function get_all_author(){
		global $wpdb;
		$order = 'user_id';
		$user_ids = $wpdb->get_col($wpdb->prepare("SELECT $wpdb->usermeta.user_id FROM $wpdb->usermeta where meta_key='{$wpdb->prefix}user_level' and meta_value>=1 ORDER BY %s ASC",$order));

		foreach($user_ids as $user_id) :
			$user = get_userdata($user_id);
			$all_authors[$user_id] = $user->display_name;
		endforeach;
		return $all_authors;
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('theme_widget_recent_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts', 'theme_front') : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
		
		if ( !$desc_length = (int) $instance['desc_length'] )
			$desc_length = 80;
		else if ( $desc_length < 1 )
			$desc_length = 1;

		if ( !$title_length = (int) $instance['title_length'] )
			$title_length = '';
		else if ( $title_length < 1 )
			$title_length = '';
		

		$big_thumbnail = $instance['big_thumbnail'] ? '1': '0';
		$thumbnail_size = array(65,65);
		if($big_thumbnail) {
			$thumbnail_size = array(240,150);
		}
		$disable_thumbnail = $instance['disable_thumbnail'] ? '1' : '0';
		
		$display_extra_type = $instance['display_extra_type'] ? $instance['display_extra_type'] :'time';
		if($display_extra_type == 'both'){
			$display_extra_type = array('time','description');
		}else{
			$display_extra_type = array($display_extra_type);
		}
		$query = array('showposts' => $number, 'nopaging' => 0, 'orderby'=> 'date', 'order'=>'DESC', 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		if(!empty($instance['cat'])){
			$query['cat'] = implode(',', $instance['cat']);
		}
		if(!empty($instance['authors'])){
			$query['author'] = implode(',', $instance['authors']);
		}
		
		$r = new WP_Query($query);
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="posts_list<?php if($big_thumbnail) echo ' posts_list_big';?>">
<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li>
<?php if(!$disable_thumbnail):?>
<?php if (has_post_thumbnail() ): ?>
				<a class="thumbnail" href="<?php echo get_permalink() ?>" title="<?php the_title();?>">
					<?php the_post_thumbnail($thumbnail_size,array('title'=>get_the_title(),'alt'=>get_the_title())); ?>
				</a>
<?php elseif(!$big_thumbnail):
	$default_thumbnail_image = get_template_directory_uri().'/library/img/widget_posts_thumbnail.png';
?>
				<a class="thumbnail" href="<?php echo get_permalink() ?>" title="<?php the_title();?>">
					<img src="<?php echo $default_thumbnail_image;?>" width="<?php echo $thumbnail_size;?>" height="<?php echo $thumbnail_size;?>" title="<?php the_title();?>" alt="<?php the_title();?>"/>
				</a>
<?php endif;//end has_post_thumbnail ?>
<?php endif;//disable_thumbnail ?>
				<div class="post_extra_info">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
						<?php 
							if( get_the_title() ) { 
								if((int)$title_length){
									echo uncode_truncate(get_the_title(),$title_length);
								}else{
									echo get_the_title();
								}
							}else {
								the_ID();
							}
						?>
					</a>
<?php if(in_array('time', $display_extra_type)):?>
					<time datetime="<?php the_time('Y-m-d') ?>"><?php echo get_the_date(); ?></time>
<?php endif;?>
<?php if(in_array('description', $display_extra_type)):?>
					<p><?php echo wp_html_excerpt(get_the_excerpt(),$desc_length);?>...</p>
<?php endif;//end display extra type ?>
				</div>
				<div class="clearboth"></div>
			</li>
<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_query();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('theme_widget_recent_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['title_length'] = (int) $new_instance['title_length'];
		$instance['desc_length'] = (int) $new_instance['desc_length'];
		$instance['big_thumbnail'] = !empty($new_instance['big_thumbnail']) ? 1 : 0;
		$instance['disable_thumbnail'] = !empty($new_instance['disable_thumbnail']) ? 1 : 0;
		$instance['display_extra_type'] = $new_instance['display_extra_type'];
		$instance['cat'] = $new_instance['cat'];
		$instance['authors'] = $new_instance['authors'];
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['theme_widget_recent_posts']) )
			delete_option('theme_widget_recent_posts');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('theme_widget_recent_posts', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$big_thumbnail = isset( $instance['big_thumbnail'] ) ? (bool) $instance['big_thumbnail'] : false;
		$disable_thumbnail = isset( $instance['disable_thumbnail'] ) ? (bool) $instance['disable_thumbnail'] : false;
		$display_extra_type = isset( $instance['display_extra_type'] ) ? $instance['display_extra_type'] : 'time';
		$cat = isset($instance['cat']) ? $instance['cat'] : array();
		$authors = isset($instance['authors']) ? $instance['authors'] : array();
		$authors_list = $this->get_all_author();
		if(empty($authors_list)){
			$authors_list = array();
		}
		
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;

		if ( !isset($instance['title_length']) || !$title_length = (int) $instance['title_length'] )
			$title_length = '';

		if ( !isset($instance['desc_length']) || !$desc_length = (int) $instance['desc_length'] )
			$desc_length = 80;

		$categories = get_categories('orderby=name&hide_empty=0');
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'uncode'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'uncode'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('disable_thumbnail'); ?>" name="<?php echo $this->get_field_name('disable_thumbnail'); ?>"<?php checked( $disable_thumbnail ); ?> />
		<label for="<?php echo $this->get_field_id('disable_thumbnail'); ?>"><?php _e( 'Disable Post Thumbnail?', 'uncode' ); ?></label></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('big_thumbnail'); ?>" name="<?php echo $this->get_field_name('big_thumbnail'); ?>"<?php checked( $big_thumbnail ); ?> />
		<label for="<?php echo $this->get_field_id('big_thumbnail'); ?>"><?php _e( 'Show Big Thumbnail?', 'uncode' ); ?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('title_length'); ?>"><?php _e('Length of Title to show:', 'uncode'); ?></label>
		<input id="<?php echo $this->get_field_id('title_length'); ?>" name="<?php echo $this->get_field_name('title_length'); ?>" type="text" value="<?php echo $title_length; ?>" size="3" /></p>

		<p>
			<label for="<?php echo $this->get_field_id('display_extra_type'); ?>"><?php _e( 'Display Extra infomation type:', 'uncode' ); ?></label>
			<select name="<?php echo $this->get_field_name('display_extra_type'); ?>" id="<?php echo $this->get_field_id('display_extra_type'); ?>" class="widefat">
				<option value="time"<?php selected($display_extra_type,'time');?>><?php _e( 'Time', 'uncode' ); ?></option>
				<option value="description"<?php selected($display_extra_type,'description');?>><?php _e( 'Description', 'uncode' ); ?></option>
				<option value="both"<?php selected($display_extra_type,'both');?>><?php _e( 'Time and Description', 'uncode' ); ?></option>
				<option value="none"<?php selected($display_extra_type,'none');?>><?php _e( 'None', 'uncode' ); ?></option>
			</select>
		</p>
		
		<p><label for="<?php echo $this->get_field_id('desc_length'); ?>"><?php _e('Length of Description to show:', 'uncode'); ?></label>
		<input id="<?php echo $this->get_field_id('desc_length'); ?>" name="<?php echo $this->get_field_name('desc_length'); ?>" type="text" value="<?php echo $desc_length; ?>" size="3" /></p>

		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e( 'Categorys:' , 'uncode'); ?></label>
			<select style="height:5.5em" name="<?php echo $this->get_field_name('cat'); ?>[]" id="<?php echo $this->get_field_id('cat'); ?>" class="widefat" multiple="multiple">
				<?php foreach($categories as $category):?>
				<option value="<?php echo $category->term_id;?>"<?php echo in_array($category->term_id, $cat)? ' selected="selected"':'';?>><?php echo $category->name;?></option>
				<?php endforeach;?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('authors'); ?>"><?php _e( 'Authors:' , 'uncode'); ?></label>
			<select style="height:5.5em" name="<?php echo $this->get_field_name('authors'); ?>[]" id="<?php echo $this->get_field_id('authors'); ?>" class="widefat" multiple="multiple">
				<?php foreach($authors_list as $user_id => $display_name):?>
				<option value="<?php echo $user_id;?>"<?php echo in_array($user_id, $authors)? ' selected="selected"':'';?>><?php echo $display_name;?></option>
				<?php endforeach;?>
			</select>
		</p>
<?php
	}
}


/**
 * Popular_Posts Widget Class
 */
class Theme_Widget_Popular_Posts extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_popular_posts', 'description' => __( "Displays the popular posts on your site", 'uncode') );
		parent::__construct('popular_posts', __('Popular Posts', 'uncode'), $widget_ops);
		$this->alt_option_name = 'widget_popular_posts';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function get_all_author(){
		global $wpdb;
		$order = 'user_id';
		$user_ids = $wpdb->get_col($wpdb->prepare("SELECT $wpdb->usermeta.user_id FROM $wpdb->usermeta where meta_key='{$wpdb->prefix}user_level' and meta_value>=1 ORDER BY %s ASC",$order));

		foreach($user_ids as $user_id) :
			$user = get_userdata($user_id);
			$all_authors[$user_id] = $user->display_name;
		endforeach;
		return $all_authors;
	}
	
	function widget($args, $instance) {
		$cache = wp_cache_get('theme_widget_popular_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Popular Posts', 'theme_front') : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;
		
		if ( !$desc_length = (int) $instance['desc_length'] )
			$desc_length = 80;
		else if ( $desc_length < 1 )
			$desc_length = 1;

		if ( !$title_length = (int) $instance['title_length'] )
			$title_length = '';
		else if ( $title_length < 1 )
			$title_length = '';
		
		$big_thumbnail = $instance['big_thumbnail'] ? '1': '0';
		$thumbnail_size = array(65,65);
		if($big_thumbnail) {
			$thumbnail_size = array(280,180);
		}
		$disable_thumbnail = $instance['disable_thumbnail'] ? '1' : '0';
		$display_extra_type = $instance['display_extra_type'] ? $instance['display_extra_type'] :'time';
		if($display_extra_type == 'both'){
			$display_extra_type = array('time','description');
		}else{
			$display_extra_type = array($display_extra_type);
		}
		$query = array('showposts' => $number, 'nopaging' => 0, 'orderby'=> 'comment_count', 'post_status' => 'publish', 'ignore_sticky_posts' => 1);
		if(!empty($instance['cat'])){
			$query['cat'] = implode(',', $instance['cat']);
		}
		if(!empty($instance['authors'])){
			$query['author'] = implode(',', $instance['authors']);
		}
		
		
		$r = new WP_Query($query);
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul class="posts_list<?php if($big_thumbnail) echo ' posts_list_big';?>">
<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li>
<?php if(!$disable_thumbnail):?>
<?php if (has_post_thumbnail() ): ?>
				<a class="thumbnail" href="<?php echo get_permalink() ?>" title="<?php the_title();?>">
					<?php the_post_thumbnail($thumbnail_size,array('title'=>get_the_title(),'alt'=>get_the_title())); ?>
				</a>
<?php elseif(!$big_thumbnail):
	$default_thumbnail_image = get_template_directory_uri().'/library/img/widget_posts_thumbnail.png';
?>
				<a class="thumbnail" href="<?php echo get_permalink() ?>" title="<?php the_title();?>">
					<img src="<?php echo $default_thumbnail_image;?>" width="<?php echo $thumbnail_size;?>" height="<?php echo $thumbnail_size;?>" title="<?php the_title();?>" alt="<?php the_title();?>"/>
				</a>
<?php endif;//end has_post_thumbnail ?>
<?php endif;//disable_thumbnail ?>
				<div class="post_extra_info">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
						<?php 
							if( get_the_title() ) { 
								if((int)$title_length){
									echo uncode_truncate(get_the_title(),$title_length);
								}else{
									echo get_the_title();
								}
							}else {
								the_ID();
							}
						?>
					</a>
<?php if(in_array('time', $display_extra_type)):?>
					<time datetime="<?php the_time('Y-m-d') ?>"><?php echo get_the_date(); ?></time>
<?php endif;?>
<?php if(in_array('description', $display_extra_type)):?>
					<p><?php echo wp_html_excerpt(get_the_excerpt(),$desc_length);?>...</p>
<?php endif;//end display extra type ?>
				</div>
				<div class="clearboth"></div>
			</li>
<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_query();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('theme_widget_popular_posts', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['title_length'] = (int) $new_instance['title_length'];
		$instance['desc_length'] = (int) $new_instance['desc_length'];
		$instance['disable_thumbnail'] = !empty($new_instance['disable_thumbnail']) ? 1 : 0;
		$instance['big_thumbnail'] = !empty($new_instance['big_thumbnail']) ? 1 : 0;
		$instance['display_extra_type'] = $new_instance['display_extra_type'];
		$instance['cat'] = $new_instance['cat'];
		$instance['authors'] = $new_instance['authors'];
		
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['theme_widget_popular_posts']) )
			delete_option('theme_widget_popular_posts');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('theme_widget_popular_posts', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$disable_thumbnail = isset( $instance['disable_thumbnail'] ) ? (bool) $instance['disable_thumbnail'] : false;
		$big_thumbnail = isset( $instance['big_thumbnail'] ) ? (bool) $instance['big_thumbnail'] : false;
		$display_extra_type = isset( $instance['display_extra_type'] ) ? $instance['display_extra_type'] : 'time';
		$cat = isset($instance['cat']) ? $instance['cat'] : array();
		$authors = isset($instance['authors']) ? $instance['authors'] : array();
		$authors_list = $this->get_all_author();
		if(empty($authors_list)){
			$authors_list = array();
		}
		
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;

		if ( !isset($instance['title_length']) || !$title_length = (int) $instance['title_length'] )
			$title_length = '';

		if ( !isset($instance['desc_length']) || !$desc_length = (int) $instance['desc_length'] )
			$desc_length = 80;
		

		$categories = get_categories('orderby=name&hide_empty=0');

?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','uncode'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:', 'uncode'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('disable_thumbnail'); ?>" name="<?php echo $this->get_field_name('disable_thumbnail'); ?>"<?php checked( $disable_thumbnail ); ?> />
		<label for="<?php echo $this->get_field_id('disable_thumbnail'); ?>"><?php _e( 'Disable Post Thumbnail?' , 'uncode'); ?></label></p>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('big_thumbnail'); ?>" name="<?php echo $this->get_field_name('big_thumbnail'); ?>"<?php checked( $big_thumbnail ); ?> />
		<label for="<?php echo $this->get_field_id('big_thumbnail'); ?>"><?php _e( 'Show Big Thumbnail?', 'uncode' ); ?></label></p>
		
		<p><label for="<?php echo $this->get_field_id('title_length'); ?>"><?php _e('Length of Title to show:', 'uncode'); ?></label>
		<input id="<?php echo $this->get_field_id('title_length'); ?>" name="<?php echo $this->get_field_name('title_length'); ?>" type="text" value="<?php echo $title_length; ?>" size="3" /></p>

		<p>
			<label for="<?php echo $this->get_field_id('display_extra_type'); ?>"><?php _e( 'Display Extra infomation type:', 'uncode' ); ?></label>
			<select name="<?php echo $this->get_field_name('display_extra_type'); ?>" id="<?php echo $this->get_field_id('display_extra_type'); ?>" class="widefat">
				<option value="time"<?php selected($display_extra_type,'time');?>><?php _e( 'Time', 'uncode' ); ?></option>
				<option value="description"<?php selected($display_extra_type,'description');?>><?php _e( 'Description', 'uncode' ); ?></option>
				<option value="both"<?php selected($display_extra_type,'both');?>><?php _e( 'Time and Description', 'uncode' ); ?></option>
				<option value="none"<?php selected($display_extra_type,'none');?>><?php _e( 'None', 'uncode' ); ?></option>
			</select>
		</p>
		
		<p><label for="<?php echo $this->get_field_id('desc_length'); ?>"><?php _e('Length of Description to show:', 'uncode'); ?></label>
		<input id="<?php echo $this->get_field_id('desc_length'); ?>" name="<?php echo $this->get_field_name('desc_length'); ?>" type="text" value="<?php echo $desc_length; ?>" size="3" /></p>

		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e( 'Categorys:' , 'uncode'); ?></label>
			<select style="height:5.5em" name="<?php echo $this->get_field_name('cat'); ?>[]" id="<?php echo $this->get_field_id('cat'); ?>" class="widefat" multiple="multiple">
				<?php foreach($categories as $category):?>
				<option value="<?php echo $category->term_id;?>"<?php echo in_array($category->term_id, $cat)? ' selected="selected"':'';?>><?php echo $category->name;?></option>
				<?php endforeach;?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('authors'); ?>"><?php _e( 'Authors:' , 'uncode'); ?></label>
			<select style="height:5.5em" name="<?php echo $this->get_field_name('authors'); ?>[]" id="<?php echo $this->get_field_id('authors'); ?>" class="widefat" multiple="multiple">
				<?php foreach($authors_list as $user_id => $display_name):?>
				<option value="<?php echo $user_id;?>"<?php echo in_array($user_id, $authors)? ' selected="selected"':'';?>><?php echo $display_name;?></option>
				<?php endforeach;?>
			</select>
		</p>
<?php
	}
}
/**
 * Adds Most_recommended_posts widget.
 */
class Most_recommended_posts extends WP_Widget
{

	/**
	 * Register widget with WordPress.
	 */
	function __construct()
	{
		parent::__construct('most-recommended-posts', // Base ID
			esc_html__('Most recommended posts', 'dot') , // Name
			array('description' => esc_html__('Your site’s most liked posts.', 'dot') ,) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance)
	{

		$numberOf = $instance['number'];
		$show_count = $instance['show_count'];
		$title = $instance['title'];
		$before_widget = $args['before_widget'];
		$after_widget = $args['after_widget'];
		$before_title = $args['before_title'];
		$after_title = $args['after_title'];

		$widget_before = $before_widget;
		$widget_before .= $before_title . $title . $after_title;
		echo wp_kses_post($widget_before);
		echo '<ul class="mostrecommendedposts">';

		most_recommended_posts($numberOf, '<li>', '</li>', $show_count);

		echo '</ul>';
		echo wp_kses_post($after_widget);
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance)
	{
		$title = !empty($instance['title']) ? $instance['title'] : esc_html__('Most recommended posts', 'dot');
		$number = !empty($instance['number']) ? $instance['number'] : 5;
		$show_count = !empty($instance['show_count']) ? true : false;
		?>
    <p>
        <label for="<?php echo  esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'dot'); ?></label>
        <input class="widefat" id="<?php echo  esc_attr($this->get_field_id('title')); ?>" name="<?php echo  esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
    </p>
    <p>
        <label for="<?php echo  esc_attr($this->get_field_id('number')); ?>"><?php esc_html_e('Number of posts to show:', 'dot'); ?></label><br />
        <input id="<?php echo  esc_attr($this->get_field_id('number')); ?>" name="<?php echo  esc_attr($this->get_field_name('number')); ?>" type="text" value="<?php echo esc_attr($number); ?>" style="width: 35px;"> <small>(max. 15)</small></label></p>
    </p>
    <p>
        <label for="<?php echo  esc_attr($this->get_field_id('show_count')); ?>"><?php esc_html_e('Show post count', 'dot'); ?></label>
        <input class="checkbox" type="checkbox" <?php checked($instance['show_count'], '1'); ?> value="1" id="<?php echo  esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo  esc_attr($this->get_field_name('show_count')); ?>" />
    </p>
  	<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['number'] = (!empty($new_instance['number'])) ? strip_tags($new_instance['number']) : '';
		$instance['show_count'] = (!empty($new_instance['show_count'])) ? $new_instance['show_count'] : false;

		return $instance;
	}
}

function uncode_wpcf7_ajax_loader() {
	return get_template_directory_uri() . '/library/img/fading-squares.gif';
}

add_filter( 'wpcf7_ajax_loader', 'uncode_wpcf7_ajax_loader', 10 );
