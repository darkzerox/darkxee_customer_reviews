<?php
/*
Plugin Name: Customer Review
Plugin URI: https://www.darkxee.com
Description: Customer Review for seo
Version: 1.0
Author: Darkxee
Author URI:https://www.darkxee.com
License: Plugin comes under GPL Licence.
*/

//wp_enqueue_script('custom-js', plugins_url( '/js/script.js' , __FILE__ ) , array( 'jquery' ));
//wp_localize_script( 'custom-js', 'darkxee', array( 'callurl' => admin_url( 'admin-ajax.php')));

wp_enqueue_style( 'dzx-css', plugins_url('/css/dzx.css', __FILE__) );

//start html skeleton
function customer_review($atts){  
    global $wpdb;

    $atts = shortcode_atts(
		array(
            'start' => '',
            'end' => '',
            'sku' => '',
            'orderby' => 'date',           
        ), $atts ,'customer_review' 
    );
    $where = '';   
    if ($atts['start']!= ''){
        $where = "WHERE (review_date BETWEEN '".$atts['start']."' AND '".$atts['end']."')";
    }

    $datas = $wpdb->get_results("SELECT * FROM wp_customer_review $where");

    $html = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
    
    $html .= '<ul class="customer-rev">';

    foreach ( $datas as $data ) 
    {
       
        $html .= '<li  id="rev'.$data->id.'">';
        $html .= '<div class="rev-contener">';
        $html .= '<div class="rev-img"><a href="'.$data->link.'"><img src="/wp-content/uploads/customer_review/'.$data->img.'"/ alt="'.$data->img_alt.'"></a></div>';
        $html .= '<h3><a href="'.$data->link.'">'.$data->title.'</a></h3>';  
        $html .= '<div class="rev-star">';       
       
        for ($i = 0; $i < 5; $i++) {
            if ($i <= $data->id){
                $html.= '<span class="fa fa-star getstar"></span>';
            }else{
                $html.= '<span class="fa fa-star"></span>';
            }         
        }    

        $html .= '</div>';
        $html .= '<div class="rev-content">'.$data->detail.'</div>';    
        $html .= '</div>'   ;
        $html .='</li>';
    }  
    $html.='</ul>';
    echo  $html;
  }
add_shortcode('dzx_data', 'customer_review');



register_activation_hook( __FILE__, 'dzx_plugin_create_db' );

function dzx_plugin_create_db() {

	global $wpdb;	

	$sql = "CREATE TABLE wp_customer_review (
            id int(20) NOT NULL,
            title varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            img varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            img_alt varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            link varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            detail longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            star int(10) NOT NULL,
            product_sku varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            review_date date NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; ALTER TABLE wp_customer_review
            ADD PRIMARY KEY (id); ALTER TABLE wp_customer_review
            MODIFY id int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
            COMMIT; ";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}
