<?php
/**
 * Stores transient data between requests
 *
 * Not using transient API anymore so we can have more control over this API, and its clean up. Also, provide possibility to not-remove if needed.
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_DB_Transient extends Caldera_Forms_DB_Base {

	/**
	 * Primary fields
	 *
	 * @since 1.4.4
	 *
	 * @var array
	 */
	protected $primary_fields = array(
		'process_id'    => array(
			'%s',
			'strip_tags'
		),
		'form_instance' => array(
			'%s',
			'strip_tags'
		),
		'datestamp' => array(
			'%s',
			'strip_tags'
		),
		'expire' => array(
			'%d',
			'absint'
		),
		'data' => array(
			'%s',
			'serialize'
		)
	);


    /**
     * Meta fields
     *
     * @since 1.4.4
     *
     * @var array
     */
    protected $meta_fields = array(
        'event_id'   => array(
            '%d',
            'absint',
        ),
        'meta_key'   => array(
            '%s',
            'strip_tags',
        ),
        'meta_value' => array(
            '%s',
            'strip_tags',
        ),
    );

    /**
     * Meta keys
     *
     * @since 1.4.4
     *
     * @var array
     */
    protected $meta_keys = array(
        'error' => array(
            '%s',
            'strip_tags',
        ),
        'note'  => array(
            '%s',
            'strip_tags',
        ),
        'fields' => array(
            '%s',
            'escape_array',
        ),
        'other' => array(
            '%s',
            'escape_array'
        )
    );

	/**
	 * Name of primary index
	 *
	 * @since 1.4.4
	 *
	 * @var string
	 */
	protected $index = 'id';

	/**
	 * Name of table
	 *
	 * @since 1.4.4
	 *
	 * @var string
	 */
	protected $table_name = 'cf_transient';

	/**
	 * Class instance
	 *
	 * @since 1.4.4
	 *
	 * @var Caldera_Forms_DB_Transient
	 */
	private static $instance;

    /**
     * Caldera_Forms_DB_Transient constructor.
     *
     * @since 1.4.4
     */
    protected function __construct(){
        add_action( Caldera_Forms_Transient_Util::get_cron_action(), array( 'Caldera_Forms_Transient_Util', 'event_callback' ) );
    }

    /**
	 * Get instance
	 *
	 * @since 1.4.4
	 *
	 * @return Caldera_Forms_DB_Transient
	 */
	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Add a transient
	 *
	 * @since 1.4.4
	 *
	 * @param Caldera_Forms_Transient_Object $data
	 *
	 * @return bool|int|null
	 */
	public function add_transient( Caldera_Forms_Transient_Object $data ){
		$id =  $this->create( $data->to_array() );
        if( 0 < absint( $id ) ){
            Caldera_Forms_Transient_Util::schedule_delete( $id, $data->expire );
        }
        return $id;

	}

	/**
	 * Get a transient by process ID, the preffred method
	 *
	 * @since 1.4.4
	 *
	 * @param string $process_id Process ID
	 *
	 * @return Caldera_Forms_Transient_Object
	 */
	public function get_transient( $process_id ){
		global $wpdb;
		$table_name = $this->get_table_name( false );
		$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE `process_id` = %s", strip_tags( $process_id ) );
		$r = $wpdb->get_results( $sql );
		if( ! empty( $r ) ){
			$object = new Caldera_Forms_Transient_Object( $r[ key( array_slice( $r, -1, 1, true  ) )] );
			return $object;
		}

	}

	/**
	 * @inheritdoc
	 * @since 1.4.4
	 */
	public function get_meta( $id, $key = false ){
        global $wpdb;
        $table_name = $this->get_table_name( true );
        if( is_array( $id ) ) {
            $sql = "SELECT * FROM $table_name WHERE`$this->index` IN(" . $this->escape_array( $id ) . ")";
        }else{
            $sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE `$this->index` = %d", absint( $id ) );
        }

        $results = $wpdb->get_results( $sql, ARRAY_A );

        if( ! empty( $results ) && is_string( $key ) ){
            return $this->reduce_meta( $results, $key );

        }

        return $results;
	}

	/**
	 * @inheritdoc
	 * @since 1.4.4
	 */
	public function get_record( $id ){
		$found = parent::get_record( $id );
		if( ! empty( $found ) ){
			return new Caldera_Forms_Transient_Object( (object) $found );
		}
	}

	public function recent(){
	    global $wpdb;
        $table_name = $this->get_table_name( false );
        $r = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
        return $r;
    }

}