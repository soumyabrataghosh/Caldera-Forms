<?php

/**
 * Object representation of Caldera Forms transient data
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Transient_Object extends Caldera_Forms_Object implements  ArrayAccess {

	/** @var  int */
	protected $id;

	/** @var string */
	protected $process_id;

	/** @var  int */
	protected $form_instance;

	/** @var  string */
	protected $datestamp;

	/** @var  int */
	protected $expire;

	/** @var  string */
	protected $data;

    /** @var  array */
    protected $meta;

    /**
     * Caldera_Forms_Transient_Object constructor.
     *
     * @since 1.4.4
     *
     * @param stdClass $obj Optional StdClass object with matching properites
     */
    public function __construct(stdClass $obj){
        parent::__construct($obj);
        if( empty( $this->meta ) ){
            $this->meta = array();
        }
        foreach( $this->main_meta_types() as $type ){
            $this->meta[ $type ] = null;
        }
        $this->meta[ 'other' ] = array();

    }

    /**
	 * Set data property
	 *
	 * @since 1.4.4
	 *
	 * @param array|null $value
	 */
	public function data_set( array $value = null ){
		$this->data = $value;
	}

	/**
	 * Set datestamp with current time fallback
	 *
	 * @since 1.4.4.
	 *
	 * @param null $value
	 */
	public function datastamp_set( $value = null ){
		if( null == $value ){
			$value = current_time( 'mysql' );
		}

		$this->datestamp = $value;
	}

    /**
     * Set meta value
     *
     * @since 1.4.4
     *
     * @param string $type Meta type
     * @param mixed $value Meta value
     *
     * @return bool
     */
	public function meta_set( $type, $value ){
        if( in_array( $type, $this->main_meta_types() ) ){
            $this->meta[ $type ] = $value;

        }else{
            $this->meta[ 'other' ][ $type ] = $value;
        }

        return true;
    }

    /**
     * Get a meta value
     *
     * @since 1.4.4
     *
     * @param string $type Meta type
     * @return bool|null
     */
    public function meta_get( $type ){
        if( in_array( $type, $this->meta_types() ) ){
            if( ! isset( $this->meta[ 'type' ] ) ){
                return null;
            }else{
                return $this->meta[ 'type' ];
            }
        }

        return false;

    }

    /**
     * @since 1.4.4
     * @inheritdoc
     */
	public function to_array($serialize_arrays = true ){
        $data = parent::to_array( $serialize_arrays );
        $data[ 'transient' ] = $data[ 'process_id' ];
        return $data;
    }

    /**
     * @since 1.4.4
     * @inheritdoc
     */
    public function offsetSet($offset, $value){
        if (property_exists($this, $offset) && 'meta' != $offset) {
            return $this->__set($offset, $value);
        } elseif (in_array($offset, $this->main_meta_types())) {
            return $this->meta_set($offset, $value);
        } elseif (isset($this->meta['other'][$offset])) {
            return $this->meta['other'][$offset];

        } else {
            return false;
        }

    }

    /**
     * @since 1.4.4
     * @inheritdoc
     */
    public function offsetExists($offset) {
        if ( isset( $this->$offset ) || in_array( $offset, $this->main_meta_types() ) || isset( $this->meta[ 'other' ][ $offset ] ) ){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @since 1.4.4
     * @inheritdoc
     */
    public function offsetUnset($offset) {
        if( in_array( $offset, $this->main_meta_types() ) ){
            unset( $this->meta[ $offset ] );
            return true;
        }elseif ( isset( $this->meta[ 'other' ][ $offset ] ) ){
            unset( $this->meta[ 'other' ][ $offset ] );
            return true;
        } elseif( isset( $this->$offset ) ){
            unset( $this->$offset );
            return true;
        }else{
            return false;
        }
    }

    /**
     * @since 1.4.4
     * @inheritdoc
     */
    public function offsetGet($offset) {
        if( property_exists( $this, $offset ) && 'meta' != $offset ){
            return $this->__get( $offset );
        }elseif ( in_array( $offset, $this->main_meta_types() )  ){
            return $this->meta_get( $offset );
        }elseif( isset( $this->meta[ 'other' ][ $offset ] ) ){
            return $this->meta[ 'other' ][ $offset ];
        } else{
            return false;
        }
    }

    /**
     * Main meta types
     *
     * @since 1.4.4
     *
     * @return array
     */
    protected function main_meta_types(){
        return array( 'error', 'note', 'fields' );
    }

}