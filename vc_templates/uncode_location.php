<?php

$address = $phone = $email = $person = $qrcode = $output = '';

extract(shortcode_atts(array(
	'address' => '',
	'phone' => '',
	'email' => '',
	'person' => '',
	'css_animation' => '',
	'animation_delay' => '',
	'animation_speed' => '',
) , $atts));

if($content) {
	$content = '<h4>'.$content.'</h4>';
}

if($address) {
	$content .= '<div class="uncode-location-detail">'. $address .'</div>';
}

$content .= '<ul class="uncode-location-ul">';

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


$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-location', $this->settings['base'], $atts );

$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}

$output .= '<div class="uncode-wrapper '.$css_class.'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';
$output .= $content;
$output .= '</div>';

echo wpb_js_remove_wpautop($output);