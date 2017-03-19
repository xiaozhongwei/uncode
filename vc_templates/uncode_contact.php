<?php

$address = $phone = $email = $person = $qrcode = $output = '';

extract(shortcode_atts(array(
	'address' => '',
	'phone' => '',
	'email' => '',
	'person' => '',
	'qrcode' => '',
	'css_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
) , $atts));

$content = '';

if($qrcode) {
	$qr_attributes = uncode_get_media_info($qrcode);
	if (!isset($qr_attributes)) $qr_attributes = new stdClass();
	$qr_metavalues = unserialize($qr_attributes->metadata);
	$qr_mime = $qr_attributes->post_mime_type;
	$qr_href = '';

	if (strpos($qr_mime, 'image/') !== false && $qr_mime !== 'image/url' && isset($qr_metavalues['width']) && isset($qr_metavalues['height'])) {
		$image_orig_w = $qr_metavalues['width'];
		$image_orig_h = $qr_metavalues['height'];
		$big_image = uncode_resize_image($qr_attributes->guid, $qr_attributes->path, $image_orig_w, $image_orig_h, 12, null, false);
		$qr_href = $big_image['url'];
	} else if ($qr_mime === 'image/url') {
		$qr_href = $qr_attributes->guid;
	}

	if($qr_href) {
		$content .= '<div class="uncode-contact-qrcode">';
		$content .= '<img src="'.$qr_href.'" />';
		$content .= '</div>';
	}
}

$content .= '<ul class="uncode-contact-content">';
if($address) {
	$content .= '<li><i class="fa fa-map-marker"></i>'. $address .'</li>';
}
if($phone) {
	$content .= '<li><i class="fa fa-phone"></i>'. $phone .'</li>';
}
if($email) {
	$content .= '<li><i class="fa fa-envelope"></i><a href="mailto:'.$email.'">'. $email .'</a></li>';
}
if($person) {
	$content .= '<li><i class="fa fa-user"></i>'. $person .'</li>';
}
$content .= '</ul>';



$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-contact', $this->settings['base'], $atts );

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