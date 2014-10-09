<?php
defined('ABSPATH') or die();
/** @var AFB\Admin\Screen $this */
?>
<form method="post" action="<?php echo $this->setting_url() ?>" id="afb-form">
	<?php wp_nonce_field("afb_option", "_afb_nonce");?>
	<table class="form-table">
		<tbody>
		<tr>
			<th><?php $this->i18n->e("Styling"); ?></th>
			<td>
				<p>
					<label><input type="radio" name="afb_style" value="0" <?php checked($this->option["style"] == 0) ?>/><?php $this->i18n->e("No style"); ?></label>
					<label><input type="radio" name="afb_style" value="1" <?php checked($this->option["style"] == 1) ?>/><?php $this->i18n->e("Auto load"); ?></label>
				</p>
				<p class="description">
					<?php printf($this->i18n->_("If you select &quot;No style&quot;, you need stylize skin. Skin s mark up can be specified at <strong>%s</strong> section"), $this->i18n->_("Custom markup")) ; ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php $this->i18n->e("Post type setting"); ?></th>
			<td>
				<p>
					<?php foreach(get_post_types() as $post_type ): $object = get_post_type_object($post_type); if( false === array_search($post_type, array("revision", "nav_menu_item"))): ?>
						<label><input type="checkbox" name="afb_post_types[]" value="<?php echo $post_type?>" <?php checked( false !== array_search($post_type, $this->option["post_types"])) ?>/><?php echo esc_html($object->labels->name) ?></label>
					<?php endif; endforeach; ?>
				</p>
				<p class="description">
					<?php $this->i18n->e("Checked post type will have feedback controller inside post content"); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><?php $this->i18n->e("Comment setting"); ?></th>
			<td>
				<p>
					<label><input type="radio" name="afb_comment" value="0" <?php checked($this->option["comment"] == 0) ;?>/><?php $this->i18n->e("Not show"); ?></label><br />
					<label><input type="radio" name="afb_comment" value="1" <?php checked($this->option["comment"] == 1) ;?>/><?php $this->i18n->e("Show in comment loop"); ?></label>
				</p>
				<p class="description">
					<?php $this->i18n->e("This option decide to display feedback controller in comment loop."); ?>
				</p>
			</td>
		</tr>
		<tr>
			<th><label for="afb_text"><?php $this->i18n->e("Custom markup"); ?></label></th>
			<td>
				<textarea id="afb_text" name="afb_text" rows="8"><?php echo esc_textarea(stripcslashes($this->option['controller'])) ?></textarea>
				<p class="description">
					<?php $this->i18n->e("You can customize markup of Feedback controller.<br />If you don't want, leave it blank.<br />In case of customization, You can use variables(%POSITIVE%, %TOTAL%, %NEGATIVE%, %POST_TYPE%) and <strong>2 link tags(&lt;a&gt;) must have class name &quot;good&quot; and &quot;bad&quot; and %LINK% as href attribute</strong>.<br />Default markup is below.");  ?>
				</p>
<pre class="afb-code-exam">
<?php
$message = esc_html(sprintf($this->i18n->_("Is this %s useful?"), "%POST_TYPE%"));
$useful = esc_html($this->i18n->_("Useful"));
$useless = esc_html($this->i18n->_("Useless"));
$status = esc_html(sprintf($this->i18n->_('%1$s of %2$s people say this %3$s is useful.'), "%POSITIVE%", "%TOTAL%", "%POST_TYPE%"));
$markup = <<<HTML
<span class="message">{$message}</span>
<a class="good" href="%LINK%">{$useful}</a>
<a class="bad" href="%LINK%">{$useless}</a>
<span class="status">{$status}</span>
HTML;
echo esc_html($markup)
?>
</pre>
			</td>
		</tr>
		<tr>
			<th><?php $this->i18n->e('Google Analytics Tracking') ?></th>
			<td>
				<label><input type="radio" name="afb_ga" value="1" <?php checked($this->option["ga"] == 1) ?>/><?php $this->i18n->e("Track"); ?></label>
				<label><input type="radio" name="afb_ga" value="0" <?php checked($this->option["ga"] == 0) ?>/><?php $this->i18n->e("Do not track"); ?></label>
				<p class="description">
					<span class="label">Since 1.0</span>
					<?php printf($this->i18n->_('This feature send report as event tracking to Google Analytics. You can get chronological report there. For detail, see <a href="%s">advanced usage</a>. '), $this->setting_url('advanced')) ?>
				</p>
			</td>
		</tr>
		</tbody>
	</table>
	<?php submit_button($this->i18n->_('Update')) ?>
</form>
