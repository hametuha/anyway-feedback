<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
?>
<div id="tabs-4">

	<h3><?php _e( 'Template Tags', 'anyway-feedback' ); ?></h3>

	<p>
		<?php _e( 'If you are experienced developper, you may need customazation. Ofcourse, you can edit your theme and get your own appearance.', 'anyway-feedback' ); ?>
	</p>
	<dl class="code-format">
		<dt><span class="blue">afb_display</span>()</dt>
		<dd>
			<p>
				<?php _e( 'Display controller of post feedback. Use inside loop.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_comment_display</span>( (int) <span class="yellow">$comment_id</span> )</dt>
		<dd>
			<p>
				<?php _e( 'Display controller of comment feedback. You must pass comment_ID as 1st argument.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_affirmative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php _e( "Display number of affirmative feedbacks of specified post. In loop, you don't have to specify \$object_id and \$post_type. \$echo set true, value won't displayed and just return.", 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_negative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php _e( 'Display number of negative feedbacks of specified post. Same as afb_affirmative.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_total</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php _e( 'Display number of total feedbacks of specified post. Same as afb_affirmative.', 'anyway-feedback' ); ?>
			</p>
		</dd>
	</dl>
	<p class="description">
		<?php _e( "All these template tags above should be wrapped inside if declaration for compatibility. If not, stopping this plugin will break your theme's display.", 'anyway-feedback' ); ?>
	</p>
<pre class="afb-code-exam">
<?php
$tag = <<<HTML
<?php if( function_exists("afb_display") ){ afb_display(); } ?>
HTML;
echo esc_html( $tag );
?>
</pre>
	<p class="description">
		<?php printf( __( 'See function detail at <code>%s</code>', 'anyway-feedback' ), dirname( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'functions.php' ); ?>
	</p>

	<hr />

	<h3><?php _e( 'Google Analytics', 'anyway-feedback' ); ?></h3>

	<p><?php printf( __( 'You can save all data as <a href="%s" target="_blank">event tracking</a> and analize report chronologically. Data format is like below:', 'anyway-feedback' ), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/events' ); ?></p>

	<table class="afb-speck">
		<thead>
		<tr>
			<th scope="col"><?php _e( 'Name', 'anyway-feedback' ); ?></th>
			<th scope="col"><?php _e( 'Value', 'anyway-feedback' ); ?></th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<th>Category</th>
				<td><code>anyway-feedback/post</code> or <code>anyway-feedback/comment</code></td>
			</tr>
			<tr>
				<th>Action</th>
				<td><code>positive</code> or <code>negative</code></td>
			</tr>
			<tr>
				<th>Label</th>
				<td><?php _e( '<code>Post ID</code> or <code>comment ID</code>', 'anyway-feedback' ); ?></td>
			</tr>
			<tr>
				<th>Value</th>
				<td><?php _e( 'Always 1', 'anyway-feedback' ); ?></td>
			</tr>
		</tbody>
	</table>

	<p class="description">
		<?php printf( __( 'This feature is premised on <a href="%s">Universal Analytics</a>. If you use other services or ga.js, grab the event and record it by yourself.', 'anyway-feedback' ), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/' ); ?>
	</p>

<pre class="afb-code-exam">
<?php
$script = <<<JS
// For example, record old ga.js event.
// This is your original jQuery script.
(function($){
	/**
	 * Listen event of Anyway Feedback
	 *
	 * @param {Event} event
	 * @param {String} post_type or comment
	 * @param {Number} object_id
	 * @param {Boolean} positive
	 */
	$(document).on('feedback.afb', '.afb_container', function(event, type, object_id, positive){
		// Do what you want.
		_gaq.push(['_trackEvent', 'anyway-feedback/' + type, positive, object_id]);
	});
})(jQuery);
JS;
echo esc_html( $script );
?>
</pre>

</div>
