<?php
/*
Plugin Name: FT EDD Bundle License
Plugin URL: #
Description: Improve EDD Bundle Licensing.
Version: 1.0.0
Author: shrimp2t
Author URI:
*/


if ( is_admin() ) {

    function ft_edd_bundle_license_scripts ($hook)
    {
        if ($hook != 'download_page_edd-licenses') {
            return;
        }
        wp_enqueue_script('ft_edd_bungle_license', plugins_url('js/js.js', __FILE__), array('jquery'));

    }

    function ft_edd_bundle_license_admin_head() {
        echo '<style type="text/css">.column-limit p, .edd-sl-adjust-limit { display: none !important; } .edd-can-change { border: 1px solid transparent;  cursor: pointer; min-width: 50px; display: inline-block; } .edd-can-change:hover { border-color: #CCCCCC; background: #ededed; }</style>';
    }
    add_action( 'admin_head', 'ft_edd_bundle_license_admin_head' );
    add_action('admin_enqueue_scripts', 'ft_edd_bundle_license_scripts');
}

add_filter( 'edd_template_paths', 'ft_edd_custom_tpl' );
function ft_edd_custom_tpl( $file_paths ){
    $file_paths[ 20 ] = dirname( __FILE__ ).'/templates';
    return $file_paths;
}

add_action( 'wp_ajax_ft_edd_bundle_set_activation_limit', 'ft_edd_bundle_set_activation_limit' );
function ft_edd_bundle_set_activation_limit(){

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error();
    }

    if ( ! isset( $_GET['license_id'] ) ) {
        wp_send_json_error();
    }
    $license_id = absint( $_GET['license_id'] );
    $license = new EDD_SL_License( $license_id );
    if ( ! $license ) {
        wp_send_json_error();
    }
    if ( ! isset( $_GET['number'] ) ) {
        wp_send_json_error();
    }
    $number = isset( $_GET['number'] ) ? intval( $_GET['number'] ) : '';

    do_action( 'edd_sl_pre_set_activation_limit', $license->ID, $number );
    $license->update_meta( 'activation_limit', $number );
    do_action( 'edd_sl_post_set_activation_limit', $license->ID, $number );

    wp_send_json_success();
}


/// Only need this for backwards compatible hooks.
//$type = $is_bundle ? 'bundle' : 'default';
//do_action( 'edd_sl_store_license', $this->ID, $purchased_download->ID, $payment->ID, $type );
function ft_edd_sl_store_license( $license_id, $download_id, $payment_id, $type ){
    $license = new EDD_SL_License( $license_id );
    $price_id = $license->__get( 'price_id' );
    //$default_price_id = get_post_meta( $download_id, '_edd_default_price_id', true );
    if ( $license->__get( 'post_parent' ) ) { // if is child license of bundle download
        if ( '' !== $price_id && strlen( $price_id ) > 0 ) { // if price_variable seclected
            //var_dump( $license_id.' Price_Id: '.$price_id . ' Default_ID: '.$default_price_id );
            $download = new EDD_Download($download_id );
            if ( $download->has_variable_prices() ) {
                $prices = $download->get_prices();
                if ( isset( $prices[ $price_id ] ) ) {
                    if ( $prices[ $price_id ]['license_limit'] ) {
                        $activation_limit =  $prices[ $price_id ]['license_limit'];
                    } else {
                        $activation_limit = 0;
                    }
                    $license->__set( 'activation_limit',  $activation_limit );
                }
            }
        }

    }

}

add_action( 'edd_sl_store_license', 'ft_edd_sl_store_license', 80, 4 );
