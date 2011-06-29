<?php
if($_SERVER["SCRIPT_FILENAME"] == __FILE__){
	die();
}
global $wp_post_types;
?>
<div class="wrap afb">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php $this->e("Anyway Feedback Option"); ?></h2>
	<?php do_action("admin_notice"); ?>
	<!-- //header -->
	
	
	<!-- contents // -->
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><?php $this->e("Statistics"); ?></a></li>
			<li><a href="#tabs-2"><?php $this->e("General Setting"); ?></a></li>
			<li><a href="#tabs-3"><?php $this->e("About"); ?></a></li>
			<li><a href="#tabs-4"><?php $this->e("Advanced Usage");?></a></li>
		</ul>
		
		<div id="tabs-1">
			<?php $record = $this->recorded_post_types(); if(empty($record)):?>
				<p class="error"><?php $this->e("There is no record."); ?></p>
			<?php else: foreach($record as $r): ?>
				<h3><?php $post_type_name = ($r == "comment") ? __("Comment") : $wp_post_types[$r]->labels->name; echo $post_type_name; ?></h3>
				<div class="afb-summery">
					<?php $total = $this->statistic("total", $r);  ?>
					<p class="description">
						<?php printf($this->_("There are %d feedbacks."), $total->positive + $total->negative); ?>
					</p>
					<img class="total-chart" alt="<?php printf($this->_("Statistic of %s"), $post_typa_name); ?>" width="300" height="300" src="https://chart.googleapis.com/chart?cht=p3&amp;chs=300x300&amp;chd=t:<?php echo $total->positive;?>,<?php echo $total->negative;?>&amp;chdl=<?php echo rawurlencode($this->_("Positive")."({$total->positive})"); ?>|<?php echo rawurlencode($this->_("Negative")."({$total->negative})"); ?>&amp;chco=13455B" />
				</div>
				<?php $rows = $this->get_all($r); ?>
				<div class="afb-table">
				<div class="floater">
				<div class="table-wrap">
					<table>
						<thead>
							<tr>
								<th scope="col"><?php $this->e("Object"); ?></th>
								<th scope="col"><?php $this->e("Positive"); ?></th>
								<th scope="col"><?php printf($this->_("%s Ratio"), $this->_("Positive")); ?></th>
								<th scope="col"><?php $this->e("Negative"); ?></th>
								<th scope="col"><?php printf($this->_("%s Ratio"), $this->_("Negative")); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td>&nbsp;</td>
								<th scope="row"><?php $this->e("Total"); ?></th>
								<td>
									<?php global $wpdb; $post_count = ($r == "comment") ? $wpdb->get_var("SELECT COUNT(comment_ID) FROM {$wpdb->comments} WHERE comment_approved = 1") :$wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish'", $r));?>
									<?php echo number_format($post_count); ?>
								</td>
								<th scope="row"><?php $this->e("Feedbacked");?></th>
								<td><?php echo number_format(count($rows));?></td>
							</tr>
						</tfoot>
						<tbody>
							<?php $counter = 0; foreach($rows as $row):  ?>
								<tr<?php if($counter % 2 == 0) echo ' class="alternate"'; ?>>
									<td>
										<?php if($r == "comment"): ?>
											<?php printf($this->_("<a href=\"%1\$s\">%2\$s</a> comments <a href=\"%3\$s\">%4\$s</a>"), admin_url("user-edit.php?user_id={$row->user_id}"), $row->comment_author, get_permalink($row->ID), $row->post_title); ?><br />
											<small>
												<a href="<?php echo admin_url("comment.php?c={$row->comment_ID}&amp;action=editcomment");?>"><?php $this->e("edit"); ?></a>
											</small>
										<?php else: ?>
											<?php echo $row->post_title; ?><br />
											<small><a href="<?php echo admin_url("post.php?post={$row->ID}&amp;action=edit");?>"><?php $this->e("edit"); ?></a> | <a href="<?php echo get_permalink($row->ID);  ?>" target="_blank"><?php $this->e('show'); ?></a></small>
										<?php endif; ?>
									</td>
									<td><?php echo $row->positive; ?></td>
									<td><?php echo floor($row->positive / $row->total * 100); ?>%</td>
									<td><?php echo $row->negative; ?></td>
									<td><?php echo floor($row->negative / $row->total * 100); ?>%</td>
								</tr>
							<?php $counter++; endforeach; ?>
						</tbody>
					</table>
				</div>
				</div>
				</div>
				<hr style="clear:left;" />
			<?php endforeach; endif; ?>
		</div>
		<div id="tabs-2">
			<form method="post" action="#tabs-2">
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
									<?php printf($this->_("If you select &quot;No style&quot;, you need stylize skin. Skin s mark up can be specified at <strong>%s</strong> section"), $this->_("Custom markup")) ; ?>
								</p>
							</td>
						</tr>
						<tr>
							<th><?php $this->e("Post type setting"); ?></th>
							<td>
								<p>
									<?php foreach($wp_post_types as $post_type => $object): if(false === array_search($post_type, array("revision", "nav_menu_item"))): ?>
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
						<tr>
							<th><label for="afb_text"><?php $this->e("Custom markup"); ?></label></th>
							<td>
								<textarea id="afb_text" name="afb_text" rows="5" cols="40"><?php if(!empty($this->option['controller'])) echo $this->option['controller']; ?></textarea>
								<p class="description">
									<?php $this->e("You can customize markup of Feedback controller.<br />If you don't want, leave it blank.<br />In case of customization, You can use variables(%POSITIVE%, %TOTAL%, %NEGATIVE%, %POST_TYPE%) and <strong>2 link tags(&lt;a&gt;) must have class name &quot;good&quot; and &quot;bad&quot; and %LINK% as href attribute</strong>.<br />Default markup is below.");  ?>
								</p>
								<pre>
