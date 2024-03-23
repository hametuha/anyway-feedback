<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
/** @var stdClass $post_type */
?>
<div class="wrap afb">

	<h2><i class="dashicons dashicons-chart-area"></i> <?php printf( $this->i18n->_( 'Feedback Statistic of %s' ), $post_type->labels->name ); ?></h2>

	<div id="chart-area" class="clearfix" data-endpoint="<?php echo wp_nonce_url( admin_url( 'admin-ajax.php?action=afb_chart&post_type=' . $post_type->name ), 'afb_chart' ); ?>">
		<div class="div_2 loading" id="afb-pie-chart">
			<img class="loader" src="<?php echo $this->assets_url( 'img/ajax-loader.gif' ); ?>" width="33" height="33" alt="loading" />
		</div>
		<div class="div_2 loading" id="afb-bar-chart">
			<img class="loader" src="<?php echo $this->assets_url( 'img/ajax-loader.gif' ); ?>" width="33" height="33" alt="loading" />
		</div>
	</div>

	<?php $table->prepare_items(); ?>

	<form action="<?php echo admin_url( 'edit.php' ); ?>" method="get">
		<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type->name ); ?>">
		<input type="hidden" name="page" value="anyway-feedback-static-<?php echo esc_attr( $post_type->name ); ?>">
		<?php $table->search_box( $this->i18n->_( 'Search' ), 's' ); ?>
	</form>

	<?php $table->display(); ?>

	<!-- // contents -->

	<?php $this->load_template( 'footer.php' ); ?>

</div>
