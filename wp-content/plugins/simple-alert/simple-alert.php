<?php
/**
 * Plugin Name: Simple Alert
 * Plugin URI: https://example.com/
 * Description: Plugin Description
 * Version: 1.0
 * Author: zafar iqbal
 * Author URI:
 * License: GPLv2 or later
 * Text Domain: smpaltr
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add to menu
 */
function simple_alert_register_options_page() {
	 add_options_page( 'Simple Alert', 'Simple Alert', 'manage_options', 'simple-alert', 'simple_alert_options_page' );
}

add_action( 'admin_menu', 'simple_alert_register_options_page' );

add_action( 'wp_ajax_simple_alert_get_post_data', 'get_alert_data_ajax_handler' );

function get_alert_data_ajax_handler() {
	$post_types                            = $_POST['type'];
	$options                               = get_option( 'simple-alert-posts' );
	$options['posts_types'][ $post_types ] = 1;
	update_option( 'simple-alert-posts', $options );
	rander_posts_checkbox( $post_types );
	// wp_send_json_success($results);
	wp_die();
}

function rander_posts_checkbox( $post_types ) {
	 global $wpdb;

	$query = "
            SELECT $wpdb->posts.ID,$wpdb->posts.post_title,
            PM1.meta_value as simple_alert
            FROM $wpdb->posts
            LEFT JOIN $wpdb->postmeta AS PM1 ON ( $wpdb->posts.ID = PM1.post_id AND PM1.meta_key = '_simple_alert')
            WHERE $wpdb->posts.post_type = '$post_types'
            AND $wpdb->posts.post_status = 'publish'
            GROUP BY $wpdb->posts.ID
            ORDER BY $wpdb->posts.post_date DESC

         ";

	$results = $wpdb->get_results( $query );

	foreach ( $results as $row ) {
		?>
		<div class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input update-posttype" <?php checked( '1', $row->simple_alert, 1 ); ?> id="alert_type_<?php echo esc_attr( $row->ID ); ?>" value="<?php echo esc_attr( $row->ID ); ?>">
			<label class="custom-control-label" for="alert_type_<?php echo esc_attr( $row->ID ); ?>"><?php echo esc_attr( $row->post_title ); ?></label>
		</div>
		<?php
	}
}

add_action( 'wp_ajax_simple_alert_remove_post_data', 'remove_alert_data_ajax_handler' );

function remove_alert_data_ajax_handler() {
	 global $wpdb;
	$post_types                            = $_POST['type'];
	$options                               = get_option( 'simple-alert-posts' );
	$options['posts_types'][ $post_types ] = 0;
	update_option( 'simple-alert-posts', $options );
}

function update_alert_data_ajax_handler() {
	 $pid  = $_POST['post_id'];
	$value = $_POST['value'];
	update_post_meta( $pid, '_simple_alert', $value );
	echo $value;
	wp_die();
}
add_action( 'wp_ajax_simple_alert_update_post_data', 'update_alert_data_ajax_handler' );


function simple_alert_save_ajax_handler() {
	 $alert_message           = $_POST['alert_message'];
	$options                  = get_option( 'simple-alert-posts' );
	$options['alert_message'] = $alert_message;
	update_option( 'simple-alert-posts', $options );
	wp_die();
}
add_action( 'wp_ajax_simple_alert_save', 'simple_alert_save_ajax_handler' );

/**
 * Loads scripts
 */
