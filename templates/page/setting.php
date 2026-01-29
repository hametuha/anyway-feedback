<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
?>

<form method="post" action="<?php echo admin_url( 'options.php' ); ?>" id="afb-form">
	<?php
	add_action( 'afb_after_setting_field', function ( $key ) {
		$messages = [
			'post_types'              => __( 'Checked post type will be active for feedbacks and display controller inside post content via <code>the_content</code> hook.', 'anyway-feedback' ),
			'comment'                 => __( 'This option decide to display feedback controller in comment loop. This affects all comment of commentable post types.', 'anyway-feedback' ),
			'style'                   => __( 'If you select &quot;No style&quot;, you need a custom style sheet for the controller UI. Mark up can be specified at <strong>Custom markup</strong> section', 'anyway-feedback' ),
			'hide_default_controller' => __( 'Checked post types are still active for feedbacks, but you need custom code to display the controller. See &quot;Advanced Usage&quot; section.', 'anyway-feedback' ),
			'controller'              => implode( '<br />', [
				__( 'You can customize markup of Feedback controller. If you don\'t want, leave it blank.', 'anyway-feedback' ),
				__( 'In case of customization, You can use variables(<code>%POSITIVE%</code>, <code>%TOTAL%</code>, <code>%NEGATIVE%</code>, <code>%POSITIVE_RATE%</code>, <code>%NEGATIVE_RATE%</code>, <code>%POST_TYPE%</code>) and 2 clickable elements must have class name <code>good</code> and <code>bad</code>. Both <code>&lt;a&gt;</code> and <code>&lt;button&gt;</code> tags are supported. For <code>&lt;a&gt;</code> tags, use <code>%LINK%</code> as href attribute.', 'anyway-feedback' ),
				__( 'Default markup is below:', 'anyway-feedback' ),
			] ),
			'ga'                      => sprintf(
				// translators: %s is a url.
				__( 'This feature send report as event tracking to Google Analytics. You can get chronological report there. For detail, see <a href="%s">Advanced Usage</a>. ', 'anyway-feedback' ),
				// phpcs:ignore PHPCompatibility.FunctionDeclarations.NewClosure.ThisFoundOutsideClass
				$this->setting_url( 'advanced' )
			),
		];

		if ( ! empty( $messages[ $key ] ) ) {
			printf(
				'<p class="description">%s</p>',
				wp_kses_post( $messages[ $key ] )
			);
		}
		if ( 'controller' === $key ) {
			echo '<pre class="afb-code-exam">';
			// translators: %s is post type.
			$message = esc_html( sprintf( __( 'Is this %s useful?', 'anyway-feedback' ), '%POST_TYPE%' ) );
			$useful  = esc_html( __( 'Useful', 'anyway-feedback' ) );
			$useless = esc_html( __( 'Useless', 'anyway-feedback' ) );
			// translators: %1$s number of positive feedbacks, %2$s is total number, %3$s is post type.
			$status = esc_html( sprintf( __( '%1$s of %2$s people say this %3$s is useful.', 'anyway-feedback' ), '%POSITIVE%', '%TOTAL%', '%POST_TYPE%' ) );
			$markup = \AFB\Main::get_instance()->default_controller_html( $message, '%LINK%', $useful, $useless, $status );
			echo esc_html( $markup );
			echo '</pre>';
		}
	} );

	settings_fields( 'anyway-feedback' );
	do_settings_sections( 'anyway-feedback' );
	submit_button();
	?>
</form>
