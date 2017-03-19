<?php

$title = $columns = $image_size = $order_by = $order = $number = $category = $selected_videos = $next_page = $read_more_text = $read_more_link = $el_id = $css_animation = $animation_delay = $animation_speed = '';


$params = shortcode_atts(array(
  'columns'    => '',
  'image_size' => 'full',
  'order_by'        => 'date',
  'order'           => 'ASC',
  'number'          => '-1',
  'category'        => '',
  'selected_videos' => '',
  'el_id' => '',
  'css_animation' => '',
  'animation_delay' => '',
  'animation_speed' => '',
) , $atts);

extract($params);

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'uncode-videos', $this->settings['base'], $atts );

$classes = array();
$div_data = array();
if ($css_animation !== '') {
	$css_class .= ' ' . $css_animation . ' animate_when_almost_visible';
	if ($animation_delay !== '') $div_data['data-delay'] = $animation_delay;
	if ($animation_speed !== '') $div_data['data-speed'] = $animation_speed;
}

$queryArray = array(
  'post_type'      => 'video',
  'orderby'        => $order_by,
  'order'          => $order,
  'posts_per_page' => $number
);

if(!empty($category)) {
  $queryArray['tax_query'] = array(
    'taxonomy' => 'video_category',
    'field'    => 'slug',
    'terms'    => explode(',', $category),
  );
}

$projectIds = null;
if(!empty($params['selected_videos'])) {
  $projectIds             = explode(',', $selected_videos);
  $queryArray['post__in'] = $projectIds;
}

$queryArray['paged'] = 1;

$query = new WP_Query($queryArray);

$itemClass =  array('videos-list-item', 'block-item');
$parentClass = array('videos-list');

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

ob_start();
?>
<div class="<?php echo $parent_class;?>">
  <?php if($query->have_posts()) : ?>
    <?php while($query->have_posts()) : $query->the_post();

$video_url = get_post_meta(get_the_id(), '_youku_url', true);
$matches = array();
$youku_iframe = false;
$youku_id = false;
if(preg_match("/(http:|https:|)\/\/v.youku.com\/v_show\/id_([A-Za-z0-9._%-=]*).html/", $video_url, $matches)){
  $youku_iframe = 'http://player.youku.com/embed/'.$matches[2];
  $youku_id = $matches[2];
}
if(preg_match("/(http:|https:|)\/\/static.video.qq.com\/TPout\.swf\?vid=([A-Za-z0-9._%-=]*)/", $video_url, $matches)){
  $youku_iframe = 'http://v.qq.com/iframe/player.html?vid='.$matches[2];
}

?>
      
      <div <?php post_class($item_class); ?>>
        <div class="video-list-item-inner">
        <?php if($youku_id):?>
           <a class="video-list-item-holder" data-lbox="ilightbox" href="<?php echo site_url();?>/youku?id=<?php echo $youku_id;?>">
        <?php elseif($youku_iframe):?>
          <a class="video-list-item-holder" data-lbox="ilightbox" href="<?php echo $youku_iframe;?>">
        <?php else:?>
          <div class="video-list-item-holder">
        <?php endif;?>
            <?php if(has_post_thumbnail()) : ?>
              <div class="video-list-item-image-holder">
                  <?php the_post_thumbnail(); ?>
              </div>
            <?php endif; ?>
            <div class="video-list-item-content single-block-padding">
              <div class="video-list-item-title-holder">
                <h5 class="video-list-item-title">
                  <?php echo uncode_truncate(get_the_title(), 40, '..'); ?>
                </h5>
              </div>
              <div class="video-list-item-info">
                <span class="video-list-item-info-icon">
                  <span class="fa fa-clock-o"></span>
                  <?php echo get_the_date(); ?>
                </span>
              </div>
            </div>
        <?php if($youku_id || $youku_iframe):?>
          </a>
        <?php else:?>
          </div>
        <?php endif;?>
        </div>
      </div>

    <?php endwhile; ?>
    <?php wp_reset_postdata(); ?>
  <?php else: ?>
    <p><?php esc_html_e('Sorry, no videos matched your criteria.', 'uncode'); ?></p>
  <?php endif; ?>
</div>
<?php

$videos_content = ob_get_clean();

$output .= '<div id="' . esc_attr($el_id) .'" class="uncode-wrapper '.esc_attr($css_class).'" '.implode(' ', array_map(function ($v, $k) { return $k . '="' . $v . '"'; }, $div_data, array_keys($div_data))).'>';

// $link = vc_build_link($read_more_link); 
// $output .= '<div class="uncode-blog-heading clearfix"><h2 class="uncode-blog-title">'.$title.'</h2><a class="uncode-blog-more" href="'.$link['url'].'" target="'.$link['target'].'" alt="'.$link['title'].'">'.$read_more_text.'</a></div>';

$output .= $videos_content;

$output .= '</div>';

echo wpb_js_remove_wpautop($output);