function simple_alert_scripts() {
	if ( get_current_screen()->id === 'settings_page_simple-alert' ) {
		$css_path        = plugins_url( 'simple-alert/lib/assets/css/bootstrap.min.css' );
		$js_path         = plugins_url( 'simple-alert/lib/assets/js/bootstrap.min.js' );
		$custom_js_path  = plugins_url( 'simple-alert/lib/assets/js/custom.js' );
		$custom_css_path = plugins_url( 'simple-alert/lib/assets/css/custom.css' );
		wp_enqueue_style( 'latest-bootstrap-css', $css_path, array(), '4.5.0' );
		wp_enqueue_style( 'simple-alert-custom-css', $custom_css_path, array(), '4.5.0' );
		wp_enqueue_script( 'latest-bootstrap-js', $js_path, array(), '4.5.0' );
		wp_enqueue_script( 'simple-alert-custom-js', $custom_js_path, array( 'jquery' ), '1.0' );
		wp_localize_script( 'simple-alert-custom-js', 'simple_alert_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}
}
add_action( 'admin_enqueue_scripts', 'simple_alert_scripts' );
/**
 * Option Page
 */
function simple_alert_options_page() {
	?>

	<div class="wrap">

		<h2>Simple Alert</h2>
		<div class="notification-top-bar">
			<div class="msg">Please wait </div>
			<a class="swatch active" data-filter-name="Color" data-filter-value="Green" title="Green" href="#"></a>
			<div class="loading">
				<span><i></i><i></i></span>
			</div>
		</div>

		<div class="container mb-3">
			<div class="row">

				<?php
				$options = get_option( 'simple-alert-posts' );

				$get_cpt_args        = array(
					'public'   => true,
					'_builtin' => true,
				);
				$types_array         = array( 'attachment', 'media' );
				$post_types          = get_post_types( $get_cpt_args, 'object' ); // use 'names' if you want to get only name of the post type.
				$availabe_post_types = array();
				if ( $post_types ) {
					foreach ( $post_types as $cpt_key => $type ) {
						if ( ! in_array( $type->name, $types_array ) ) {
							$availabe_post_types[] = $type->name;
							if ( isset( $type->labels->name ) ) {
								?>
								<div class="col-6 col-sm-6 col-md-3 col-lg-1">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="alert_type_<?php echo esc_attr( $type->labels->name ); ?>" <?php checked( '1', $options['posts_types'][ $type->name ], 1 ); ?> value="<?php echo esc_attr( $type->name ); ?>">
										<label class="custom-control-label" for="alert_type_<?php echo esc_attr( $type->labels->name ); ?>"><?php echo esc_attr( $type->labels->name ); ?></label>
									</div>
								</div>
								<?php
							}
						}
					}
				}
				?>

			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-12">
					<ul id="tab-list" class="nav nav-pills mb-2">
						<?php
						$a = 0;
						if ( count( (array) $options['posts_types'] ) > 0 ) {
							foreach ( $options['posts_types'] as $save_types => $value ) {
								if ( in_array( $save_types, $availabe_post_types ) ) {
									if ( $value ) {
										echo '<li class="nav-item m-0"><a class="nav-link ' . ( ( $a < 1 ) ? 'active' : '' ) . '" id="tabli' . $save_types . '" data-toggle="pill" href="#tab' . $save_types . '" role="tabpanel">' . $save_types . '</a></li>';
										$a++;
									}
								}
							}
						}
						?>
					</ul>
					<div class="tab-content mt-3 border border-secondary p-1 rounded mb-3" id="tab-content">
						<?php
						$i = 0;
						if ( count( (array) $options['posts_types'] ) > 0 ) {
							foreach ( $options['posts_types'] as $save_types => $value ) {
								if ( $value ) {
									echo '<div  class="tab-pane fade ' . ( ( $i > 0 ) ? '' : 'active show' ) . '" role="tabpanel" aria-labelledby="tab' . $save_types . '" id="tab' . $save_types . '">';
									rander_posts_checkbox( $save_types );
									echo '</div>';
									$i++;
								}
							}
						}
						?>

					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<textarea class="form-control form-control-sm mb-3 simple-alert-message" rows="3" placeholder="Small textarea"><?php echo $options['alert_message']; ?></textarea>
					<button class="button button-primary save-changes">Save Changes</button>
				</div>
			</div>
		</div>

	</div>
	<?php
}

function simple_alert_font_assets() {
	$custom_js_path = plugins_url( 'simple-alert/lib/assets/js/simple-alert.js' );

	$custom_css_path = plugins_url( 'simple-alert/lib/assets/css/simple-alert.css' );

	wp_enqueue_style( 'simple-alert-custom-css', $custom_css_path, array(), '4.5.0' );
	wp_enqueue_script( 'simple-alert-custom-js', $custom_js_path, array( 'jquery' ), '1.0' );
}

add_action( 'wp_enqueue_scripts', 'simple_alert_font_assets' );

function simple_alert_script_footer( $alert_message ) {
	?>

	<div class="toast__main">
		<div class="toast__container">
			<div class="toast__cell">



				<div class="toast toast--blue add-margin">
					<div class="toast__icon">
						<svg version="1.1" class="toast__svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;" xml:space="preserve">
							<g>
								<g id="info">
									<g>
										<path d="M10,16c1.105,0,2,0.895,2,2v8c0,1.105-0.895,2-2,2H8v4h16v-4h-1.992c-1.102,0-2-0.895-2-2L20,12H8     v4H10z"></path>
										<circle cx="16" cy="4" r="4"></circle>
									</g>
								</g>
							</g>

						</svg>
					</div>
					<div class="toast__content">
						<p class="toast__type">Info</p>
						<p class="toast__message"><?php echo $alert_message; ?></p>
					</div>
					<div class="toast__close">
						<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15.642 15.642" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 15.642 15.642">
							<path fill-rule="evenodd" d="M8.882,7.821l6.541-6.541c0.293-0.293,0.293-0.768,0-1.061  c-0.293-0.293-0.768-0.293-1.061,0L7.821,6.76L1.28,0.22c-0.293-0.293-0.768-0.293-1.061,0c-0.293,0.293-0.293,0.768,0,1.061  l6.541,6.541L0.22,14.362c-0.293,0.293-0.293,0.768,0,1.061c0.147,0.146,0.338,0.22,0.53,0.22s0.384-0.073,0.53-0.22l6.541-6.541  l6.541,6.541c0.147,0.146,0.338,0.22,0.53,0.22c0.192,0,0.384-0.073,0.53-0.22c0.293-0.293,0.293-0.768,0-1.061L8.882,7.821z"></path>
						</svg>
					</div>
				</div>


			</div>
		</div>
	</div>

	<?php
}

function simple_alert_message() {
	global $post;
	$options = get_option( 'simple-alert-posts' );
	if ( get_post_meta( $post->ID, '_simple_alert', 1 ) == 1 and $options['alert_message'] != '' ) {
		simple_alert_script_footer( $options['alert_message'] );
	}
}
add_action( 'wp_footer', 'simple_alert_message' ); ?>
