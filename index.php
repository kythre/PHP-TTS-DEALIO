<?php
if (isset($_GET['q'])) {
	$file='cache/'.strtolower(urlencode($_GET['q'])).'.wav';
	//check if file has been created
	if(!file_exists($file)){
		//check if file has been reqeusted
		if(!strpos(file_get_contents("requests.txt"),$_GET['q'])) { 
			//check if virtual screen is running
			if (!strpos(shell_exec("ps -ef|grep Xvfb"), 'screen')) {
				//start virtual screen
				shell_exec ("Xvfb :0 -screen 0 1024x768x16 &");
			}
			//log the request
			$requests = 'requests.txt';
			$handle = fopen($requests, 'a') or die('Cannot open file:  '.$requests);
			$data = "\n".$_GET['q'];
			fwrite($handle, $data);
			//request file creation
			shell_exec ("cd dectalk; DISPLAY=:0.0 wine say.exe -w ../".$file." ".$_GET['q']); 
		}
	}

	//wait until file is created
	while(!file_exists($file)){
		usleep(500);
	}

	if(file_exists($file)){		
		header('Content-type: audio/wav');
		header('Content-length: ' . filesize($file));
		header('Content-Disposition: filename="dectalk.mp3"');
		header('X-Pad: avoid browser bug');
		header('Cache-Control: no-cache');
		readfile($file);
	}
}
?>
