<?php

/**
 * Utility functions for transient
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Transient_Util{

    /**
     * The name of the CRON event for deleting transients
     *
     * @since 1.4.4
     *
     * @var string
     */
    protected static $cron_action = 'caldera_forms_clear_transient';

    /**
     * Caldera_Forms_Transient_Object factory
     *
     * @sicnce 1.4.4
     *
     * @param string $process_id
     * @param string $form_instance
     * @param array $data
     * @return Caldera_Forms_Transient_Object
     */
    public static function factory( $process_id, $form_instance, $data ){
        $obj = new Caldera_Forms_Transient_Object;
        $obj->process_id = $process_id;
        $obj->form_instance = $form_instance;
        $obj->data_set( $data );
        return $obj;
    }

    /**
     * Get the action name for the delete event
     *
     * @since 1.4.4.
     *
     * @return string
     */
    public static function get_cron_action(){
        return self::$cron_action;
    }

    /**
     * Schedule deleting of a transient
     *
     * @since 1.4.4
     *
     * @param int $id Transient ID
     * @param int $expires Seconds after now to schedule delete for
     */
    public static function schedule_delete( $id, $expires = 360 ){
        wp_schedule_single_event( time() + $expires, self::$cron_action, array( $id ) );
    }

    /**
     * CRON callback for delete action
     *
     * @since 1.4.4
     *
     * @param array $args
     */
    public static function event_callback( $args ){
        if( isset( $args[0] ) && is_numeric( $args[0] ) ){
            Caldera_Forms_DB_Transient::get_instance()->delete( $args[0] );
        }
    }

}