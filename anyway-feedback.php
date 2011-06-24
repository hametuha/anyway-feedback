<?php
 /*
	Plugin Name: Anyway Feedback
	Plugin URI: http://hametuha.co.jp/
	Description: Help to assemble simple feedback(negative or positive) and get statics of them.
	Version: 0.1
	Author: Takahashi Fumiki (Hametuha inc.)
	Author URI: http://hametuha.co.jp
	Copyright 2011 hametuha(email : info@hametuha.co.jp)
 	TextDomain: anyway-feedback
  
  
  
	 Copyright 2011 Takahashi Fumiki (email : takahashi.fumiki@hametuha.co.jp)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
 
 */

//Load required files
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."anyway-feedback.class.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."widgets".DIRECTORY_SEPARATOR."popular.php";
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."functions.php";

//Make Instance
global $afb;
$afb = new Anyway_Feedback();

//Register Activation Hook.
register_activation_hook(__FILE__, array($afb, "activate"));