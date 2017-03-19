<?php
global $uncode_post_types;

$title = $loop = $layout = $read_more_text = $read_more_link = $el_id = $output = $css_animation = $animation_delay = $animation_speed = '';
$post_types = array();
$wc_filtered = array();

extract(shortcode_atts(array(
	'title' => 'Blog',
	'loop' => 'size:10|order_by:date|post_type:post',
	'layout' => 'list', // list, two
	'read_more_text' => 'Read more &gt;',
	'read_more_link' => '',
	'el_id' => '',
	'css_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
) , $atts));

if (isset($uncode_post_types) && !empty($uncode_post_types)) {
  foreach ($uncode_post_types as $key => $value) {
    $post_types[] = $value;
    if (isset($atts[$value . '_items']) && strpos($value, '-') !== false) {
      $new_key = str_replace('-', '_', $value);
      $atts[$new_key . '_items'] = $atts[$value . '_items'];
      unset($atts[$value . '_items']);
      $value = $new_key;
    }
    $attributes_second[$value . '_items'] = 'media|featured,title,type,category,text';
  }
}


$post_types[] = 'post';
$post_types[] = 'page';

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-blog', $this->settings['base'], $atts );

$classes = array();
$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}
if ( empty( $loop ) ) return;


$output .= '<div id="' . esc_attr($el_id) .'" class="uncode-wrapper '.esc_attr($css_class).'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';

$link = vc_build_link($read_more_link); 
$output .= '<div class="uncode-blog-heading clearfix"><h2 class="uncode-blog-title">'.$title.'</h2><a class="uncode-blog-more" href="'.$link['url'].'" target="'.$link['target'].'" alt="'.$link['title'].'">'.$read_more_text.'</a></div>';


$output .= '<div class="uncode-blog-list layout-'.$layout.'">';

global $wp_query, $temp_index_id;
$temp_index_id = $el_id;
$paged = (get_query_var('paged')) ? get_query_var('paged') : (isset($wp_query->query['paged']) ? $wp_query->query['paged'] : 1);

if (class_exists('WC_Query')) {
  $instanceWC_Query = new WC_Query();
  $wc_filtered = $instanceWC_Query->price_filter();
  if (!empty($wc_filtered)) {
    $wc_filtered = (implode(',', array_filter($wc_filtered)));
    $loop .= '|by_id:' . $wc_filtered;
  }
}

if (isset($_GET['upage'])) $paged = $_GET['upage'];
if (isset($infinite) && $infinite !== 'yes') {
  $loop_pagination = $loop;
  if(isset($_GET['ucat'])) $loop .= '|category:'.$_GET['ucat'];
}
$loop .= '|paged:' . $paged;

$this->getLoop( $loop );
$my_query = $this->query;

$args = $this->loop_args;

if (isset($custom_order)&&$custom_order === 'yes') {
  if ($order_ids !== '') {
    $post_list = explode(',', $order_ids);
    $ordered = array();
    foreach($post_list as $key) {
      foreach($my_query->posts as $skey => $spost) {
        if($key == $spost->ID) {
          $ordered[] = $spost;
          unset($my_query->posts[$skey]);
        }
      }
    }
    $my_query->posts = array_merge($ordered, $my_query->posts);
  }
}
$post_blocks = array();
foreach ($post_types as $key => $value) {
  $value = str_replace('-', '_', $value);
if(isset(${$value.'_items'})){
  $post_blocks['uncode_' . $value] = uncode_flatArray(vc_sorted_list_parse_value( ${$value . '_items'} ));
}
}

$posts = array();
$this->filter_categories = array();
while ( $my_query->have_posts() ) {
  $my_query->the_post(); // Get post from query
  $post = new stdClass(); // Creating post object.
  $post->id = get_the_ID();
  $post->title = get_the_title($post->id);
  $post->type = get_post_type( $post->id );
  $post->format = ($post->type === 'post') ? get_post_format( $post->id ) : '';
  $post->link = get_permalink( $post->id );
  $post->content = get_the_content();
  $post_category = $this->getCategoriesCss( $post->id);
  $post->categories_css = $post_category['cat_css'];
  $post->categories_name = $post_category['cat_name'];
  $post->categories_id = $post_category['cat_id'];
  $posts[] = $post;
}
wp_reset_query();
foreach ( $posts as $post ):
	$block_data = array();
  $item_thumb_id = '';
  $item_thumb_id = get_post_thumbnail_id($post->id);
	if ($item_thumb_id === '') {
		$item_thumb_id = get_post_meta( $post->id, '_uncode_featured_media', 1);
		$medias = explode(',', $item_thumb_id);
		if (is_array($medias) && isset($medias[0])) $item_thumb_id = $medias[0];
	}

	$block_data['id'] = $post->id;
	$block_data['classes'] = array(
		'tmb',
		'tmb-light',
		// 'tmb-overlay-text-anim',
		// 'tmb-overlay-anim',
		'tmb-shadowed',
		'tmb-bordered'
	);

	if (!empty($post->format)) $block_data['classes'][] = 'tmb-format-' . $post->format;

    $block_data['content'] = $post->content;
    $block_data['tmb_data'] = array();
    if($layout === 'two'){
      $block_data['single_text'] = 'under';
    } else {
      $block_data['single_text'] = 'left';
    }
    
    $block_data['single_width'] = '10';
    $block_data['single_height'] = '7';
    $block_data['single_back_color'] = 'color-xsdn';
    $block_data['text_padding'] = 'single-block-padding';
    $block_data['media_id'] = $item_thumb_id;
    $block_data['title_classes'] = array('h4');
    $block_data['single_title'] = uncode_truncate( $post->title, 40, '..');
    $typeLayout = array (
    	'title' => array(),
    	'media' => array('featured', 'onpost', 'original'),
    	'meta' => array(),
    	// 'author' => array(),
    	'text' => array('full', '60'),
    );
    $block_data['link'] = array(
	  'url' => $post->link,
	  'target' => '_self'
	);

	$output .= uncode_create_single_block($block_data, $el_id, '', $typeLayout, '', 'no');
endforeach;

$output .= '</div>';
$output .= '</div>';

echo wpb_js_remove_wpautop($output);
