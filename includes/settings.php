<?php

class CWV2_Settings {

	public function def_group() {}

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

	/**
	 * @param array $haystack
	 * @param mixed $cur
	 * @param bool  $show
	 *
	 * @author JayWood
	 * @return string
	 */
	private function selected_array( $haystack, $cur, $show = true ) {
		if ( is_array( $haystack ) ) {
			if ( ! empty( $cur ) && in_array( $cur, $haystack ) ) {
				$cur = $haystack = 1;
			} else {
				$cur      = 0;
				$haystack = 1;
			}
		}

		return selected( $haystack, $cur, $show );
	}

	/**
	 * @param array $haystack
	 * @param mixed $cur
	 * @param bool  $show
	 *
	 * @author JayWood
	 * @return string
	 */
	private function checked_array( $haystack, $cur, $show = true ) {
		if ( is_array( $haystack ) ) {
			if ( ! empty( $cur ) && in_array( $cur, $haystack ) ) {
				$cur = $haystack = 1;
			} else {
				$cur      = 0;
				$haystack = 1;
			}
		}

		return checked( $haystack, $cur, $show );
	}

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function check( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		$default     = empty( $args['default'] ) ? array() : $args['default'];
		if ( ! $options || empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

		?><fieldset><?php
		foreach ( $options as $op_value => $label ) {
			?>
			<label for="<?php echo esc_attr( $field_id ); ?>">
				<input id="<?php echo esc_attr( $field_id ); ?>" type="checkbox" value="<?php echo esc_attr( $op_value ); ?>" name="<?php echo esc_attr( $field_id ); ?>[]" <?php $this->checked_array( $option_value, $op_value ); ?>/><?php echo $label; ?>
			</label>
			<?php
		}
		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
		?></fieldset><?php
	}


	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function number( $args = array() ) {

		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? 0 : $args['default'];
		$options     = empty( $args['options'] ) ? array() : $args['options'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

		$attributes = '';

		if ( ! empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				$attributes .= $k . '="' . $v . '"';
			}
		}

		?><input type="number" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo $option_value; ?>" id="<?php echo esc_attr( $field_id ); ?>" <?php echo $attributes; ?>/><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}

	}

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function text( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? '' : $args['default'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

		?><input type="text" name="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $option_value ); ?>" id="<?php echo esc_attr( $field_id ); ?>" class="regular-text" /><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function radio( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		$default     = $args['default'];
		if ( ! $options || empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

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

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function media( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$options     = $args['options'];
		$default     = $args['default'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

		if ( ! empty( $option_value ) ) {
			$option_value = esc_url( $option_value );
		}

		$label = isset( $options['label'] ) ? $options['label'] : __( 'Upload', 'content-warning-v2' );
		$uploader_title = isset( $options['uploader-title'] ) ? sprintf( 'data-uploader-title="%s"', $options['uploader-title'] ) : '';
		$uploader_button = isset( $options['uploader-btn'] ) ? sprintf( 'data-uploader-btn-txt="%s"', $options['uploader-btn'] ) : '';

		?><fieldset>
			<input type="text" name="<?php echo $field_id; ?>" id="<?php echo $field_id; ?>" value="<?php echo $option_value; ?>" class="regular-text" />
			<input type="button" class="button button-secondary upload_image_button" value="<?php echo $label; ?>" data-target-id="<?php echo $field_id; ?>" <?php echo $uploader_button; ?> <?php echo $uploader_title; ?> />
		</fieldset><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function color( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? '' : $args['default'];
		$options     = empty( $args['options'] ) ? array() : $args['options'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );
		$attributes = '';

		if ( ! empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				$attributes .= $k . '="' . $v . '"';
			}
		}

		?><input type="text" name="<?php echo $field_id; ?>" value="<?php echo esc_attr( $option_value ); ?>" id="<?php echo $field_id; ?>" class="regular-text color_select" <?php echo $attributes; ?>/><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function textbox( $args = array() ) {

		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? '' : $args['default'];
		$options     = empty( $args['options'] ) ? array() : $args['options'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

		$attributes = '';

		if ( ! empty( $options ) ) {
			foreach ( $options as $k => $v ) {
				$attributes .= $k . '="' . $v . '"';
			}
		}

		?><textarea name="<?php echo esc_attr( $field_id ); ?>" id="<?php echo esc_attr( $field_id ); ?>" class="regular-text" <?php echo $attributes; ?>><?php echo esc_attr( $option_value ); ?></textarea><?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}

	public function editor( $args = array() ) {

		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? '' : $args['default'];
		$options     = empty( $args['options'] ) ? array() : $args['options'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );

		wp_editor( $option_value, $field_id, $options );

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}

	/**
	 * @param array $args
	 *
	 * @author JayWood
	 */
	public function select2_multi( $args = array() ) {
		$args = $this->get_default_args( $args );

		$field_id    = $args['id'];
		$description = $args['desc'];
		$default     = empty( $args['default'] ) ? '' : $args['default'];
		$options     = empty( $args['options'] ) ? array() : $args['options'];
		if ( empty( $field_id ) ) {
			return;
		}

		$option_value = get_option( $field_id, $default );
		?>
		<select name="<?php echo esc_attr( $field_id ); ?>[]" id="<?php echo esc_attr( $field_id ); ?>" class="cwv2_select2 widefat" multiple="multiple">
			<?php foreach ( $options as $k => $v ) : ?>
				<option value="<?php echo $k; ?>" <?php $this->selected_array( $option_value, $k ); ?>><?php echo $v; ?></option>
			<?php endforeach; ?>
		</select>
		<?php

		if ( ! empty( $description ) ) {
			?><p class="description"><?php echo $description; ?></p><?php
		}
	}
}
