<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
?>
<div id="tabs-4">

	<h3><?php esc_html_e( 'Template Tags', 'anyway-feedback' ); ?></h3>

	<p>
		<?php esc_html_e( 'If you are experienced developer, you may need customisation. Of course, you can edit your theme and get your own appearance.', 'anyway-feedback' ); ?>
	</p>
	<dl class="code-format">
		<dt><span class="blue">afb_display</span>()</dt>
		<dd>
			<p>
				<?php esc_html_e( 'Display controller of post feedback. Use inside loop.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_comment_display</span>( (int) <span class="yellow">$comment_id</span> )</dt>
		<dd>
			<p>
				<?php esc_html_e( 'Display controller of comment feedback. You must pass comment_ID as 1st argument.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_affirmative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php esc_html_e( "Display number of affirmative feedbacks of specified post. In loop, you don't have to specify \$object_id and \$post_type. \$echo set true, value won't displayed and just return.", 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_negative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php esc_html_e( 'Display number of negative feedbacks of specified post. Same as afb_affirmative.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_total</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php esc_html_e( 'Display number of total feedbacks of specified post. Same as afb_affirmative.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_positive_rate</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php esc_html_e( 'Display positive feedback rate as integer percentage (0-100). Same as afb_affirmative.', 'anyway-feedback' ); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_negative_rate</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php esc_html_e( 'Display negative feedback rate as integer percentage (0-100). Same as afb_affirmative.', 'anyway-feedback' ); ?>
			</p>
		</dd>
	</dl>
	<p class="description">
		<?php esc_html_e( "All these template tags above should be wrapped inside if declaration for compatibility. If not, stopping this plugin will break your theme's display.", 'anyway-feedback' ); ?>
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
		<?php
		printf(
			// translators: %s is path to functions.php.
			__( 'See function detail at <code>%s</code>', 'anyway-feedback' ),
			dirname( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'functions.php'
		);
		?>
	</p>

	<hr />

	<h3><?php esc_html_e( 'Google Analytics', 'anyway-feedback' ); ?></h3>

	<p>
		<?php
		echo wp_kses_post( sprintf(
			// translators: %s is url to advanced usage.
			__( 'You can save all data as <a href="%s" target="_blank">event tracking</a> and analize report chronologically. Data format is like below:', 'anyway-feedback' ),
			'https://developers.google.com/analytics/devguides/collection/analyticsjs/events'
		) );
		?>
	</p>

	<table class="afb-speck">
		<thead>
		<tr>
			<th scope="col"><?php esc_html_e( 'Name', 'anyway-feedback' ); ?></th>
			<th scope="col"><?php esc_html_e( 'Value', 'anyway-feedback' ); ?></th>
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
				<td><?php wp_kses_post( __( '<code>Post ID</code> or <code>comment ID</code>', 'anyway-feedback' ) ); ?></td>
			</tr>
			<tr>
				<th>Value</th>
				<td><?php esc_html_e( 'Always 1', 'anyway-feedback' ); ?></td>
			</tr>
		</tbody>
	</table>

	<p class="description">
		<?php
		printf(
			// translators: %s is url to Google Analytics 4.
			__( 'This feature is premised on <a href="%s">Google Analytics 4</a>. If you use other services or Google Tag Manager, grab the event and record it by yourself.', 'anyway-feedback' ),
		'https://developers.google.com/analytics/devguides/collection/ga4/events?client_type=gtag&sjid=8607412637313615612-AP' );
		?>
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
