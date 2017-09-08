<?php
/*
Plugin Name: FT EDD Bundle License
Plugin URL: #
Description: Improve EDD Bundle Licensing.
Version: 1.0.0
Author: shrimp2t
Author URI:
*/

$payment_data = array(
    'price'         => '6969',
    'date'          => '2017-09-08 10:10:10',
    'user_email'    => 'tessssssst-email@mail.com',
    'purchase_key'  => $purchase_data['purchase_key'],
    'currency'      => edd_get_currency(),
    'downloads'     => $purchase_data['downloads'],
    'cart_details'  => $purchase_data['cart_details'],
    'user_info'     => $purchase_data['user_info'],
    'status'        => 'pending'
);

// record the pending payment
$payment = edd_insert_payment( $payment_data );



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



/*
 *
 * //// Tess
$payment_id = 87388;
$args =  array(

);


Key: b5dc1ffb80c3b235b54d01141806001a - 87390
Lấy dc Payement ID: _edd_sl_payment_id  - 87388
Lâyd dc download ID: _edd_sl_download_id -  155



 */


/*
$keys = array(
    '646cc09e8d0203974464cb76b5053286',
    'fed0bc55057bc3a16f1d02137c3b739d',
    'c4e044524003080d707f8903ba751bd5',
    '646cc09e8d0203974464cb76b5053286',
    '6833e21fd16d01fd88ce29cbf4183e5d',
);
// _edd_sl_key

foreach (  $keys as $k ){
    global $wpdb;
    $value = $wpdb->get_var( $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s" , '_edd_sl_key', $k) );

    if ( $value ) {
        wp_delete_post( $value, true);
    }
}

//die();
*/


/*
add_action( 'init', 'edd_sl_test_api' );

function edd_sl_test_api(){
   // $d = new EDD_Download( 8537 );
   // var_dump( $d->get_prices() );
    if ( isset( $_GET['sl_create'] ) ) {
        $license  = new EDD_SL_License();
        $keys = $license->create( 188, 87458, 0, 0, array() );
       // var_dump( $keys );
    }

}

function ft_edd_complete_download_purchase_license( $download_id = 0, $payment_id = 0, $type = 'default', $cart_item = array(), $cart_index = 0 ){
    $keys = array();

    // Bail if this cart item is for a renewal
    if( ! empty( $cart_item['item_number']['options']['is_renewal'] ) ) {
        return $keys;
    }

    // Bail if this cart item is for an upgrade
    if( ! empty( $cart_item['item_number']['options']['is_upgrade'] ) ) {
        return $keys;
    }

    $purchased_download = new EDD_SL_Download( $download_id );
    if ( ! $purchased_download->is_bundled_download() && ! $purchased_download->licensing_enabled() ) {
        var_dump( $keys );
        return $keys;
    }

    $license  = new EDD_SL_License();

    if ( ! empty( $license->ID ) ) {
        $keys[] = $license->ID;

        $child_licenses = $license->get_child_licenses();
        if ( ! empty( $child_licenses ) ) {
            $child_ids = wp_list_pluck( $child_licenses, 'ID' );
            $keys = array_merge( $keys, $child_ids );
        }
    }

    var_dump( $keys );

    return $keys;
}

// add_action( 'edd_complete_download_purchase', 'ft_edd_complete_download_purchase_license', 80, 5 );
*/

/// Only need this for backwards compatible hooks.
//$type = $is_bundle ? 'bundle' : 'default';
//do_action( 'edd_sl_store_license', $this->ID, $purchased_download->ID, $payment->ID, $type );
function ft_edd_sl_store_license( $license_id, $download_id, $payment_id, $type ){
    $license = new EDD_SL_License( $license_id );
    $price_id = $license->__get( 'price_id' );
    $default_price_id = get_post_meta( $download_id, '_edd_default_price_id', true );

    if ( $license->__get( 'post_parent' ) ) { // if is child license of bundle download
        //var_dump( $license_id.' Price_Id: '.$price_id . ' Default_ID: '.$default_price_id );
        if ( $default_price_id != $price_id ) { // if price_variable seclected
            $download = new EDD_Download($download_id );
            if ( $download->has_variable_prices() ) {
                $prices = $download->get_prices();
                if ( isset( $prices[ $price_id ] ) ) {
                    if ( $prices[ $price_id ]['license_limit'] ) {
                        $activation_limit =  $prices[ $price_id ]['license_limit'];
                        $license->__set( 'activation_limit',  $activation_limit );
                    }
                }
            }
        }
    }

}

add_action( 'edd_sl_store_license', 'ft_edd_sl_store_license', 80, 4 );
