<?php
$sortby = $exclude = $parent_linkable = $show_title = $css_animation = $animation_delay = $animation_speed  = $output = '';
extract(shortcode_atts(array(
	'sortby' => 'menu_order',
	'exclude' => '',
	'parent_linkable' => '',
	'show_title' => '',
	'css_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
) , $atts));

global $post;
$children = wp_list_pages( 'echo=0&child_of=' . $post->ID . '&title_li=' );

if ($children) {
	$parent = $post->ID;
	$parent_linkable = '';
}else{
	$parent = $post->post_parent;
	if(!$parent){
		$parent_linkable = '';
		$parent = $post->ID;
	}
}

$list = wp_list_pages( array('title_li' => '', 'echo' => 0, 'child_of' =>$parent, 'sort_column' => $sortby, 'exclude' => $exclude, 'depth' => 1) );

$parent_title = get_the_title($parent);
$title = empty($content)? $parent_title: $content;

if($parent_linkable === 'yes'){
	$title = '<a href="'.get_permalink($parent).'">'.$title.'</a>';
}

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-subnav', $this->settings['base'], $atts );

$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}

$output .= '<div class="uncode-wrapper '.$css_class.'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';
if($show_title === 'yes'){
	$output .= '<h4 class="uncode-subnav-title">'.$title.'</h4>';
}
$output .= '<ul class="uncode-subnav-list">';
$output .= $list;
$output .= '</ul>';
$output .= '</div>';

echo wpb_js_remove_wpautop($output);