<?php
/*
Plugin Name: DZX Customer Review
Plugin URI: https://www.darkxee.com
Description: Customer Review for seo
Version: 1.0
Author: Darkxee
Author URI:https://www.darkxee.com
License: Plugin comes under GPL Licence.
*/

function load_my_script(){
    wp_register_script('my_script', plugins_url( '/js/script.js' , __FILE__ ) , array( 'jquery' ));
    wp_register_script('masonry-js', plugins_url( '/js/masonry.pkgd.min.js' , __FILE__ ) , array( 'jquery' ),'1.0',true);
    wp_enqueue_script( 'my_script' );
    wp_enqueue_script( 'masonry-js' );

    wp_enqueue_style( 'dzx-css', plugins_url('/css/dzx.css', __FILE__) );
    
}
add_action('wp_enqueue_scripts', 'load_my_script');


//wp_localize_script( 'custom-js', 'darkxee', array( 'callurl' => admin_url( 'admin-ajax.php')));



//start html skeleton
function customer_review($atts){  
    global $wpdb;
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    
    // echo 'page: '. $paged ."<br/>";


    $atts = shortcode_atts(
		array(
            'start' => '',
            'end' => '',
            'sku' => '',
            'orderby' => 'star',
            'sort' => 'DESC',            
            'limit' => '10',
            'ispagenavi'=>'',
            
        ), $atts ,'customer_review' 
    ); 
 
    $limitStart = ($atts['limit'] * ($paged-1));

    $where = ''; 
    
    if ($atts['start']!= '' || $atts['sku']!= '' ){
        $where = 'WHERE ( ';
        
        if ($atts['start']!= ''){
        $where .= " review_date BETWEEN '".$atts['start']."' AND '".$atts['end']."'";
        }        
        if ($atts['sku']!= '' && $atts['start']== '' ){
            $where .= " product_sku LIKE '".$atts['sku']."' ";
        }
        if ($atts['sku']!= '' && $atts['start']!= '' ){
            $where .= " And product_sku LIKE '".$atts['sku']."' ";
        }

        $where.=' ) ';
    }
    $where.= "ORDER BY ".$atts['orderby']." ".$atts['sort'];
    
    if ($atts['limit']!= ''){
          $where .= ' LIMIT '. (int)$limitStart.','.  (int)$atts['limit'];
    }

    //  echo $where;
    

    $datas = $wpdb->get_results("SELECT * FROM wp_customer_review $where");

    $html = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
    
    $html .= '<ul class="customer-rev">';
    $pagination = '<ul class="customer-pagei">';
    $item=0;
    foreach ( $datas as $data ) 
    {
        $item++; 
        $html .='<li itemprop="review" itemscope itemtype="http://schema.org/Review" id="revId-'.$data->id.'" class="rev-item">';
        // $html.= '<span>id: '.$data->id.'</span>';
        $html .='<div class="rev-contener">';
        $html .='<div class="rev-img">';
        $html .='<div class="hover-shadow"><i class="fa fa-search-plus" aria-hidden="true"></i></div>';
        $html .='<img itemprop="image" src="/wp-content/uploads/customer_review/'.$data->img.'"/ alt="'.$data->img_alt.'">';
        $html .='</div>';
        $html .='<h3 class="rev-title" itemprop="name">'.$data->title.'</h3>';

        if ( $data->customer_name != ''){
            $html .='<h4><span>by </span>';
            $html .='<span itemprop="author">'.$data->customer_name.'</span></h4>';
        }
        
        $html .='<div class="rev-sku">sku: '.$data->product_sku.'</div>';
        $html .='<meta itemprop="datePublished" content="'.$data->review_date.'">'.$data->review_date;
        $html .='    <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">';
        $html .='            <div class="rev-star">';
        $html .='            <meta itemprop="worstRating" content="1">';
        for ($i = 0; $i < 5; $i++) {
                if ($i < $data->star){ 
                    $html.= '<span class="fa fa-star getstar"></span>';
                }else{
                    $html.= '<span class="fa fa-star"></span>';
                }         
            }
        $html .='           <span itemprop="ratingValue">'.$data->star.'</span>/';
        $html .='           <span itemprop="bestRating">5</span>stars';
        $html .='        </div>';
        $html .='    </div>';
        $html .='   <span class="rev-detail" itemprop="description">'.$data->detail.'</span>';
        $html .='</div>';
        $html .='</li>';

    }  
    if ($paged > 1){
        $pagination .='<li class="previous"> <a href="'.wp_get_canonical_url().'page/'.((int)$paged-1).'/"><i class="fa fa-caret-left" aria-hidden="true"></i> Previous</a> </li>';
    }
    if ($item > 0) {
        $pagination .='<li class="next"> <a href="'.wp_get_canonical_url().'page/'.((int)$paged+1).'/">Next <i class="fa fa-caret-right" aria-hidden="true"></i></a></li>';
    }    
    
    $pagination .= '</ul>';
    $html.='</ul>';
    
    
    echo  $html;

    if ($atts['ispagenavi']){
        echo $pagination;
    }
   

     
  }
add_shortcode('dzx_customer_review', 'customer_review');

add_action('woocommerce_after_single_product','get_product_review_sku');
function get_product_review_sku(){
    global $product;
    echo "<h2>Customer Review</h2>";
    echo do_shortcode( '[dzx_customer_review sku="'.$product->get_sku().'" limit="4"]' );
}



register_activation_hook( __FILE__, 'dzx_plugin_create_db' );
function dzx_plugin_create_db() {

	global $wpdb;	

	$sql = "CREATE TABLE wp_customer_review (
            id int(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            customer_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            img_alt varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            link varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            detail longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            star int(10) NULL DEFAULT 0,
            product_sku varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            category varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            review_date date NULL DEFAULT NULL,
            slocation varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
            priority int(11) NULL DEFAULT 0,
            PRIMARY KEY  (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
