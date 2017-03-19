<?php

$title = $columns = $image_size = $order_by = $order = $number = $category = $selected_events = $next_page = $read_more_text = $read_more_link = $el_id = $css_animation = $animation_delay = $animation_speed = '';


$params = shortcode_atts(array(
  //'title' => 'Events',
  'columns'    => '',
  'image_size' => 'full',
  'order_by'        => 'date',
  'order'           => 'ASC',
  'number'          => '-1',
  'category'        => '',
  'selected_events' => '',
  'next_page'       => '',
  // 'read_more_text' => 'Read more &gt;',
  // 'read_more_link' => '',
  'el_id' => '',
  'css_animation' => '',
  'animation_delay' => '',
  'animation_speed' => '',
) , $atts);

extract($params);

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-events', $this->settings['base'], $atts );

$classes = array();
$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}

$eventsQuery = ThemeEventsQuery::getInstance();
$query = $eventsQuery->buildQueryObject($params);

$itemClass =  array('mkdf-events-list-item', 'block-item');
$parentClass = array('mkdf-events-list');

switch($params['columns']) {
  case 'one':
    $parentClass[] = 'blocks-1';
    break;
  case 'two':
    $parentClass[] = 'blocks-xlg-2';
    $parentClass[] = 'blocks-lg-2';
    $parentClass[] = 'blocks-sm-2';
    $parentClass[] = 'blocks-xs-1';
    break;
  
  case 'four':
    $parentClass[] = 'blocks-xlg-4';
    $parentClass[] = 'blocks-lg-3';
    $parentClass[] = 'blocks-sm-2';
    $parentClass[] = 'blocks-xs-1';
    break;
  case 'three':
  default:
    $parentClass[] = 'blocks-xlg-3';
    $parentClass[] = 'blocks-lg-3';
    $parentClass[] = 'blocks-sm-2';
    $parentClass[] = 'blocks-xs-1';
    break;
}

$item_class = implode(' ', $itemClass);
$parent_class = implode(' ', $parentClass);

if($params['image_size'] !== 'full') {
  $image_size =  'events_'.$params['image_size'];
}

ob_start();
?>
<div class="<?php echo $parent_class;?>">
  <?php if($query->have_posts()) : ?>
    <?php while($query->have_posts()) : $query->the_post(); ?>
      
      <div <?php post_class($item_class); ?>>
        <div class="mkdf-events-list-item-inner">
          <div class="mkdf-events-list-item-holder">
            <?php if(has_post_thumbnail()) : ?>
              <div class="mkdf-events-list-item-image-holder">
                <a href="<?php the_permalink(); ?>">
                  <?php the_post_thumbnail($image_size); ?>

                  <div class="mkdf-events-list-item-date-holder">
                    <div class="mkdf-events-list-item-date-inner">
                      <span class="mkdf-events-list-item-date-day">
                        <?php echo tribe_get_start_date(null, true, 'd'); ?>
                      </span>

                      <span class="mkdf-events-list-item-date-month">
                        <?php echo tribe_get_start_date(null, true, 'M'); ?>
                      </span>
                    </div>
                  </div>
                </a>
              </div>
            <?php endif; ?>
            <div class="mkdf-events-list-item-content single-block-padding">
              <div class="mkdf-events-list-item-title-holder">
                <h5 class="mkdf-events-list-item-title">
                  <a href="<?php the_permalink(); ?>"><?php echo uncode_truncate(get_the_title(), 50, '..'); ?></a>
                </h5>
              </div>
              <div class="mkdf-events-list-item-info">
                <div class="mkdf-events-list-item-date">
                  <span class="mkdf-events-item-info-icon">
                    <span class="fa fa-clock-o"></span>
                  </span>
                  <?php echo tribe_events_event_schedule_details(); ?>
                </div>
                <?php if(tribe_address_exists() && !empty(tribe_get_address())) : ?>
                  <div class="mkdf-events-list-item-location-holder">
                    <span class="mkdf-events-item-info-icon">
                     <span class="fa fa-map-marker"></span>
                    </span>
                    <span class="mkdf-events-list-item-location"><?php echo esc_html(tribe_get_address()); ?></span>
                  </div>
                <?php endif; ?>
                <?php if(tribe_has_organizer()) : ?>
                  <div class="mkdf-events-list-item-organizer-holder">
                    <span class="mkdf-events-item-info-icon">
                      <span class="fa fa-user"></span>
                    </span>
                    <span class="mkdf-events-list-item-organizer">
                      <?php echo esc_html(tribe_get_organizer()); ?>
                    </span>
                  </div>
                <?php endif; ?>
                <?php

                 if(has_term('', 'tribe_events_cat')) : ?>
                  <div class="mkdf-events-list-item-category-holder">
                    <span class="mkdf-events-item-info-icon">
                      <span class="fa fa-bookmark-o"></span>
                    </span>
                    <span class="mkdf-events-list-item-category">
                      <?php echo get_the_term_list(get_the_id(), 'tribe_events_cat'); ?>
                    </span>
                  </div>
                <?php endif; ?>

              </div>
            </div>
          </div>
        </div>
      </div>

    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
  <?php else: ?>
    <p><?php esc_html_e('Sorry, no events matched your criteria.', 'uncode'); ?></p>
  <?php endif; ?>
</div>
<?php

$events_content = ob_get_clean();

$output .= '<div id="' . esc_attr($el_id) .'" class="uncode-wrapper '.esc_attr($css_class).'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';

// $link = vc_build_link($read_more_link); 
// $output .= '<div class="uncode-blog-heading clearfix"><h2 class="uncode-blog-title">'.$title.'</h2><a class="uncode-blog-more" href="'.$link['url'].'" target="'.$link['target'].'" alt="'.$link['title'].'">'.$read_more_text.'</a></div>';

$output .= $events_content;

$output .= '</div>';

echo wpb_js_remove_wpautop($output);
