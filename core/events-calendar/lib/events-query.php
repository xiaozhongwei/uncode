<?php

class ThemeEventsQuery {
	/**
	 * @var private instance of current class
	 */
	private static $instance;

	/**
	 * Private constuct because of Singletone
	 */
	private function __construct() {
	}

	/**
	 * Private sleep because of Singletone
	 */
	private function __wakeup() {
	}

	/**
	 * Private clone because of Singletone
	 */
	private function __clone() {
	}

	/**
	 * Returns current instance of class
	 * @return ShortcodeLoader
	 */
	public static function getInstance() {
		if(self::$instance == null) {
			return new self;
		}

		return self::$instance;
	}

	public function buildQueryObject($params) {
		$queryArray = array(
			'post_type'      => 'tribe_events',
			'orderby'        => $params['order_by'],
			'order'          => $params['order'],
			'posts_per_page' => $params['number']
		);

		if(!empty($params['category'])) {
			if(false != strpos($params['category'], ',')){
				$queryArray['tax_query'] = array(
					'taxonomy' => 'tribe_events_cat',
					'field'    => 'slug',
					'terms'    => explode(',', $category),
				);
			} else {
				$queryArray['tribe_events_cat'] = $params['category'];
			}
		}

		$projectIds = null;
		if(!empty($params['selected_events'])) {
			$projectIds             = explode(',', $params['selected_events']);
			$queryArray['post__in'] = $projectIds;
		}

		if(!empty($params['next_page'])) {
			$queryArray['paged'] = $params['next_page'];

		} else {
			$queryArray['paged'] = 1;
		}


		return tribe_get_events($queryArray, true);
	}
}