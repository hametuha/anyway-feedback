<?php

namespace AFB\Widget;


/**
 * Widget for Popular post
 *
 * @package Anyway Feedback
 */
class Popular extends \WP_Widget
{
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	function Anyway_Feedback_Popular(){
		global $afb;
		parent::WP_Widget(
			"anyway-feedback-popular-widgets",
			'Anyway_Feedback_Popular',
			array(
				"description" => $afb->_("This widget shows most popular post per post type."),
				"classname" => "anyway-feedback-popular-widget"
			)
		);
	}
	
	/**
	 * Retrieve
	 */
	function widget($args, $instance){
		global $wpdb, $afb;
		extract( $args );
        $title = empty($instance['title']) ? $this->name :apply_filters('widget_title', $instance['title']);
		$post_type = empty($instance['post_type']) ? 'post' :esc_attr($instance['post_type']);
		$num_posts = empty($instance['num_posts']) ? 5 : esc_attr($instance['num_posts']);
		$sql = <<<SQL
			SELECT
				post.post_title, post.ID, afb.positive
			FROM {$afb->table} AS afb
			LEFT JOIN {$wpdb->posts} AS post
			ON afb.object_id = post.ID AND afb.post_type = post.post_type
			WHERE afb.post_type = %s
			  AND post.post_status = 'publish'
			ORDER BY positive DESC
			LIMIT %d
SQL;
		$posts = $wpdb->get_results($wpdb->prepare($sql, $post_type, $num_posts));
		?>
              <?php echo $before_widget; ?>
                  <?php if ( $title ): ?>
                       <?php echo $before_title . $title . $after_title; ?>
                  <?php endif; ?>
                  <ul>
                  	<?php if(empty($posts)): ?>
                  		<li><?php $afb->e("There is no feedback."); ?></li>
                  	<?php else: foreach($posts as $p): ?>
                  		<li><a href="<?php echo get_permalink($p->ID); ?>"><?php echo apply_filters('the_title', $p->post_title);?></a><span class="count">(<?php printf($afb->_("%d sais usefull."), $p->positive); ?>)</span></li>
                  	<?php endforeach; endif; ?>
                  </ul>
              <?php echo $after_widget; ?>
        <?php
	}
	
	/**
	 * Update function 
	 * @see WP_Widget
	 */
	function update($new_instance, $old_instance) {				
        return $new_instance;
    }
	
	/**
	 * Form to update widget
	 * 
	 * @see WP_Widget
	 */
	function form($instance) {
		global $afb, $wpdb;
        $title = esc_attr($instance['title']);
		$post_type = esc_attr($instance['post_type']);
		$num_posts = esc_attr($instance['num_posts']);
		$sql = "SELECT DISTINCT post_type FROM {$wpdb->posts} WHERE post_type NOT IN ('draft', 'revision', 'nav_menu_item', 'attachment') GROUP BY post_type ORDER BY post_type ASC";
		$results = $wpdb->get_results($sql);
        ?>
            <p>
            	<label for="<?php echo $this->get_field_id('title'); ?>">
            		<?php _e('Title:'); ?>
            		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            	</label>
            	<label for="<?php echo $this->get_field_id('post_type'); ?>">
            		<?php $afb->e('Post Type:'); ?> <br />
	            	<select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>">
	            		<?php foreach($results as $r): ?>
	            			<option<?php if($r->post_type == $post_type) echo ' selected="selected"'; ?> value="<?php echo $r->post_type; ?>"><?php echo $r->post_type; ?></option>
	            		<?php endforeach; ?>
	            	</select>
            	</label><br />
            	<label for="<?php echo $this->get_field_id('num_posts'); ?>">
            		<?php $afb->e('Number of posts:'); ?> <br />
            		<input type="text" id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" value="<?php echo $num_posts;?>" />
            	</label>
            </p>
        <?php 
    }
}
