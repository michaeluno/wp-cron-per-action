<?php

/**
 * Handles the main routine of the plugin.
 * 
 * @since       0.0.1
 */
final class WPCronPerAction {
    
    private $___iHookPriority = 1;
    
    /**
     * Indicates whether multiple due items exist.
     */
    private $___bMultiple = false;
    
    /**
     * Sets up hooks
     */
    public function __construct() {

        add_filter( 'pre_transient_' . 'doing_cron', array( $this, 'replyToRemoveOptionCronFilter' ), 10, 2 );
    
        // The filter will be called during the execution of `_get_cron_array()`.
        add_filter( 'option_' . 'cron', array( $this, 'replyToCheckWPCronExecution' ), $this->___iHookPriority, 2 );
        
    }

    /**
     * Removes the `option_cron` filter as the scheduled items should not be modified once the WP Cron routine has started.
     * @callback        filter      pre_transient_doing_cron
     */
    public function replyToRemoveOptionCronFilter( $mValue, $sTransientName ) {
        remove_filter( 'option_' . 'cron', array( $this, 'replyToCheckWPCronExecution' ), $this->___iHookPriority );
        return $mValue;
    }
    
    /**
     * If there is a scheduled item due now, this method returns only that due item.
     * Otherwise, the scheduled items as they are.
     * @since       0.0.1
     * @callback    action          option_cron
     * @return      false|array
     */
    public function replyToCheckWPCronExecution( $abScheduledItems, $sOptionName ) {
                
        if ( ! is_array( $abScheduledItems ) ) {
            return $abScheduledItems;
        }
        // At this point, items have been scheduled.

        // Return only the first item if it is due now.
        if ( ! $this->___hasDueItem( $abScheduledItems ) ) {
            return $abScheduledItems;
        }
            
        $this->___bMultiple = false;    // gets updated in the method below.
        $_aFirstScheduled   = $this->___getTheFirstScheduledItem( $abScheduledItems );

        $this->___scheduleNextPageLoad( $this->___bMultiple, $abScheduledItems );
        return $_aFirstScheduled + $this->___getUndueItems( $abScheduledItems, $_aFirstScheduled );
                
    }
        /**
         * Schedules the next page load.
         * @return      void
         */
        private function ___scheduleNextPageLoad( $bHasMultiple, array $aScheduledItems ) {
            
            if ( ! $bHasMultiple ) {                           
                if ( ! $this->___hasMultipleDueItems( $aScheduledItems ) ) {
                    return;
                }
            }
            add_action( 'shutdown', array( __CLASS__, 'replyToReloadWPCron' ) );
            
        }
            /**
             * Checks if more than one items are due now
             * @return      boolean
             */
            private function ___hasMultipleDueItems( array $aScheduledItems ) {
                unset( $aScheduledItems[ 'version' ] );
                $_iCount = 0;
                foreach( $aScheduledItems as $_iTime => $_aScheduledItemsPerTime ) {
                    
                    if ( $_iTime > microtime( true ) ) {
                        break;
                    }
                    
                    if ( 1 < count( $_aScheduledItemsPerTime ) ) {
                        return true;
                    }
                    $_iCount++;
                    
                }
                return 1 < count( $_iCount );
            }
            
        /**
         * Checks if there is a due item.
         * @since       0.0.1
         * @return      boolean
         */
        private function ___hasDueItem( array $aScheduledItems ) {
            
            $_aKeys      = array_keys( $aScheduledItems );   
            $_iMostClose = isset( $_aKeys[ 0 ] ) ? $_aKeys[ 0 ] : 0;
            $_fNow       = microtime( true );
            if ( $_iMostClose > $_fNow ) {
                return false;
            }
            return true;   
        }
        /**
         * @since       0.0.1
         * @return      array
         */
        private function ___getTheFirstScheduledItem( array $abScheduledItems ) {
            foreach( $abScheduledItems as $_iTime => $_aScheduledItemsPerTime ) {
                if ( ! is_numeric( $_iTime ) ) {
                    continue;
                }
                return array( 
                    $_iTime => $this->___getTheFirstItemsPerAction( $_aScheduledItemsPerTime ),
                );
            }
            return array();
        }
            /**
             * 
             * The passed array may have more than one actions per timestamp.
             * @return      array
             */
            private function ___getTheFirstItemsPerAction( array $aScheduledItemsPerTime ) {
                if ( 1 < count( $aScheduledItemsPerTime ) ) {
                    $this->___bMultiple = true;
                }                
                foreach( $aScheduledItemsPerTime as $_sActionName => $_aItem ) {
                    return array(
                        $_sActionName => $this->___getTheFirstAction( $_aItem ),
                    );
                }
                return array();
            }
                /**
                 * @return      array
                 */
                private function ___getTheFirstAction( array $aItems ) {
                    if ( 1 < count( $aItems ) ) {
                        $this->___bMultiple = true;
                    }
                    foreach( $aItems as $_sHash => $_aArguments ) {
                        return array(
                            $_sHash => $_aArguments,
                        );
                    }
                    return $aItems;
                }
        
        /**
         * Returns items that are not due now.
         * @return      array
         * @remark      `array_diff()` cannot be used for multi-dimensional array.
         */
        private function ___getUndueItems( array $aScheduledItems, array $aFirstScheduled ) {
            foreach( $aFirstScheduled as $_iTime => $_aItem ) {
                unset( $aScheduledItems[ $_iTime ] );
            }
            return $aScheduledItems;
        }
    
    /**
     * @since       0.0.1
     * @callback    action      shutdown
     */
    static public function replyToReloadWPCron() {
        
        if ( self::$___bReloaded ) {
            return;
        }
        self::$___bReloaded = true;

        wp_remote_get(
            site_url( 'wp-cron.php' ),
            array(
                'timeout'     => 0.01, 
                'sslverify'   => false, 
            )
        );
        
    }
        static private $___bReloaded = false;
    
}

