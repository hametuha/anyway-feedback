<?php
/**
 * Template tags for users
 *
 * @package Anyway Feedback
 * @since 0.1
 */

/**
 * Display Anyway Feedback buttons.Use inside loop.
 * 
 * @param mixed $class_name class name to append
 * @retun void
 */
function afb_display($class_name = null){
	$class = "afb_container";
	if($class_name){
		if(is_array($class_name)){
			$class .= implode(" ", $class_name);
		}else{
			$class .= " ". (string) $class_name;
		}
	}
	global $afb, $post; if($post)
	?>
	<div class="<?php echo htmlspecialchars($class, ENT_QUOTES, "utf-8");?>" id="afb_comment_container_<?php the_ID(); ?>">
		<span class="message"><?php $afb->e("Is this article usefull?");?></span>
		<a class="good" href="<?php the_permalink(); ?>"><?php $afb->e("Useful"); ?></a>
		<a class="bad" href="<?php the_permalink(); ?>"><?php $afb->e("Useless"); ?></a>
		<input type="hidden" name="post_type" value="<?php echo get_post_type(); ?>" />
		<input type="hidden" name="object_id" value="<?php the_ID(); ?>" />
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("anyway_feedback");?>" />
		<span class="status">
			<?php printf($afb->_("%d of %d people say this article is usefull."), afb_affirmative(false), afb_total(false));?>
		</span>
	</div>
	<?php
}

/**
 * Display Anyway Feedback buttons for comment.
 * @param int $comment_id
 * @return void
 */
function afb_comment_display($comment_id){
	$class = "afb_container";
	global $afb;
	?>
	<div class="<?php echo $class; ?>" id="afb_comment_container_<?php echo $comment_id; ?>">
		<span class="message"><?php $afb->e("Is this comment usefull?");?></span>
		<a class="good" href="<?php the_permalink(); ?>"><?php $afb->e("Useful"); ?></a>
		<a class="bad" href="<?php the_permalink(); ?>"><?php $afb->e("Useless"); ?></a>
		<input type="hidden" name="post_type" value="comment" />
		<input type="hidden" name="object_id" value="<?php echo $comment_id; ?>" />
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce("anyway_feedback");?>" />
		<span class="status">
			<?php printf($afb->_("%d of %d people say this comment is usefull."), afb_affirmative(false, $comment_id, "comment"), afb_total(false, $comment_id, "comment"));?>
		</span>
	</div>
	<?php
}

/**
 * Retrieve total feedback count. Use inside loop.
 *
 * @param boolean $echo (optional) Return value if false. 
 * @return void|int
 */
function afb_total($echo = true, $object_id = null, $post_type = null){
	global $wpdb, $afb;
	$sql = "SELECT (positive + negative) as total FROM {$afb->table} WHERE object_id = %d AND post_type = %d";
	if(is_null($object_id)){
		$object_id = get_the_ID();
	}
	if(is_null($post_type)){
		$post_type = get_post_type();
	}
	$total = (int) $wpdb->get_var($wpdb->prepare($sql, $object_id, $post_type));
	if($echo){
		echo $total;
	}else{
		return $total;
	}
}

/**
 * Retrieve affirmative feedback count. Use inside loop.
 * 
 * @param boolean $echo (optional) Return value if false. 
 * @return void|int
 */
function afb_affirmative($echo = true, $object_id = null, $post_type = null){
	global $wpdb, $afb;
	$sql = "SELECT positive FROM {$afb->table} WHERE object_id = %d AND post_type = %d";
	if(is_null($object_id)){
		$object_id = get_the_ID();
	}
	if(is_null($post_type)){
		$post_type = get_post_type();
	}
	$total = (int) $wpdb->get_var($wpdb->prepare($sql, $object_id, $post_type));
	if($echo){
		echo $total;
	}else{
		return $total;
	}
}