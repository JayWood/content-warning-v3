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

	public function check( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		if ( ! $options || empty( $field_id ) ) {
			return;
		}

		?><fieldset><?php
		$offset = 0;
		foreach ( $options as $id => $label ) {
			$cur_id = $id . '-' . $offset;
			$offset++;

			?>
			<label for="<?php echo $cur_id; ?>">
				<input id="<?php echo $cur_id; ?>" type="checkbox" value="<?php echo $id; ?>" name="<?php echo $field_id; ?>[]" /><?php echo $label; ?>
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
