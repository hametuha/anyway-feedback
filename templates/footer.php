<?php
defined( 'ABSPATH' ) or die();
/** @var AFB\Admin\Screen $this */
?>

<div id="afb-info" class="clearfix">

	<div class="div_4">
		<h3><i class="dashicons dashicons-editor-help"></i> <?php _e( 'About Anyway Feedback', 'anyway-feedback' ); ?></h3>
		<p>
			<?php _e( "This plugin enables user to feed back to post or comment. It may help you to analyze your subscriber's feeling.", 'anyway-feedback' ); ?>
		</p>
	</div><!-- //.div_4 -->

	<div class="div_4">
		<h3><i class="dashicons dashicons-id-alt"></i> <?php _e( 'Who made this plugin', 'anyway-feedback' ); ?></h3>
		<p class="clearfix">
			<?php echo get_avatar( 'takahashi.fumiki@hametuha.co.jp', 60 ); ?>
			<?php
			printf(
				// translators: %s is link to WordPress.org profile.
				esc_html__( 'Takahashi Fumiki did. I am a WordPress developer and novelist. See detail at %s', 'anyway-feedback' ),
				'<a href="https://profiles.wordpress.org/takahashi_fumiki/" target="_blank" rel="noopener noreferrer">WordPrss.org</a>'
			);
			?>
		</p>
	</div><!-- //.div_4 -->

	<div class="div_4">
		<h3><i class="dashicons dashicons-email-alt"></i> <?php esc_html_e( 'Contact', 'anyway-feedback' ); ?></h3>
		<p>
			<?php esc_html_e( 'If you have some request, please feel free to contact via:', 'anyway-feedback' ); ?>
		</p>
		<p class="contact">
			<?php
			foreach ( array(
				'wordpress' => 'https://wordpress.org/support/plugin/anyway-feedback',
				'twitter'   => 'https://twitter.com/takahashifumiki',
				'facebook'  => 'https://www.facebook.com/TakahashiFumiki.Page',
			) as $icon => $url ) :
				?>
				<a href="<?php echo esc_html( $url ); ?>" target="_blank"><i class="dashicons dashicons-<?php echo $icon; ?>"></i></a>
			<?php endforeach; ?>
		</p>
		<p>
			<?php
			printf(
				// translators: %s is link to GitHub.
				esc_html__( 'Japanese, English and French are welcomed. Of course, you can send pull request via %s.', 'anyway-feedback' ),
				'<a href="https://github.com/hametuha/anyway-feedback" target="_blank" rel="noopener noreferrer">GitHub</a>'
			);
			?>
		</p>
	</div><!-- //.div_4 -->


	<div class="div_4">
		<h3><i class="dashicons dashicons-heart"></i> <?php _e( 'Donation', 'anyway-feedback' ); ?></h3>
		<p>
			<?php _e( 'If you think this plugin is useful, please donate for it and make me motivated. In other words, buy me a beer.', 'anyway-feedback' ); ?>
		</p>

		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="V2CUE7S2FBRKG">
			<table>
				<tr>
					<td>
						<input type="hidden" name="on0" value="金額">
						<select name="os0">
							<option value="発泡酒"><?php _e( 'Low mal beer', 'anyway-feedback' ); ?> &yen;100</option>
							<option value="エビス500ml"><?php _e( 'Ebisu beer', 'anyway-feedback' ); ?> &yen;300</option>
							<option value="発泡酒6缶パック"><?php _e( '6 cans of beer', 'anyway-feedback' ); ?> &yen;1,000</option>
							<option value="ビール1ケース"><?php _e( '1 case of beer', 'anyway-feedback' ); ?> &yen;3,000</option>
						</select>
					</td>
				</tr>
			</table>
			<input type="hidden" name="currency_code" value="JPY">
			<input type="image" src="https://www.paypalobjects.com/ja_JP/JP/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal- オンラインで安全・簡単にお支払い">
			<img alt="" border="0" src="https://www.paypalobjects.com/ja_JP/i/scr/pixel.gif" width="1" height="1">
		</form>
	</div>

</div>
