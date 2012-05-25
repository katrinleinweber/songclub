<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *      
	* JINZORA | Web-based Media Streamer 
	*
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL. 
	* 
	* Jinzora Author:
	* Ross Carlson: ross@jasbone.com
	* http://www.jinzora.org
	* Documentation: http://www.jinzora.org/docs	
	* Support: http://www.jinzora.org/forum
	* Downloads: http://www.jinzora.org/downloads
	* License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* Contributors:
	* Please see http://www.jinzora.org/modules.php?op=modload&name=jz_whois&file=index
	*
	* Code Purpose: Processes data for the jlGui embedded Java Player
	* Created: 03.03.05 by Ross Carlson
	*
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

	define('SERVICE_PLAYERS_jwmp3','true');

	/**
	* Returns the player width
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_WIDTH_jwmp3(){
	  return 300;
	}

	/**
	* Returns the players height.
	* 
	* @author Ben Dodson
	* @version 8/23/05
	* @since 8/23/05
	*/
	function SERVICE_RETURN_PLAYER_HEIGHT_jwmp3(){
	  return 150;
	}

	
	/**
	* Returns the data for the form posts for the player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	* @param $formname The name of the form that is being created
	*/
	function SERVICE_RETURN_PLAYER_FORM_LINK_jwmp3($formname){
		return "document.". $formname. ".target='embeddedPlayer'; openMediaPlayer('', 300, 150);";
	}
	
	
	/**
	* Returns the data for the href's to open the popup player
	* 
	* @author Ross Carlson
	* @version 06/05/05
	* @since 06/05/05
	*/
	function SERVICE_RETURN_PLAYER_HREF_jwmp3(){
		return ' target="embeddedPlayer" onclick="openMediaPlayer(this.href, 300, 150); return false;"';
	}
	

	/**
	* Actually displays this embedded player
	* 
	* @author Ross Carlson
	* @version 3/03/05
	* @since 3/03/05
	* @param $list an array containing the tracks to be played
	*/
	function SERVICE_DISPLAY_PLAYER_jwmp3($width, $height){
		global $root_dir, $this_site, $css,$JWMP3_OPTS;
		
		if (!isset($JWMP3_OPTS)) {
        	$JWMP3_OPTS = "autostart=true&thumbsinplaylist=true&showeq=true&showdigits=true&repeat=false&shuffle=false&lightcolor=0x1414E9&backcol\
or=0x1e1e1e&frontcolor=0xCCCCCC";
        }
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (false === stristr($useragent, 'Nintendo Wii')) {
        	//$this_player_name = 'mp3player_flash7.swf';
        	$this_player_name = 'mp3player.swf';
        } else {
        	$this_player_name = 'mp3player.swf';
        }
                
                
		$local_jwmp3_vars = "file=${this_site}${root_dir}/temp/playlist.xspf&" . $JWMP3_OPTS;
		?>
		<SCRIPT LANGUAGE=JAVASCRIPT TYPE="TEXT/JAVASCRIPT"><!--\
			window.resizeTo(<?php echo $width; ?>,<?php echo $height; ?>)
		-->
		</SCRIPT>
		<?php	
		
		// Let's setup the page
		echo '<script type="text/javascript" src="';echo $this_site. $root_dir; echo'/services/services/players/ufo.js"></script>';
		echo '<title>Jinzora jwmp3 Media Player</title>';
		echo '<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#000000"><center>';
		echo '<div id="flashbanner">this text will be replaced by the SWF.</div>';
		
		$playlist = $this_site. $root_dir. "/temp/playlist.xspf?". time();
		$height = $height - 45;
		?>

		<script type="text/javascript">
		var FO = { 
		  movie:"<?php echo $this_site. $root_dir; ?>/services/services/players/<?php echo $this_player_name; ?>", 
		  width:"280",height:"280",majorversion:"7",build:"0",
		  flashvars:"<?php echo $local_jwmp3_vars;?>"
		};
		UFO.create(FO, "flashbanner");
		</script>	
		
		
		<?php
		exit();
	}

	
	/**
	* Processes data for the jlGui embedded player
	* 
	* @author Ross Carlson
	* @version 3/03/05
	* @since 3/03/05
	* @param $list an array containing the tracks to be played
	*/
	function SERVICE_OPEN_PLAYER_jwmp3($list){
		global $include_path, $root_dir, $this_site;
	
		$display = new jzDisplay();

		// Let's set the name of this player for later
		$player_type = "jwmp3";		
				
		// Now let's loop through each file
		$list->flatten();

		$output_content = '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
		$output_content .= '<playlist version="1" xmlns = "http://www.jinzora.org">'. "\n";
		$output_content .= '  <trackList>'. "\n";
		
		// Now let's loop throught the items to create the list
		foreach ($list->getList() as $track) {
			// Should we play this?
			if ((stristr($track->getPath("String"),".lofi.") 
				or stristr($track->getPath("String"),".clip."))
				and $_SESSION['jz_play_all_tracks'] <> true){continue;}
				
			// Let's get the meta
			$meta = $track->getMeta();
			
			// Let's get the art
			$parent = $track->getParent();
			if (($art = $parent->getMainArt()) !== false) {
				$image = jzCreateLink($art,"image");
			} else {
				$image = $this_site. $root_dir. "/style/images/default.jpg";
			}
			
			$output_content .= '    <track>'. "\n";
			$output_content .= '      <location>'. $track->getFileName("user"). '</location>'. "\n";
			$output_content .= '      <image>'. $image. '</image>'. "\n";
			$output_content .= '      <title>'. $meta['artist']. " - ". $meta['title']. '</title>'. "\n";
		//	$output_content .= '      <title>'. $meta['title']. '</title>'. "\n";
			$output_content .= '    </track>'. "\n";
		}

		// Now let's finish up the content
		$output_content .= '  </trackList>'. "\n";
		$output_content .= '</playlist>';
		
		// Now that we've got the playlist, let's write it out to the disk
		$plFile = $include_path. "temp/playlist.xspf";
		@unlink($plFile);
		$handle = fopen ($plFile, "w");
		fwrite($handle,$output_content);				
		fclose($handle);
			
		// Now let's display
		$width = "315";
		$height = "300";
		SERVICE_DISPLAY_PLAYER_jwmp3($width, $height);
	}	
?>
