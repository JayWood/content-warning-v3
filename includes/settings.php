<?php

class CWV2_Settings {
	// Stuff

	public function def_group() {

	}

	/**
	 * Just gets a default set of arguments to make sure they're always set.
	 *
	 * @param $args
	 *
	 * @author JayWood
	 * @return array
	 */
	private function get_default_args( $args ) {
		return wp_parse_args( $args, array(
			'id'      => '',
			'name'    => '',
			'desc'    => '',
			'default' => '',
			'options' => false,
		) );

	}

	private function checked_array( $haystack, $cur, $show = true ) {
		if ( is_array( $haystack ) && in_array( $cur, $haystack ) ) {
			$cur = $haystack = 1;
		}
		if ( is_array( $haystack ) ) { //if $haystack is still an array, take first value
			$haystack = array_shift( $haystack );
		}

		return checked( $haystack, $cur, $show );
	}

	public function check( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		$default     = empty( $args['default'] );
		if ( ! $options || empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $field_id, $default );

		?><fieldset><?php
		$offset = 0;
		foreach ( $options as $op_value => $label ) {
			$cur_id = $op_value . '-' . $offset;
			$offset++;

			?>
			<label for="<?php echo $cur_id; ?>">
				<input id="<?php echo $cur_id; ?>" type="checkbox" value="<?php echo $op_value; ?>" name="<?php echo $field_id; ?>[]" <?php $this->checked_array( $option_value, $op_value ); ?>/><?php echo $label; ?>
			</label>
			<?php

		}
		?></fieldset><?php
	}

	public function number( $args = array() ) {

	}

	public function text( $args = array() ) {

	}

	public function radio( $args = array() ) {

	}

	public function media( $args = array() ) {

	}

	public function color( $args = array() ) {

	}

	public function textbox( $args = array() ) {

	}

	public function editor( $args = array() ) {

	}
}
