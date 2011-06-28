<?php
if($_SERVER["SCRIPT_FILENAME"] == __FILE__){
	die();
}
?>
<div class="wrap afb">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php $this->e("Anyway Feedback Option"); ?></h2>
	<?php do_action("admin_notice"); ?>
	<!-- //header -->
	
	
	<!-- contents // -->
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php $this->e("General Setting"); ?></a></li>
			<li><a href="#tabs-2"><?php $this->e("About"); ?></a></li>
			<li><a href="#tabs-3"><?php $this->e("Advanced Use");?></a></li>
		</ul>
		
		<div id="tabs-1">
			<form method="post">
				<?php wp_nonce_field("afb_option", "_afb_nonce");?>
				<table class="form-table">
					<tbody>
						<tr>
							<th><?php $this->e("Styling"); ?></th>
							<td>
								<p>
									<label><input type="radio" name="afb_style" value="0" <?php if($this->option["style"] == 0) echo 'checked="checked" ';?>/><?php $this->e("No style"); ?></label>
									<label><input type="radio" name="afb_style" value="1" <?php if($this->option["style"] == 1) echo 'checked="checked" ';?>/><?php $this->e("Auto load"); ?></label>
								</p>
								<p class="description">
									<?php printf($this->_("If you select &quot;No style&quot;, you need stylize skin. Skin s mark up is in <a class=\"change-tab\" href=\"%s\">How to use</a> section"), "#tabs-3"); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th><?php $this->e("Post type setting"); ?></th>
							<td>
								<p>
									<?php global $wp_post_types; foreach($wp_post_types as $post_type => $object): if(false === array_search($post_type, array("revision", "nav_menu_item"))): ?>
										<label><input type="checkbox" name="afb_post_types[]" value="<?php echo $post_type?>" <?php if(false !== array_search($post_type, $this->option["post_types"])) echo 'checked="checked" ';?>/><?php echo $object->labels->name; ?></label>
									<?php endif; endforeach; ?>
								</p>
								<p class="description">
									<?php $this->e("Checked post type will have feedback controller inside post content"); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th><?php $this->e("Comment setting"); ?></th>
							<td>
								<p>
									<label><input type="radio" name="afb_comment" value="0" <?php if($this->option["comment"] == 0) echo 'checked="checked" ';?>/><?php $this->e("Not show"); ?></label><br />
									<label><input type="radio" name="afb_comment" value="1" <?php if($this->option["comment"] == 1) echo 'checked="checked" ';?>/><?php $this->e("Show in comment loop"); ?></label>
								</p>
								<p class="description">
									<?php $this->e("This option decide to display feedback controller in comment loop."); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php $this->e("Update"); ?>" />
				</p>
			</form>
		</div>
		
		<div id="tabs-2">
			<h3><?php $this->e("Detailed description"); ?></h3>
			<p>
				<?php $this->e("This plugin enables user to feed back to post or comment.<br />It may help you to analyze your subscriber's ");?>
			</p>
			<h3><?php $this->e("Who make this plugin"); ?></h3>
			<p>
				
			</p>
		</div>
		
		<div id="tabs-3">
			
		</div>
	</div>
	<!-- // contents -->
</div>