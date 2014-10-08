<?php
defined('ABSPATH') or die();
/** @var AFB\Screen $this */
?>
<div id="tabs-4">

	<h3><?php $this->i18n->e("Template Tags");?></h3>

	<p>
		<?php $this->i18n->e("If you are experienced developper, you may need customazation. Ofcourse, you can edit your theme and get your own appearance."); ?>
	</p>
	<dl class="code-format">
		<dt><span class="blue">afb_display</span>()</dt>
		<dd>
			<p>
				<?php $this->i18n->e("Display controller of post feedback. Use inside loop.");?>
			</p>
		</dd>
		<dt><span class="blue">afb_comment_display</span>( (int) <span class="yellow">$comment_id</span> )</dt>
		<dd>
			<p>
				<?php $this->i18n->e("Display controller of comment feedback. You must pass comment_ID as 1st argument.");?>
			</p>
		</dd>
		<dt><span class="blue">afb_affirmative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php $this->i18n->e("Display number of affirmative feedbacks of specified post. In loop, you don't have to specify \$object_id and \$post_type. \$echo set true, value won't displayed and just return."); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_negative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php $this->i18n->e("Display number of negative feedbacks of specified post. Same as afb_affirmative."); ?>
			</p>
		</dd>
		<dt><span class="blue">afb_total</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
		<dd>
			<p>
				<?php $this->i18n->e("Display number of total feedbacks of specified post. Same as afb_affirmative."); ?>
			</p>
		</dd>
	</dl>
	<p class="description">
		<?php $this->i18n->e("All these template tags above should be wrapped inside if declaration for compatibility. If not, stopping this plugin will break your theme's display."); ?>
	</p>
<pre>
<?php $tag = <<<HTML
<?php if( function_exists("afb_display") ){ afb_display(); } ?>
HTML;
echo esc_html($tag);
?>
</pre>
	<p class="description">
		<?php printf($this->i18n->_('See function detail at <code>%s</code>'), dirname(plugin_dir_path(__FILE__)).DIRECTORY_SEPARATOR."functions.php"); ?>
	</p>


	<h3><?php $this->i18n->e('Google Analytics') ?></h3>

	<p><?php printf($this->i18n->_('You can save all data as <a href="%s" target="_blank">event tracking</a> and analize report chronologically. Data format is like below:'), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/events') ?></p>

	<dl>
		<dt>Category</dt>
		<dd><code>anyway-feedback/post</code> or <code>anyway-feedback/comment</code></dd>
		<dt>Action</dt>
		<dd><code>positive</code> or <code>negative</code></dd>
		<dt>Label</dt>
		<dd><?php $this->i18n->e('<code>Post ID</code> or <code>comment ID</code>') ?></dd>
		<dt>Value</dt>
		<dd><?php $this->i18n->e('Always 1') ?></dd>
	</dl>

	<p class="description">
		<?php printf($this->i18n->_('This feature is premised on <a href="%s">Universal Analytics</a>. If you use other services or ga.js, grab the event and record it by yourself.'), 'https://developers.google.com/analytics/devguides/collection/analyticsjs/'); ?>
	</p>

<pre>
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
echo esc_html($script);
?>
</pre>

</div>