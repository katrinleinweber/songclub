<?php
	define('JZ_SECURE_ACCESS','true');
	
	if (!(isset($define_only) && $define_only)) {
	  include_once('../../system.php');		
	  include_once('../../settings.php');
    }
	$skin = "darklights";
	
	// Now let's set the colors for this thing
	define("jz_pg_bg_color","#000000");
	define("jz_bg_color","#4A4A4A");
	define("jz_fg_color","#CCCC99");
	define("jz_font_color","#9C9C9C");
	define("jz_link_color","#FDCD00");
	define("jz_link_hover_color","#000000");
	define("jz_select_bg","#1A1A1A");
	define("jz_select_font_color","#FDCD00");
	define("jz_submit_bg","#1A1A1A");
	define("jz_submit_font_color","#FDCD00");
	define("jz_input_bg","#1A1A1A");
	define("jz_input_font_color","#FDCD00");
	define("jz_row1","#4A4A4A");
	define("jz_row2","#666666");
	define("jz_headers","#FDCD00");
	define("jz_default_table_color","#4A4A4A");
	define("jz_default_border","1px solid black");
	
	// Do they want the whole stylesheet
	if (isset($define_only)){if ($define_only){return;}}
	
	include_once("../css.php");
?>