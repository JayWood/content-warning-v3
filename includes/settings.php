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
		if ( is_array( $haystack ) ) {
			if ( ! empty( $cur ) && in_array( $cur, $haystack ) ) {
				$cur = $haystack = 1;
			} else {
				$cur = 0;
				$haystack = 1;
			}
		}

		return checked( $haystack, $cur, $show );
	}

	public function check( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		$default     = empty( $args['default'] ) ? array() : $args['default'];
		if ( ! $options || empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $field_id, $default );

		?><fieldset><?php
		foreach ( $options as $op_value => $label ) {
			?>
			<label for="<?php echo $op_value; ?>">
				<input id="<?php echo $op_value; ?>" type="checkbox" value="<?php echo $op_value; ?>" name="<?php echo $field_id; ?>[]" <?php $this->checked_array( $option_value, $op_value ); ?>/><?php echo $label; ?>
			</label>
			<?php
		}
		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
		?></fieldset><?php
	}

	public function number( $args = array() ) {

		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? array() : $args['default'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $field_id, $default );

		?><input type="number" name="<?php echo $field_id; ?>" value="<?php echo intval( $option_value ); ?>" id="<?php echo $field_id; ?>" /><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}

	}

	public function text( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? array() : $args['default'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $field_id, $default );

		?><input type="text" name="<?php echo $field_id; ?>" value="<?php echo esc_attr( $option_value ); ?>" id="<?php echo $field_id; ?>" /><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}

	public function radio( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		$default     = $args['default'];
		if ( ! $options || empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $field_id, $default );

		?><fieldset><?php
		foreach ( $options as $op_value => $label ) {

			?>
			<label for="<?php echo $op_value; ?>">
				<input id="<?php echo $op_value; ?>" type="radio" value="<?php echo $op_value; ?>" name="<?php echo $field_id; ?>" <?php checked( $option_value, $op_value ); ?>/><?php echo $label; ?>
			</label><br />
			<?php

		}
		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
		?></fieldset><?php
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
