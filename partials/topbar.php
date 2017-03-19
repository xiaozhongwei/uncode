<?php

class untopbar
{

	public $html = '';

	function __construct() {
		$enable = ot_get_option( '_uncode_topbar_switch');

		if($enable !== 'on') {
			return;
		}
		$bg = ot_get_option('_uncode_topbar_bg_color');
		$color =  ot_get_option('_uncode_topbar_color');
		$fontsize = ot_get_option('_uncode_topbar_font_size');
		$fontfamily = ot_get_option('_uncode_topbar_font_family');
		$fontweight = ot_get_option('_uncode_topbar_font_weight');

		$this->html = '<div id="topbar" class="topbar">'.
		'<div class="row-menu limit-width">'.
		'<div class="row-menu-inner">'.
		wp_nav_menu( array(
			"menu"              => $primary_menu,
			"theme_location"    => "topbar_left",
			"container"         => "div",
			"container_class"   => "topbar-left",
			"menu_class"        => "",
			"fallback_cb"       => "wp_bootstrap_navwalker::fallback",
			"walker"            => new Topbar_Nav_Walker(),
			"echo"            => 0)
		).
		wp_nav_menu( array(
			"menu"              => $primary_menu,
			"theme_location"    => "topbar_right",
			"container"         => "div",
			"container_class"   => "topbar-right",
			"menu_class"        => "",
			"fallback_cb"       => "wp_bootstrap_navwalker::fallback",
			"walker"            => new Topbar_Nav_Walker(),
			"echo"            => 0)
		)
		.'</div>'
		.'</div>'
		.'</div>';
	}
}
?>