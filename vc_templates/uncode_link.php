<?php
$el_class = $link = $css_animation = $animation_delay = $animation_speed = $output = '';
extract(shortcode_atts(array(
	'el_class' => '',
	'link' => '',
	'css_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
) , $atts));

$link = vc_build_link($link); 
$content = '<a href="'.$link['url'].'" target="'.$link['target'].'" alt="'.$link['title'].'">'.$content.'<i class="uncode-link-icon fa fa-external-link"></i></a>';

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-link', $this->settings['base'], $atts );

$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}

$output .= '<div class="'.$css_class.'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';
$output .= $content;
$output .= '</div>';

echo wpb_js_remove_wpautop($output);