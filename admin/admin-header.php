<?php
if($_SERVER["SCRIPT_FILENAME"] == __FILE__){
	die();
}

//Update General Settings
if(isset($_POST['_afb_nonce'], $_POST['_wp_http_referer']) && wp_verify_nonce($_POST['_afb_nonce'], "afb_option") && false !== strpos($_POST['_wp_http_referer'], "page=anyway-feedback")){
	//validation
	$option = array();
	//style
	if(false === array_search($_POST["afb_style"], array(0,1))){
		$this->error[] = sprintf($this->_("%s is invalid."), $this->_("Styling"));
	}else{
		$option["style"] = (int)$_POST["afb_style"];
	}
	//post_types
	if(isset($_POST["afb_post_types"]) && is_array($_POST["afb_post_types"])){
		$option["post_types"] = $_POST["afb_post_types"];
	}else{
		$option["post_types"] = array();
	}
	//comment
	if(false === array_search($_POST["afb_style"], array(0,1))){
		$this->error[] = sprintf($this->_("%s is invalid."), $this->_("Comment setting"));
	}else{
		$option["comment"] = (int)$_POST["afb_comment"];
	}
	//controlelr
	$option["controller"] = (empty($_POST["afb_text"])) ? "" : $_POST["afb_text"];
	
	//update
	if(empty($this->error)){
		update_option("afb_setting", $option);
		$this->option = $option;
		$this->message[] = $this->_("Option updated.");
	}else{
		$this->error[] = $this->_("Update option failed because of list above.");
	}
}
