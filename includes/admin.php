<?php

class CWV2_Admin {

	/**
	 * @var ContentWarning_v2
	 */
	public $plugin;

	/**
	 * CWV2_Admin constructor.
	 *
	 * @param ContentWarning_v2 $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;

		$this->hooks();
	}

	public function hooks() {
		// Do hooks etc...
	}

}
