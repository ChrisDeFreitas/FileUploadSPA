<?php
/*
	index1.php
	- handle file uploads
	- sample code from: https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/Using_XMLHttpRequest

	application states:
	0: display html form
	1: handle file upload

*/

//state 1:
if(count($_FILES) > 0){
	$uploaddir = './files/';
	$uploadfile = $uploaddir . basename($_FILES['file1']['name']);

	$result = array();
	$result['ver'] = '3.0';
	$result['files'] = $_FILES;

	if(move_uploaded_file($_FILES['file1']['tmp_name'], $uploadfile)) {
			$result['msg'] = "Upload succeeded";
	}
	else {
		$result['msg'] = "Upload failed";
	}

	echo json_encode($result);
	return;
}

//state 0 below here
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Uploads</title>

<link href="/fonts/carlito.css" rel="stylesheet" media="screen" type="text/css"/>
<link href="dialog-polyfill.css" rel="stylesheet" media="screen" type="text/css"/>
<script src="https://code.jquery.com/jquery-3.5.1.slim.js"
			  integrity="sha256-DrT5NfxfbHvMHux31Lkhxg42LY6of8TaYyK50jnxRnM="
			  crossorigin="anonymous"></script>

<script>
	"use strict";
	
	var sp3 = null, logo=null
	window.onload = function(){
		sp3 = document.getElementById('sp3')
		logo = document.getElementById('Logo')
	}

	function ajaxSuccess () {
		logo.className = ''
		
		console.log(this.responseText)
		let obj = JSON.parse(this.responseText)
		console.log(obj)
		let files = ''
		for(let key in obj.files){
			let file = obj.files[key]
			files += file.name+'<br>'
		}
		sp3.innerHTML = obj.msg+': '+files
	}
	function updateProgress(oEvent) {
		if (oEvent.lengthComputable) {
  	  var percentComplete = oEvent.loaded / oEvent.total * 100;
			sp3.innerHTML = 'Upload completed: '+Math.round(percentComplete)+'%'
		} else {
			// Unable to compute progress information since the total size is unknown
			// that's ok
		}		
	}
	function transferFailed(evt) {
		sp3.innerHTML = "An error occurred while transferring the file."
	}
	function transferCanceled(evt) {
		sp3.innerHTML = "The transfer has been canceled by the user."
	}
	
	function AJAXSubmit (oFormElement) {
		if (!oFormElement.action) { return; }

		sp3.innerHTML = 'Wait, upload status will be displayed here'

		var oReq = new XMLHttpRequest();

		// oReq.onload = ajaxSuccess;
		oReq.addEventListener("loadstart", () => {
			logo.className = 'logoSpin'
		});
		oReq.addEventListener("load", ajaxSuccess);
		oReq.upload.addEventListener("progress", updateProgress);
		oReq.addEventListener("error", transferFailed);
		oReq.addEventListener("abort", transferCanceled);

		if (oFormElement.method.toLowerCase() === "post") {
			oReq.open("post", oFormElement.action);
			oReq.send(new FormData(oFormElement));
		} else {
			var oField, sFieldType, nFile, sSearch = "";
			for (var nItem = 0; nItem < oFormElement.elements.length; nItem++) {
				oField = oFormElement.elements[nItem];
				if (!oField.hasAttribute("name")) { continue; }
				sFieldType = oField.nodeName.toUpperCase() === "INPUT" ?
						oField.getAttribute("type").toUpperCase() : "TEXT";
				if (sFieldType === "FILE") {
					for (nFile = 0; nFile < oField.files.length;
							sSearch += "&" + escape(oField.name) + "=" + escape(oField.files[nFile++].name));
				} else if ((sFieldType !== "RADIO" && sFieldType !== "CHECKBOX") || oField.checked) {
					sSearch += "&" + escape(oField.name) + "=" + escape(oField.value);
				}
			}
			oReq.open("get", oFormElement.action.replace(/(?:\?.*)?$/, sSearch.replace(/^&/, "?")), true);
			oReq.send(null);
		}
	}
</script>

<style type="text/css">
	body {
		background-color: #fff;
		color: #4f4f4f;
		font-family: CarlitoRegular, Tahoma, Geneva, 'sans-serif';
		font-size: 12px;
		padding:2px;

		backface-visibility: visible;
		perspective: 1000px;
		perspective-origin: 50% 50%;
		transform-style: preserve-3d;
	}
	a{	color: #4f4f4f;	text-decoration:none; }
	a:hover{ color:#000;	text-shadow:2px 2px #d0d0e0; }

	#Logo{
		/*border:0;	float:left; margin:20px 0 0 30px; */
		position:absolute; top:50px; left:50px;
		width:100px;
		backface-visibility: visible;
		transform-origin: center;
		transform-style: preserve-3d;
	}
	@keyframes spin { 
		from{ transform: rotateY(0deg);	}
		to{ transform: rotateY(359deg); }
	}
	.logoSpin{ animation:spin 4s infinite linear; }

	#tbMainFrame{ background-color:transparent; margin:0 auto; padding:10px; vertical-align:top; width:100% }
	#tbMainFrame tr{ vertical-align:top}

	#tdHeader {font-size:1em; margin:0; text-align:center; width:100%}
	#tdHeader h1{ font-weight:normal; margin:30px 0 0 0; padding:0 }
	#tdHeader h2{font-weight:normal; margin:0; padding:0 }
	#tdMain{ text-align:center; vertical-align:top}

	input{font-size:0.7em }
</style>


</head>
<body>
<img id=Logo src='Logo3.svg' alt=logo>

<table id=tbMainFrame><tr>
<td id=tdHeader>
			<h1>File Upload</h1>
</td></tr>
<tr><td id=tdMain>
<!-- Start Page Content -->

<div id=divText>
	Please contact before using.<br>
</div>

<form action="index.php" method="POST" enctype="multipart/form-data"
	style='font-size:1.2em; margin:2em auto; text-align:left; width:25em;'
	onsubmit="AJAXSubmit(this); return false;"
	>

 <input type="hidden" name="UPLOAD_PROGRESS" value="123" />	<!-- name attrib set in php.ini -->
 	&nbsp; 1. Select file:
 		<input type="file" name="file1">
 		<br><br>
 	&nbsp; 2. Begin:
 		<input type="submit" value='Upload'>
 		<br><br>
 	&nbsp; 3. <span id=sp3>Wait, upload status will be displayed here</span>
</form>


<!-- End Page Content-->
</td></tr></table>

</body></html>

