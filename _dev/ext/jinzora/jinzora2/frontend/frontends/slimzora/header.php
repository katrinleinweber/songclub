<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.');
	/**
	* - JINZORA | Web-based Media Streamer -  
	* 
	* Jinzora is a Web-based media streamer, primarily desgined to stream MP3s 
	* (but can be used for any media file that can stream from HTTP). 
	* Jinzora can be integrated into a CMS site, run as a standalone application, 
	* or integrated into any PHP website.  It is released under the GNU GPL.
	* 
	* - Resources -
	* - Jinzora Author: Ross Carlson <ross@jasbone.com>
	* - Web: http://www.jinzora.org
	* - Documentation: http://www.jinzora.org/docs	
	* - Support: http://www.jinzora.org/forum
	* - Downloads: http://www.jinzora.org/downloads
	* - License: GNU GPL <http://www.gnu.org/copyleft/gpl.html>
	* 
	* - Contributors -
	* Please see http://www.jinzora.org/team.html
	* 
	* - Code Purpose -
	* - Contains the Slimzora display functions
	*
	* @since 02.17.04 
	* @author Ross Carlson <ross@jinzora.org>
	* @author Ben Dodson <ben@jinzora.org>
	*/
	require_once($include_path. 'frontend/class.php');
	require_once($include_path. 'frontend/blocks.php');		
	
	// override the blocks and frontend to be slim:
	class jzBlocks extends jzBlockClass {
	
		function trackTable($tracks, $showNumbers = true, $showArtist = false) {
			global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
			$img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $show_images, $jzUSER;					
			
			if (sizeof($tracks) == 0) return;
			
			// Let's setup the new display object
			$display = &new jzDisplay();
			
			// Now let's setup the big table to display everything
			$i=0;
			?>
			<table class="jz_track_table" width="100%" cellpadding="3">
			<?php
			foreach ($tracks as $child) {
				// First let's grab all the tracks meta data
				$metaData = $child->getMeta();
				?>
				<tr class="<?php echo $row_colors[$i]; ?>">				
					<td width="99%" valign="top" nowrap>
						<?php
						$display->downloadButton($child);
						echo "&nbsp;";
						$display->playButton($child,false,false);
						echo "&nbsp;";
						if ($jzUSER->getSetting('stream')){
							$display->link($child);
						} else {
							echo $child->getName();
						}
						echo " (". convertSecMins($metaData['length']). ")";
						?>
					</td>
				</tr>
				<?php
				$i = 1 - $i;
			}
			// Now let's set a field with the number of checkboxes that were here
			echo "</table>";
		}
		
		function nodeTable($nodes){
			global $media_dir, $jinzora_skin, $hierarchy, $album_name_truncate, $row_colors, 
			$img_more, $img_email, $img_rate, $img_discuss, $num_other_albums, $show_images, 
			$sort_by_year, $show_descriptions, $item_truncate;					

			if (sizeof($nodes) == 0) return;
			// Let's setup the new display object
			$display = &new jzDisplay();
			
			$album = false;
			if ($nodes[0]->getPType() == "album"){
				$album = true;
			}
			
			if ($sort_by_year == "true" and $album){
				sortElements($nodes,"year");
			} else {
				sortElements($nodes,"name");
			}
			
			if ($item_truncate == ""){
				$item_truncate = "25";
			}
			
			// Now let's setup the big table to display everything
			$i=0;
			?>
			<table class="jz_track_table" width="100%" cellpadding="3" cellspacing="0" border="0">
			<?php
			foreach ($nodes as $child) {
				$year = $child->getYear();
				$dispYear = "";
				if ($year <> "-" and $year <> "" and $album == true){
					$dispYear = " (". $year. ")";
				}
				?>
				<tr class="<?php echo $row_colors[$i]; ?>">
					<td nowrap valign="top" colspan="2">
					<?php 
						$display->playButton($child,false,false);
						echo "&nbsp;";
						$display->randomPlayButton($child,false,false);
						echo "&nbsp;";
						$name = $display->returnShortName($child->getName(),$item_truncate);
						$display->link($child, $name);
						echo $dispYear;
					?>
					</td>
				</tr>
				<?php
					// Let's see if we need the next row
					$art = $child->getMainArt("75x75");
					$desc = $display->returnShortName($child->getDescription(),200);
					if (($art <> "" or $desc <> "") and $show_images == "true"){
						?>
						<tr class="<?php echo $row_colors[$i]; ?>" nowrap>
							<td valign="top">
							<?php
								if ($show_images == "true" && (($art = $child->getMainArt("40x40")) !== false)) {
									$display->link($child,$display->returnImage($art,$child->getName(),40,40,"limit",false,false));
								}
							?>
							</td>
							<td valign="top" >
								<?php
									if ($desc <> "" and $show_descriptions == "true"){
										echo '<span class="jz_artistDesc">'. $desc. '</span>';
									}
								?>
							</td>
						</tr>
						<?php
					}
				?>
				<?php
				$i = 1 - $i; // cool trick ;)
			}
			echo "</table>";
		}
	}

	class jzFrontend extends jzFrontendClass {
		function jzFrontend() {
			parent::_constructor();
		}
		
		/**
		* Draws the login page.
		* 
		* @author Ben Dodson <bdodson@seas.upenn.edu>
		* @version 11/3/04
		* @since 5/13/04
		*/
		function loginPage($failed = false) {
		
			$display = &new jzDisplay();
			//$display->preHeader('Login',$this->width,$this->align);
			
			echo '<body onLoad="document.getElementById(\'loginform\').field1.focus();"></body>';
			
			$urla = array();			
			$urla['jz_path'] = isset($_GET['jz_path']) ? $_GET['jz_path'] : '';
			?>
				<style>
					body {
						background-color: #F5F5D0;
						background: #F5F5D0;
						font-family: Verdana, Sans;
						font-size: 10px;
						color: #9c9b9b;
						margin: 0 0 0 0;
					}
					td {
						font-family: Verdana, Sans;
						font-size: 10px;
					}
					submit {
						border: 1px solid black;
						background: #EFEFCC;
						color: #000000;
						font-size: 11px;
						border-width: 1px;
					}
					input {
						font-family: Verdana, Sans;
						color: #000000;
						background-color: #EFEFCC;
						font-size: 11px;
						border-width: 1px;
					}
				</style>
				    <script language="javascript" src="lib/md5.js"></script>
				    <script language="javascript">
				    function submitLogin() {
				      if (document.getElementById("loginform").doregister.value == 'true') {
					return true;
				      } else {
					// submit the other form
					// so we can submit a non-cleartext PW without changing browser's stored PW.
					document.getElementById("loginSecureForm").field1.value = 
					         document.getElementById("loginform").field1.value;

					document.getElementById("loginSecureForm").field2.value = 
					hex_md5(document.getElementById("loginform").field2.value);

					document.getElementById("loginSecureForm").remember.value =
					document.getElementById("loginform").remember.value;

					document.getElementById("loginSecureForm").submit();
					return false;
				      }
				    }
				    </script>
				<body style="background-color: #F5F5D0;">
				<table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #F5F5D0;">
					<tr>
						<td align="center" height="100%" width="100%" style="background-color: #F5F5D0;">
							<?php
								if ($failed) {
									echo "<center><strong><font color=white>Incorrect password</font></strong></center>";
								}
							?>
							<form name="loginSecureForm" id="loginSecureForm" method="POST" action="<?php echo urlize($urla); ?>">
								<input type="hidden" name="field1" value="">
								<input type="hidden" name="field2" value="">
	                            <input type="hidden" name="remember" value="">
								<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
                            </form>
							<form name="loginform" id="loginform" method="POST" action="<?php echo urlize($urla); ?>" onsubmit="return submitLogin()">
								<input type="hidden" name="<?php echo jz_encode('action'); ?>" value="<?php echo jz_encode('login'); ?>">
								<?php
									if (!$failed) {
										echo "<br><br><br>";
									}
								?>
								<?php echo word("Username"); ?><br>
								<input size="18" type="text" class="jz_input" name="field1" style="width:146px;">
								<br>
								<?php echo word("Password"); ?><br>
								<input size="18" type="password" class="jz_input" name="field2" style="width:146px;">
								<br>
								<input type="checkbox" class="jz_checkbox" name="remember"> <?php echo word("Remember me"); ?>
								<br><br>
								<input class="jz_submit" type="submit" name="<?php echo jz_encode('submit_login'); ?>" value="<?php echo word("Login"); ?>">
								   <input type="hidden" name="doregister" value="false" />
								<?php $be = new jzBackend();
									$data = $be->loadData('registration');
									if ($data['allow_registration'] == "true") {
									?>
										&nbsp;<input class="jz_submit" type="submit" name="<?php echo jz_encode('self_register'); ?>" value="<?php echo word("Register"); ?>" onclick="document.getElementById('loginform').doregister.value='true'">
									<?php 
									} 
									?>

							</form>
							<br /><br />
							<img src="style/images/login-footer-logo.gif" border="0">
							<br /><br />
						</td>
					</tr>
				</table>
				</body>
			<?php
			//this->footer();
		}
			
		function pageTop($title) {
			global $img_up_arrow, $row_colors;
			
			$display = new jzDisplay();
			
			if (isset($_GET['jz_path']) || isset($_POST['jz_path'])) {
				if (isset($_POST['jz_path'])){
					$bcArray = explode("/",$_POST['jz_path']);
					$me = new jzMediaNode($_POST['jz_path']);
				} else {
					$bcArray = explode("/",$_GET['jz_path']);
					$me = new jzMediaNode($_GET['jz_path']);
				}
			
				// Now we need to cut the last item off the list
				$bcArray = array_slice($bcArray,0,count($bcArray)-1);
				// Now let's display the crumbs
				$path = "";
				$arr = array();
				if (isset($_GET['frame'])){
					$arr['frame'] = $_GET['frame'];
				}
				
				?>
				<table class="jz_track_table" width="100%" cellpadding="3">
					<tr class="<?php echo $row_colors[1]; ?>">
						<td>
							<?php
								$link = urlize($arr);
								echo $img_up_arrow. "&nbsp;";
								jzHREF($link,"","","","Home");
								echo "&nbsp;";
								
								for ($i=0; $i < count($bcArray); $i++){
									echo $img_up_arrow. "&nbsp;";
									$path .= $bcArray[$i] ."/";
									$curPath = substr($path,0,strlen($path)-1);
									
									$arr = array();
									$arr['jz_path'] = $curPath;
									if (isset($_GET['frame'])){
										$arr['frame'] = $_GET['frame'];
									}
									
									$link = urlize($arr);
									jzHREF($link,"","","",$bcArray[$i]);
									echo "&nbsp;";
								}
								if (sizeof($bcArray) > 0) {
									echo "<br>";
								}
							?>
						</td>
					</tr>
				<?php
				
				if ($_GET['jz_path'] <> ""){
					?>
						<tr class="<?php echo $row_colors[1]; ?>">
							<td>
								<?php
									$display->playButton($me,false,false);
									echo "&nbsp;";
									$display->randomPlayButton($me,false,false);
									echo "&nbsp;";
									echo $title; 
								?>
							</td>
						</tr>
					<?php
				}
				echo '</table>';
				
				
			} else {
				echo $title;
			}
		}

		function footer() {
			global $root_dir, $jinzora_url, $jzSERVICES, $cms_mode, $jzUSER; 
			
			$display = new jzDisplay();		
			
			echo "<center>";
			
			if ($cms_mode == "false"){
				$display->loginLink();
			}
			if ($jzUSER->getSetting('edit_prefs') !== false) {
					if ($cms_mode == "false"){echo " | ";}
					$display->popupLink("preferences");
			}
			echo '<br><a href="'. $jinzora_url. '" target="_blank"><img src="'. $root_dir. '/style/images/slimzora.gif" border="0"></a><br><br>';
			echo '</td></tr></table>';
			
			$jzSERVICES->cmsClose();
		}
		
		function standardPage(&$node) {
			global $include_path;
			
			$blocks = &new jzBlocks();
			$display = &new jzDisplay();
			
			$display->preheader($node->getName(),$this->width,$this->align,true,true,true,true);
			include_once($include_path. "frontend/frontends/slimzora/css.php");
			$this->pageTop($node->getName());
			
			$nodes = $node->getSubNodes('nodes');
			$tracks = $node->getSubNodes('tracks');
			
			$blocks->trackTable($tracks, false, false);
			$blocks->nodeTable($nodes);
			
			$this->footer();
		}
	}
?>