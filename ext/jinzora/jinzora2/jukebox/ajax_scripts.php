<?php if (!defined(JZ_SECURE_ACCESS)) die ('Security breach detected.'); ?>

<script>
function setJbFormCommand(cmd) {
	document.getElementById('jbPlaylistForm').elements['command'].value = cmd;
}

function setPlayback(obj) {
  playback = obj.options[obj.selectedIndex].value;
  if (playback != "stream") {
    playback = "jukebox";
  } else {
    playback = streamto;
  }
}

function jukeboxUpdater() {
  updateJukebox(false);
  setTimeout('jukeboxUpdater()',10*1000);
}

function updateJukebox(direct_call) {
  obj = document.getElementById("jukeboxSelect");
  if (obj != false) {
    setPlayback(obj);
    x_ajaxJukebox(obj.options[obj.selectedIndex].value, direct_call, updateJukebox_cb);
  } else {
    x_ajaxJukebox(false, direct_call, updateJukebox_cb);
  }
}

function updateJukebox_cb(a) {
  if (a != "") {
    document.getElementById("jukebox").innerHTML = a;
    NextTicker_start();
    CurTicker_start();	
    displayCountdown();
  }
}

function updateSmallJukebox() {
  obj = document.getElementById("smallJukeboxSelect");  
  if (obj != false) {
    setPlayback(obj);
    x_ajaxSmallJukebox(obj.options[obj.selectedIndex].value, sm_text, sm_buttons, sm_linebreaks, updateSmallJukebox_cb);
  }
}

function updateSmallJukebox_cb(a) {
  document.getElementById("smallJukebox").innerHTML = a;
}

function sendJukeboxRequest(cmd) {
    x_ajaxJukeboxRequest(cmd, sendJukeboxRequest_cb);
}

function sendJukeboxVol() {
  obj = document.getElementById("jukeboxVolumeSelect");
  if (obj != false) {
    x_ajaxJukeboxRequest('volume',obj.options[obj.selectedIndex].value,sendJukeboxRequest_cb);
  }
}

function sendJukeboxAddType() {
  obj = document.getElementById("jukeboxAddTypeSelect");
  if (obj != false) {
    x_ajaxJukeboxRequest('addwhere',obj.options[obj.selectedIndex].value,sendJukeboxRequest_cb);
  }
}

function sendJukeboxForm() {
  obj = document.getElementById("jukeboxJumpToSelect");
  if (obj != false) {
  	selectedItems = new Array();
  	total = 0;
  	for (i = 0; i < obj.length; i++) {
  		if (obj.options[i].selected) {
  			selectedItems[total] = obj.options[i].index;
  			total++;
  		}
  	}
    if (total == 0) { return false; }

    cmd = document.getElementById('jbPlaylistForm').elements['command'].value;

    // don't worry about a serverside update for these:
    if (cmd == "moveup" || cmd == "movedown" || cmd == "delone") {
      cb_func = nothing;
    } else {
      cb_func = sendJukeboxRequest_cb;
    }

    x_ajaxJukeboxRequest(cmd, selectedItems,cb_func);
    
    // same logic as in the server-side jukebox code.
    // the sync is a little funny.
    if (cmd == "moveup") {
      i = 0;
      while (i < total && selectedItems[i] == i) {
        i++;
      }
      while (i < total) {
        swap = obj.options[selectedItems[i]-1].text;
        swapFontWeight = obj.options[selectedItems[i]-1].style.fontWeight;

        obj.options[selectedItems[i]-1].selected = true;
        obj.options[selectedItems[i]-1].text = obj.options[selectedItems[i]].text;
        obj.options[selectedItems[i]-1].style.fontWeight = obj.options[selectedItems[i]].style.fontWeight;

        obj.options[selectedItems[i]].selected = false;
        obj.options[selectedItems[i]].text = swap;
        obj.options[selectedItems[i]].style.fontWeight = swapFontWeight;


	i++;
      }
    } else if (cmd == "movedown") {
      i = total-1;
      j = obj.options.length-1;
      while (i >= 0 && selectedItems[i] == j) {
        i--; j--;
      }
      while (i >= 0) {
        swap = obj.options[selectedItems[i]+1].text;
        swapFontWeight = obj.options[selectedItems[i]+1].style.fontWeight;

        obj.options[selectedItems[i]+1].selected = true;
        obj.options[selectedItems[i]+1].text = obj.options[selectedItems[i]].text;
        obj.options[selectedItems[i]+1].style.fontWeight = obj.options[selectedItems[i]].style.fontWeight;

        obj.options[selectedItems[i]].selected = false;
        obj.options[selectedItems[i]].text = swap;
        obj.options[selectedItems[i]].style.fontWeight = swapFontWeight;

	i--;
      }
    } else if (cmd == "delone") {
      for (i = obj.options.length-1; i >= 0; i--) {
        if (selectedItems[selectedItems.length-1] == i) {
          selectedItems.pop();
          obj.remove(i);
        }
      } 
    }
  }
}

function sendJukeboxRequest_cb(a) {
  // Update everything!
  // 2 ways: update all the elements
  // or just refresh the jukebox.
  // The entire jukebox is actually more responsive, so let's do that:
  // TODO: Update the small jukebox. How should we handle the parameters?
  // You can make some hidden fields in there to embed the vars as form elements.
  // Then you can even remove them as parameters if you want.
  obj = document.getElementById("smallJukebox");
  if (obj) {
    // TODO: How to pass the actual 3 parameters here from above???
    updateSmallJukebox();
  }
  obj = document.getElementById("jukebox");
  if (obj) {
    updateJukebox(true);
  }
  return false;


  obj = document.getElementById("jukeboxNowPlaying");
  if (obj != false) {
    x_ajaxJukeboxNowPlaying(updateJukeboxNowPlaying_cb);
  }

  obj = document.getElementById("jukeboxNextTrack");
  if (obj != false) {
    x_ajaxJukeboxNextTrack(updateJukeboxNextTrack_cb);
  }

  // I didn't finish adding things, since updateJukebox() is snappier.

  return false;
}


function updateJukeboxNowPlaying_cb(a) {
  obj = document.getElementById("jukeboxNowPlaying");
  if (obj != false) {
    obj.innerHTML = a;
  }

  obj = document.getElementById("jukeboxNextTrack");
  if (obj != false) {
    obj.innerHTML = a;
  }
}

function updateJukeboxNextTrack_cb(a) {
  obj = document.getElementById("jukeboxNextTrack");
  if (obj != false) {
    obj.innerHTML = a;
  }
}
</script>