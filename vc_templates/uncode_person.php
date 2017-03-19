<?php

$address = $phone = $email = $person = $avatar = $output = '';

extract(shortcode_atts(array(
	'address' => '',
	'phone' => '',
	'email' => '',
	'avatar' => '',
	'css_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
) , $atts));

if($content) {
	$content = '<h4>'.$content.'</h4>';
}

if($avatar) {
	$avatar_attributes = uncode_get_media_info($avatar);
	if (!isset($avatar_attributes)) $avatar_attributes = new stdClass();
	$avatar_metavalues = unserialize($avatar_attributes->metadata);
	$avatar_mime = $avatar_attributes->post_mime_type;
	$avatar_href = '';

	if (strpos($avatar_mime, 'image/') !== false && $avatar_mime !== 'image/url' && isset($avatar_metavalues['width']) && isset($avatar_metavalues['height'])) {
		$image_orig_w = $avatar_metavalues['width'];
		$image_orig_h = $avatar_metavalues['height'];
		$big_image = uncode_resize_image($avatar_attributes->guid, $avatar_attributes->path, $image_orig_w, $image_orig_h, 12, null, false);
		$avatar_href = $big_image['url'];
	} else if ($avatar_mime === 'image/url') {
		$avatar_href = $avatar_attributes->guid;
	}

	if($avatar_href) {
		$content = '<div class="uncode-person-avatar"><img src="'.$avatar_href.'" /></div>'.$content;
	}
}

$content .= '<ul class="uncode-person-content">';
if($address) {
	$content .= '<li><i class="fa fa-map-marker"></i>'. $address .'</li>';
}
if($phone) {
	$content .= '<li><i class="fa fa-phone"></i>'. $phone .'</li>';
}
if($email) {
	$content .= '<li><i class="fa fa-envelope"></i><a href="mailto:'.$email.'">'. $email .'</a></li>';
}
$content .= '</ul>';



$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-person', $this->settings['base'], $atts );

$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}

$output .= '<div class="uncode-wrapper '.$css_class.'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';
$output .= $content;
$output .= '<div class="clear"></div></div>';

echo wpb_js_remove_wpautop($output);