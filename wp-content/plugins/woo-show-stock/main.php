<?php
/**
 * Plugin Name: Woocommerce Show Stock
 * Description: Show the “stock quantity” for each product in the shop
 * Author: Team Bright Vessel
 * Version: 0.2.4
 * Author URI: http://brightvessel.com/
 * WC requires at least: 3.4
 * WC tested up to: 3.4.5
*/


function brightvessel_woocommerce_show_stock() {
    global $product;

    $low_stock_notify = 3;
    if(get_option('woocommerce_notify_low_stock_amount')){
        $low_stock_notify = get_option('woocommerce_notify_low_stock_amount');
    }
    
    if ( !$product->is_type( 'variable' ) ){
        if( $product->get_stock_quantity() ) { // if manage stock is enabled
            if ( number_format($product->get_stock_quantity(),0,'','') < $low_stock_notify ) { // if stock is low
                return '<div class="remaining">'.printf(_('Only % left in stock!'),number_format($product->get_stock_quantity(),0,'','')).'</div>';
            } else {
                return '<div class="remaining">'.printf(_('%s left in stock'),number_format($product->get_stock_quantity(),0,'','')).'</div>';
            }
        }
    } else {
        if( $product->get_stock_quantity() ) { // if manage stock is enabled
            $product_variations = $product->get_available_variations();
            $stock = 0;
            foreach ($product_variations as $variation)  {
              $stock = $stock+$variation['max_qty'];
            }
            if($stock>0){
                if ( number_format($stock,0,'','') < $low_stock_notify ) { // if stock is low
                    return '<div class="remaining">'.printf(_('Only % left in stock!'),number_format($stock,0,'','')).'</div>';
                } else {
                    return '<div class="remaining">'.printf(_('%s left in stock'),number_format($stock,0,'','')).'</div>';
                }
            }
        }
    }
}
if(get_option('wc_always_show_stock') == 'yes' && get_option('wc_show_stock_where') !== null)
    add_action(get_option('wc_show_stock_where'),'brightvessel_woocommerce_show_stock', 10);

/**
 * Add settings to the specific section we created before
 */

add_filter( 'woocommerce_get_settings_products', 'brightvessel_woocommerce_show_stock_all_settings', 10, 2 );
function brightvessel_woocommerce_show_stock_all_settings( $settings, $current_section ) {

    /**
     * Check the current section is what we want
     **/
    if ( $current_section == 'inventory' ) {


        $settings[] = array( 'name' => __( 'Stock Settings'), 'type' => 'title', 'desc' => __( 'The following options are used to configure how to show your stock' ), 'id' => 'stockoptions' );



        $settings[] = array(
            'name' => __( 'Always show stock'),
            'type' => 'checkbox',
            'desc' => __( 'Always show available stock' ),
            'id'   => 'wc_always_show_stock'
        );

        $settings[] = array(
            'name' => __( 'Stock position'),
            'type'    => 'select',

            'options' => array(

                'woocommerce_after_shop_loop_item'        => __( 'After shop loop (recommended)', 'woocommerce' ),

                'woocommerce_after_shop_loop_item_title'       => __( 'After title', 'woocommerce' ),

                'woocommerce_before_shop_loop_item 	'  => __( 'Before shop look', 'woocommerce' ),

                'woocommerce_before_shop_loop_item_title' => __( 'Before title', 'woocommerce' )

            ),
            'desc' => __( 'Where the actual stock should be displayed' ),
            'id'   => 'wc_show_stock_where'
        );

        $settings[] = array(
            'type' => 'sectionend',
            'id' => 'wc_settings_tab_stock'
        );

        // $settings[] = $buffer;



        /**
         * If not, return the standard settings
         **/

    }
    return $settings;

}

function brightvessel_woocommerce_show_stock_create_support_notice() {
    $class = 'notice notice-warning';
    $message ='If you need dedicated/professional assistance with this plugin or just want an expert to get your site built and or to run the faster, you may hire us at';

    printf( '<div class="%1$s"><p><strong>[Woocommerce Show Stock]</strong> %2$s <a href="https://www.brightvessel.com/" target="_blank">Bright Vessel</a>. <small><a href="?bvsclose=true">[x]</a></small></p></div>', esc_attr( $class ), esc_html( $message ) );
}




function brightvessel_woocommerce_show_stock_check_notice(){
    if(isset($_GET['bvsclose']) && $_GET['bvsclose'] == 'true'){
        add_option('bvsclose',1);
    }

    if(intval(get_option('bvsclose')) !== 1){
        add_action( 'admin_notices', 'brightvessel_woocommerce_show_stock_check_notice' );
    }
}
add_action('admin_init','brightvessel_woocommerce_show_stock_check_notice');