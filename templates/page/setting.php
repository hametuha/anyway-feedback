<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
?>
<form method="post" action="<?php echo $this->setting_url(); ?>" id="afb-form">
	<?php wp_nonce_field( 'afb_option', '_afb_nonce' ); ?>
	<table class="form-table">
		<tbody>
		<tr>
			<th><?php _e( 'Styling', 'anyway-feedback' ); ?></th>
			<td>
				<p>
					<label><input type="radio" name="afb_style" value="0" <?php checked( $this->option['style'], 0 ); ?>/><?php _e( 'No style', 'anyway-feedback' ); ?></label>
					<label><input type="radio" name="afb_style" value="1" <?php checked( $this->option['style'], 1 ); ?>/><?php _e( 'Auto load', 'anyway-feedback' ); ?></label>
				</p>
				<p class="description">
					<?php printf( __( 'If you select &quot;No style&quot;, you need stylize skin. Skin s mark up can be specified at <strong>%s</strong> section', 'anyway-feedback' ), __( 'Custom markup', 'anyway-feedback' ) ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Post type setting', 'anyway-feedback' ); ?></th>
			<td>
				<p>
					<?php
					foreach ( get_post_types() as $post_type ) :
						$object = get_post_type_object( $post_type ); if ( ! in_array( $post_type, array( 'revision', 'nav_menu_item' ), true ) ) :
							?>
						<label><input type="checkbox" name="afb_post_types[]" value="<?php echo $post_type; ?>" <?php checked( in_array( $post_type, $this->option['post_types'], true ) ); ?>/><?php echo esc_html( $object->labels->name ); ?></label>
											<?php
					endif;
endforeach;
					?>
				</p>
				<p class="description">
					<?php _e( 'Checked post type will have feedback controller inside post content', 'anyway-feedback' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Hide default feedback controller', 'anyway-feedback' ); ?></th>
			<td>
				<p>
					<?php
					foreach ( get_post_types() as $post_type ) :
						$object = get_post_type_object( $post_type ); if ( ! in_array( $post_type, array( 'revision', 'nav_menu_item' ), true ) ) :
							?>
						<label><input type="checkbox" name="afb_hide_default_controller[]" value="<?php echo $post_type; ?>" <?php checked( in_array( $post_type, $this->option['hide_default_controller'], true ) ); ?>/><?php echo esc_html( $object->labels->name ); ?></label>
											<?php
					endif;
endforeach;
					?>
				</p>
				<p class="description">
					<?php _e( 'Checked post type if you need to hide default feedback controller.', 'anyway-feedback' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Comment setting', 'anyway-feedback' ); ?></th>
			<td>
				<p>
					<label><input type="radio" name="afb_comment" value="0" <?php checked( $this->option['comment'], 0 ); ?>/><?php _e( 'Not show', 'anyway-feedback' ); ?></label><br />
					<label><input type="radio" name="afb_comment" value="1" <?php checked( $this->option['comment'], 1 ); ?>/><?php _e( 'Show in comment loop', 'anyway-feedback' ); ?></label>
				</p>
				<p class="description">
					<?php _e( 'This option decide to display feedback controller in comment loop.', 'anyway-feedback' ); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="afb_text"><?php _e( 'Custom markup', 'anyway-feedback' ); ?></label></th>
			<td>
				<textarea id="afb_text" name="afb_text" rows="8"><?php echo esc_textarea( stripcslashes( $this->option['controller'] ) ); ?></textarea>
				<p class="description">
					<?php _e( "You can customize markup of Feedback controller.<br />If you don't want, leave it blank.<br />In case of customization, You can use variables(%POSITIVE%, %TOTAL%, %NEGATIVE%, %POST_TYPE%, 'anyway-feedback' ) and <strong>2 link tags(&lt;a&gt;) must have class name &quot;good&quot; and &quot;bad&quot; and %LINK% as href attribute</strong>.<br />Default markup is below." ); ?>
				</p>
<pre class="afb-code-exam">
<?php
$message = esc_html( sprintf( __( 'Is this %s useful?', 'anyway-feedback' ), '%POST_TYPE%' ) );
$useful  = esc_html( __( 'Useful', 'anyway-feedback' ) );
$useless = esc_html( __( 'Useless', 'anyway-feedback' ) );
$status  = esc_html( sprintf( __( '%1$s of %2$s people say this %3$s is useful.', 'anyway-feedback' ), '%POSITIVE%', '%TOTAL%', '%POST_TYPE%' ) );
$markup  = <<<HTML
<span class="message">{$message}</span>
<a class="good" href="%LINK%">{$useful}</a>
<a class="bad" href="%LINK%">{$useless}</a>
<span class="status">{$status}</span>
HTML;
echo esc_html( $markup )
?>
</pre>
			</td>
		</tr>
		<tr>
			<th><?php _e( 'Google Analytics Tracking', 'anyway-feedback' ); ?></th>
			<td>
				<label><input type="radio" name="afb_ga" value="1" <?php checked( $this->option['ga'], 1 ); ?>/><?php _e( 'Track', 'anyway-feedback' ); ?></label>
				<label><input type="radio" name="afb_ga" value="0" <?php checked( $this->option['ga'], 0 ); ?>/><?php _e( 'Do not track', 'anyway-feedback' ); ?></label>
				<p class="description">
					<span class="label">Since 1.0</span>
					<?php printf( __( 'This feature send report as event tracking to Google Analytics. You can get chronological report there. For detail, see <a href="%s">advanced usage</a>. ', 'anyway-feedback' ), $this->setting_url( 'advanced' ) ); ?>
				</p>
			</td>
		</tr>
		</tbody>
	</table>
	<?php submit_button( __( 'Update', 'anyway-feedback' ) ); ?>
</form>
