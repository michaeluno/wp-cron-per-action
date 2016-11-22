<?php
/**
 *	Plugin Name:    WP Cron per Action
 *	Plugin URI:     http://en.michaeluno.jp/wp-cron-per-action
 *	Description:    Ensures to load wp-cron.php per due action to avoid exceeding the PHP's maximum execution time.
 *	Author:         Michael Uno (miunosoft)
 *	Author URI:     http://michaeluno.jp
 *	Version:        0.0.1
 */

class WPCronPerAction_Bootstrap {
    
    public function __construct() {
        
        add_action( 'plugins_loaded', array( $this, 'replyToLoadPlugin' ) );
        
    }
    
    /**
     * @callback    action      plugins_loaded
     */
    public function replyToLoadPlugin() {
        
        $_sPageNow = isset( $GLOBALS[ 'pagenow' ] ) ? $GLOBALS[ 'pagenow' ] : '';
        if ( 'wp-cron.php' !== $_sPageNow ) {
            return;
        }
        if ( ! defined( 'DOING_CRON' ) || ! DOING_CRON ) {
            return;
        }
        
        include( dirname( __FILE__ )  . '/include/class/WPCronPerAction.php' );
        new WPCronPerAction;
        
    }
    
}
new WPCronPerAction_Bootstrap;
