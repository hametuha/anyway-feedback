<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
?>
<div class="wrap afb">


	<h2 class="nav-tab-wrapper">
		<a class="nav-tab
		<?php
		if ( ! $this->input->get( 'view' ) ) {
			echo ' nav-tab-active';}
		?>
		" href="<?php echo $this->setting_url(); ?>">
			<?php _e( 'Anyway Feedback Option', 'anyway-feedback' ); ?>
		</a>
		<?php
		foreach ( array(
			'advanced' => array( __( 'Advanced Usage', 'anyway-feedback' ) ),
		) as $key => $val ) :
			?>
			<a class="nav-tab
			<?php
			if ( $key === $this->input->get( 'view' ) ) {
				echo ' nav-tab-active';}
			?>
			" href="<?php echo $this->setting_url( $key ); ?>">
				<?php echo esc_html( $val[0] ); ?>
			</a>
		<?php endforeach; ?>
	</h2>

	<p>
		<i class="dashicons dashicons-chart-area"></i>
	<?php if ( empty( $this->option['post_types'] ) ) : ?>
		<?php _e( 'You don\'t specify any post type.', 'anyway-feedback' ); ?>
	<?php else : ?>
		<label>
			<?php _e( 'Select post type and see static', 'anyway-feedback' ); ?>:
			<select id="afb-post-type-switcher" data-href="<?php echo admin_url( 'edit.php' ); ?>">
				<option><?php _e( 'Select Post Type', 'anyway-feedback' ); ?></option>
				<?php
				foreach ( $this->option['post_types'] as $post_type ) :
					$object = get_post_type_object( $post_type );
					?>
				<option value="<?php echo $post_type; ?>"><?php echo esc_html( $object->labels->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	<?php endif; ?>
	</p>

	<hr />


	<?php
	switch ( $this->input->get( 'view' ) ) {
		case 'advanced':
			$this->load_template( 'page/advanced.php' );
			break;
		default:
			$this->load_template( 'page/setting.php' );
			break;
	}

	?>

	<!-- // contents -->

	<?php $this->load_template( 'footer.php' ); ?>

</div>
