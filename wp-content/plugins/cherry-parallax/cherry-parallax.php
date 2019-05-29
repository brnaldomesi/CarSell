<?php
/*
  Plugin Name: Cherry Parallax Plugin
  Version: 1.1.0
  Plugin URI: http://www.cherryframework.com/
  Description: Create blocks with parallax effect
  Author: Cherry Team.
  Author URI: http://www.cherryframework.com/
  Text Domain: cherry-parallax
  Domain Path: languages/
  License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
if ( ! defined( 'ABSPATH' ) )
exit;

class cherry_parallax {
  
  public $version = '1.0.0';

  function __construct() {
    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
    add_shortcode( 'cherry_parallax', array( $this, 'parallax_shortcode' ) );
    add_shortcode( 'cherry_video_parallax', array( $this, 'video_parallax_shortcode' ) );
  }

  function assets() {
    wp_enqueue_script( 'mousewheel', $this->url('js/jquery.mousewheel.min.js'), array('jquery'), '3.0.6', true );
    wp_enqueue_script( 'smoothscroll', $this->url('js/jquery.simplr.smoothscroll.min.js'), array('jquery'), '1.0', true );
    wp_enqueue_script( 'device-check', $this->url('js/device.min.js'), array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'cherry-apiloader', $this->url('js/cherry.apiloader.js'), array('jquery'), '1.0', true );
    wp_enqueue_script( 'cherry-parallax', $this->url('js/cherry.parallax.js'), array('jquery'), $this->version, true );
    wp_enqueue_style( 'cherry-parallax', $this->url('css/parallax.css'), '', $this->version );
  }

  /**
   * return plugin url
   */
  function url( $path = null ) {
    $base_url = untrailingslashit( plugin_dir_url( __FILE__ ) );
    if ( !$path ) {
      return $base_url;
    } else {
      return esc_url( $base_url . '/' . $path );
    }
  }

  /**
   * return plugin dir
   */
  function dir( $path = null ) {
    $base_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );
    if ( !$path ) {
      return $base_dir;
    } else {
      return esc_url( $base_dir . '/' . $path );
    }
  }



  /**
   * Shortcode
   */
  function parallax_shortcode( $atts, $content = null ) {
    extract(shortcode_atts( array(
        'image' => '',
        'speed' => '1.5',
        'invert' => 'false',
        'fullwidth' => 'true',
        'custom_class' => ''
      ),
      $atts,
      'cherry_parallax'
    ));
    if ( !$image ) {
      return;
    }

    $args = array(
        'post_type' => 'attachment',
        'post_mime_type' =>'image',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
    );

    $query_images = new WP_Query( $args );

    if ( $query_images->have_posts() ) {
      foreach ( $query_images->posts as $item) { 
        $filename = wp_basename($item->guid);
        if($image == $filename) $image_url = $item->guid;
      }
    }

    wp_reset_postdata(); 

    $result = '<section class="parallax-box image-parallax-box ' . esc_attr( $custom_class ) . '" >';
    $result .= '<div class="parallax-content">' . do_shortcode( $content ) . '<div class="clear"></div></div>';
    $result .= '<div class="parallax-bg" data-parallax-type="image" data-img-url="'. $image_url .'" data-speed="' . $speed . '" data-invert="' . $invert . '" data-fullwidth="' . $fullwidth . '"></div>';
    $result .= '</section>';

    $result = apply_filters( 'cherry_plugin_shortcode_output', $result, $atts, 'cherry_parallax' );

    return $result;
  }



   /**
   * Video Shortcode
   */
  function video_parallax_shortcode( $atts, $content = null ) {
    extract(shortcode_atts( array(
        'poster' => '',
        'webm' => '',
        'ogv' => '',
        'mp4' => '',
        'youtube_id' => '',
        'vimeo_id' => '',
        'mute' => 'false',
        'speed' => 'none',
        'invert' => 'false',
        'fullwidth' => 'true',
        'custom_class' => ''
      ),
      $atts,
      'cherry_parallax'
    ));

    $parallax_type = '';

    if ( $webm != '' || $ogv != '' || $mp4 != '') {
      $parallax_type = 'video';
    } else if ( $youtube_id != '') {
      $parallax_type = 'youtube';
    } else if ( $vimeo_id != '' ){
      $parallax_type = 'vimeo';
    } else {
      return;
    }

    $sourcesList = array(
      "mp4"    => $mp4,
      "webm"   => $webm,
      "ogv"    => $ogv,
      "poster" => $poster
    );

    $sourcesUrlList = array(
      "mp4"     => '',
      "webm"   => '',
      "ogv"    => '',
      "poster" => ''
    );

    $args = array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
    );

    $query_videos = new WP_Query( $args );

    if ( $query_videos->have_posts() ) {
      foreach ( $query_videos->posts as $item) { 
        $filename = wp_basename($item->guid);
        foreach ($sourcesList as $key => $value) {
          if($value == $filename){
            $sourcesUrlList[$key] = $item->guid;
          }
        }
      }
    }

    wp_reset_postdata();

    $result = '<section class="parallax-box video-parallax-box ' . esc_attr( $custom_class ) . '" >';
    $result .= '<div class="parallax-content">' . do_shortcode( $content ) . '<div class="clear"></div></div>';
    $result .= '<div class="parallax-bg" data-parallax-type="'. $parallax_type .'" data-img-url="'. $sourcesUrlList['poster'] .'" data-speed="' . $speed . '" data-invert="' . $invert . '" data-mute="' . $mute . '" data-fullwidth="' . $fullwidth . '">';
      switch ($parallax_type) {
        case "video":
            $result .= '<video class="parallax_media parallax-bg-inner load" poster="' .$sourcesUrlList['poster']. '" loop>';
              $result .= '<source src="' .$sourcesUrlList['mp4']. '" type="video/mp4">';
              $result .= '<source src="' .$sourcesUrlList['webm']. '" type="video/webm">';  
              $result .= '<source src="' .$sourcesUrlList['ogv']. '" type="video/ogg">';  
            $result .= '</video>';
            break;
        case "youtube":
            $result .= '<div class="parallax_youtube parallax-bg-inner load" data-youtube-id="'. $youtube_id .'"></div>';
            break;
        case "vimeo":
            $result .= '<div class="parallax_vimeo parallax-bg-inner load" data-vimeo-id="'. $vimeo_id .'"></div>';
            break;
      }
    $result .= '</div>';
    $result .= '</section>';

    return $result;
  }

}

new cherry_parallax();
?>