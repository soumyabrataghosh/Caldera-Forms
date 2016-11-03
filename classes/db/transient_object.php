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
class Caldera_Forms_DB_Transient_Object extends Caldera_Forms_Object {

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

}