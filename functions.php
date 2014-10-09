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
 */
function afb_display(){
	$afb = _afb();
	if( in_the_loop() ){
		echo $afb->get_controller_tag(get_the_ID(), get_post_type());
	}
}

/**
 * Display Anyway Feedback buttons for comment.
 * @param int $comment_id
 * @return void
 */
function afb_comment_display($comment_id){
	$afb = _afb();
	echo $afb->get_controller_tag($comment_id, "comment");
}

/**
 * Retrieve total feedback count. Use inside loop.
 *
 * @param boolean $echo (optional) Return value if false.
 * @param int $object_id,
 * @param string $post_type
 * @return int
 */
function afb_total($echo = true, $object_id = null, $post_type = null){
	$afb = _afb();
	if( is_null($object_id) ){
		$object_id = get_the_ID();
	}
	if( is_null($post_type) ){
		$post_type = get_post_type();
	}
	$total = $afb->feedbacks->total_answer($object_id, $post_type);
	if( $echo ){
		echo $total;
	}
	return $total;
}

/**
 * Retrieve affirmative feedback count. Use inside loop.
 * 
 * @param boolean $echo (optional) Return value if false. 
 * @return int
 */
function afb_affirmative($echo = true, $object_id = null, $post_type = null){
	$afb = _afb();
	if(is_null($object_id)){
		$object_id = get_the_ID();
	}
	if(is_null($post_type)){
		$post_type = get_post_type();
	}
	$total = $afb->feedbacks->affirmative($object_id, $post_type);
	if( $echo ){
		echo $total;
	}
	return $total;
}

/**
 * Retrieve negative feedback count. Use inside loop.
 * 
 * @param boolean $echo (optional) Return value if false. 
 * @return void|int
 */
function afb_negative($echo = true, $object_id = null, $post_type = null){
	$afb = _afb();
	if(is_null($object_id)){
		$object_id = get_the_ID();
	}
	if(is_null($post_type)){
		$post_type = get_post_type();
	}
	$total = $afb->feedbacks->negative($object_id, $post_type);
	if( $echo ){
		echo $total;
	}
	return $total;
}


/**
 * Get instance
 *
 * @return \AFB\Main
 */
function _afb(){
	return AFB\Main::get_instance();
}
