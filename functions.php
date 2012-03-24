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
function afb_display(){
	global $afb, $post;
	if($post){
		echo $afb->get_conroller_tag(get_the_ID(), get_post_type());
	}
}

/**
 * Display Anyway Feedback buttons for comment.
 * @param int $comment_id
 * @return void
 */
function afb_comment_display($comment_id){
	global $afb;
	echo $afb->get_conroller_tag($comment_id, "comment");
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

/**
 * Retrieve negative feedback count. Use inside loop.
 * 
 * @param boolean $echo (optional) Return value if false. 
 * @return void|int
 */
function afb_negative($echo = true, $object_id = null, $post_type = null){
	global $wpdb, $afb;
	$sql = "SELECT negative FROM {$afb->table} WHERE object_id = %d AND post_type = %d";
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
 * Just define for tarnsalation.
 * @global Anyway_Feedbackpe $afb
 * @return void
 */
function _afb_tranlation(){
	global $afb;
	$afb->_("Help to assemble simple feedback(negative or positive) and get statics of them.");
}