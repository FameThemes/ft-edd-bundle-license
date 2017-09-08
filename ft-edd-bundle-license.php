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

    add_action('admin_enqueue_scripts', 'ft_edd_bundle_license_scripts');
}

add_filter( 'edd_template_paths', 'ft_edd_custom_tpl' );
function ft_edd_custom_tpl( $file_paths ){
    $file_paths[ 20 ] = dirname( __FILE__ ).'/templates';
    return $file_paths;
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
