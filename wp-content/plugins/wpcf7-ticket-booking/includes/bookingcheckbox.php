<?php

/**
 * * A base module for [ticketbooking] and [ticketbooking*]
 **/


add_action( 'wpcf7_init', 'tbcf_add_form_tag_ticket_book_cf7' );
/**
 * Ticket booking init
 */
function tbcf_add_form_tag_ticket_book_cf7() {
	wpcf7_add_form_tag(
		array( 'ticket_book_cf7', 'ticket_book_cf7*' ),
		'cf7tb_ticket_form_tag_handler',
		array( 'name-attr' => true )
	);
}
/**
 * Ticket booking tag handler
 */
function cf7tb_ticket_form_tag_handler( $tag ) {
	global $wpdb;
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );

	if ( in_array( $tag->basetype, array( 'email', 'url', 'tel' ) ) ) {
		$class .= ' wpcf7-validates-as-' . $tag->basetype;
	}

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class']    = $tag->get_class_option( $class );
	$atts['id']       = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset( $tag->values );

	$value = $tag->get_default_option( $value );

	$value = wpcf7_get_hangover( $tag->name, $value );

	$atts['type'] = 'checkbox';

	$rows = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'cf7booking' );
	foreach ( $rows as $row ) {
		$atts['name']  = $tag->name . "[$row->id]";
		$atts['value'] = $row->status;
		if ( $row->status == 1 ) {
			$atts['disabled'] = 'disabled';
		} else {
			unset( $atts['disabled'] );
		}
		$html .= sprintf(
			'<br>
<label>
			<span class="wpcf7-form-control-wrap %1$s"><input %2$s />%3$s</span> %4$s</label>',
			sanitize_html_class( $tag->name ),
			wpcf7_format_atts( $atts ),
			$validation_error,
			'Ticket No.' . $row->id
		);
	}
	return $html;
}



add_filter( 'wpcf7_validate_ticket_book_cf7', 'tbcf_ticket_book_cf7_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_ticket_book_cf7*', 'tbcf_ticket_book_cf7_validation_filter', 10, 2 );

/**
 * Ticket booking Validation filter
 */
function tbcf_ticket_book_cf7_validation_filter( $result, $tag ) {
	$type = $tag->type;
	$name = $tag->name;

	$value = isset( $_POST[ $name ] ) ? (string) wp_unslash( $_POST[ $name ] ) : '';

	if ( $tag->is_required() && '' == $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}



add_action( 'wpcf7_admin_init', 'tbcf_add_tag_generator_ticket_book_cf7', 20 );

/**
 * Ticket booking Tag generator
 */
function tbcf_add_tag_generator_ticket_book_cf7() {
	 $tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add(
		'ticket_book_cf7',
		__( 'ticket_book_cf7', 'nb-cpf' ),
		'tbcf_tag_generator_ticket_book_cf7'
	);
}

/**
 * Ticket booking Tag generator hander
 */
function tbcf_tag_generator_ticket_book_cf7( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'ticket_book_cf7';

	$description = __( 'Generate a form-tag for a country dorp list with flags icon text input field.', 'nb-cpf' );

	$desc_link = '';
	?>
	<div class="control-box">
		<fieldset>
			<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html( __( 'Field type', 'nb-cpf' ) ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'nb-cpf' ) ); ?></legend>
								<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'nb-cpf' ) ); ?></label>
							</fieldset>
						</td>
					</tr>

					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'nb-cpf' ) ); ?></label></th>
						<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
					</tr>


					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'nb-cpf' ) ); ?></label></th>
						<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
					</tr>

					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'nb-cpf' ) ); ?></label></th>
						<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
					</tr>

				</tbody>
			</table>
		</fieldset>
	</div>

	<div class="insert-box">
		<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'nb-cpf' ) ); ?>" />
		</div>

		<br class="clear" />

		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( 'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.', 'nb-cpf' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="checkbox" class="mail-tag zafarhere code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
	</div>
	<?php
}

/**
 * Ticket booking update Ticket status and message on email
 */
function action_wpcf7_posted_data( $array ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'cf7booking';
	foreach ( $array as $key => $value ) {

		if ( str_starts_with( $key, 'ticket_book_cf7' ) ) {

			foreach ( $array[ $key ] as $subkey => $subvalue ) {

				$id   = array( 'id' => $subkey );
				$subs = array(
					'status'       => 1,
					'booking_time' => current_time( 'mysql' ),
				);
				$wpdb->update( $table_name, $subs, $id );
				$array[ $key ][ $subkey ] = 'Ticket No.' . $subkey . "\r\n";
			}
		}
	}

	return $array;
}
add_filter( 'wpcf7_posted_data', 'action_wpcf7_posted_data', 10, 1 );