&lt;span class=&quot;message"&gt;<?php printf($this->_("Is this %s usefull?"), "%POST_TYPE%"); ?>&lt;/span&gt;
&lt;a class=&quot;good&quot; href=&quot;%LINK%"&gt;<?php $this->e("Usefull"); ?>&lt;/a&gt;
&lt;a class=&quot;bad&quot; href=&quot;%LINK%"&gt;<?php $this->e("Useless"); ?>&lt;/a&gt;
&lt;span class=&quot;status&quot;&gt;<?php printf($this->_("%1\$s of %2\$s people say this %3\$s is usefull."), "%POSITIVE%", "%TOTAL%", "%POST_TYPE%"); ?>&lt;/span&gt;
								</pre>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php $this->e("Update"); ?>" />
				</p>
			</form>
		</div>
		
		<div id="tabs-3">
			<h3><?php $this->e("Detailed description"); ?></h3>
			<p>
				<?php $this->e("This plugin enables user to feed back to post or comment.<br />It may help you to analyze your subscriber's feeling.");?>
			</p>
			<h3><?php $this->e("Who made this plugin"); ?></h3>
			<p>
				<?php $this->e("Takahashi Fumiki did. I am a wordpress developer and novelist. See detail at <a href=\"http://takahashifumiki.com\">takahashifumiki.com</a>"); ?>
			</p>
			<h3><?php $this->e("Feedback"); ?></h3>
			<p>
				<?php $this->e("If you have some request, please feel free to contact with <a href=\"mailto:takahashi.fumiki@hametuha.co.jp\">takahashi.fumiki@hametuha.co.jp</a>. Japanese, English and French are welcomed."); ?>
			</p>
			<h3><?php $this->e("Donation"); ?></h3>
			<p>
				<?php $this->e("If you think this plugin is usefull, please donate for it and make me motivated. In other words, buy me a beer.");?>
			</p>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="V2CUE7S2FBRKG">
				<table>
				<tr>
					<td><input type="hidden" name="on0" value="金額">金額</td>
				</tr>
				<tr>
					<td>
						<select name="os0">
							<option value="発泡酒">発泡酒 ¥100</option>
							<option value="エビス500ml">エビス500ml ¥300</option>
							<option value="発泡酒6缶パック">発泡酒6缶パック ¥1,000</option>
							<option value="ビール1ケース">ビール1ケース ¥3,000</option>
						</select>
					</td>
				</tr>
				</table>
				<input type="hidden" name="currency_code" value="JPY">
				<input type="image" src="https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal- オンラインで安全・簡単にお支払い">
				<img alt="" border="0" src="https://www.paypalobjects.com/ja_JP/i/scr/pixel.gif" width="1" height="1">
			</form>
		</div>
		
		<div id="tabs-4">
			<h3><?php $this->e("Template Tags");?></h3>
			<p>
				<?php $this->e("If you are experienced developper, you may need customazation. Ofcourse, you can edit your theme and get your own appearance."); ?>
			</p>
			<dl>
				<dt><span class="blue">afb_display</span>()</dt>
				<dd>
					<p>
						<?php $this->e("Display controller of post feedback. Use inside loop.");?>
					</p>
				</dd>
				<dt><span class="blue">afb_comment_display</span>( (int) <span class="yellow">$comment_id</span> )</dt>
				<dd>
					<p>
						<?php $this->e("Display controller of comment feedback. You must pass comment_ID as 1st argument.");?>
					</p>
				</dd>
				<dt><span class="blue">afb_affirmative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
				<dd>
					<p>
					<?php $this->e("Display number of affirmative feedbacks of specified post. In loop, you don't have to specify \$object_id and \$post_type. \$echo set true, value won't displayed and just return."); ?>
					</p>
				</dd>
				<dt><span class="blue">afb_negative</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
				<dd>
					<p>
					<?php $this->e("Display number of negative feedbacks of specified post. Same as afb_affirmative."); ?>
					</p>
				</dd>
				<dt><span class="blue">afb_total</span>( (boolean) <span class="yellow">$echo</span> = <span class="red">true</span>, (int) <span class="yellow">$object_id</span> = <sapn class="red">null</sapn>, (string) <span class="yellow">$post_type</span> = <sapn class="red">null</sapn>)</dt>
				<dd>
					<p>
					<?php $this->e("Display number of total feedbacks of specified post. Same as afb_affirmative."); ?>
					</p>
				</dd>
			</dl>
			<p class="description">
				<?php printf($this->_("All these template tags above should be wrapped inside if declaration for compatibility. If not, stopping this plugin will break your theme's display.<br />ex.&lt?php if(function_exists(&quot;afb_display&quot;)){afb_display();} ?&gt;<br />See function detail at <em>%s</em>"), dirname(plugin_dir_path(__FILE__)).DIRECTORY_SEPARATOR."functions.php"); ?>
			</p>
		</div>
	</div>
	<!-- // contents -->
</div>