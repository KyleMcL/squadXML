<?php
	include("squadXMLMember.php");

	//in-game name
	$ingameName = strip_tags((string)$_POST['ingame_name']);
	$ingameName = htmlentities($ingameName, ENT_XML1);

	//player ID
	$playerID = strip_tags($_POST['player_id']);
	if (!settype($_POST['player_id'], "int")) {
		echo "invalid player ID";
		exit();
	}

	//'real' name
	$name = strip_tags((string)$_POST['name']);
	$name = htmlentities($name, ENT_XML1);

	//email
	$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	if ($email === false) {
		unset($email);
	}

	//remark
	$remark = strip_tags((string)$_POST['remark']);
	str_replace("\r", "", (string)$remark);
	str_replace("\n", "", (string)$remark);
	$remark = htmlentities($remark, ENT_XML1);

	//chosen squad xml file
	$squadXMLFile = strip_tags((string)$_POST['logo']);
	str_replace("\\", "/", $squadXMLFile); //in case someone tries some trickery fuckery magic
	if (!file_exists($_SERVER['DOCUMENT_ROOT'] ."/$squadXMLFile.xml") || count(explode("/", $squadXMLFile)) !== 2) {
		echo "squad XML for selected logo no longer exists, please select another";
		exit();
	}

	//instantiate the new member as an XML element
	$newMember = new squadXMLMember($playerID, $ingameName, $name, $email, $remark);

	//add it to the squad.xml
	$squadXML = new DOMDocument();
	$squadXML->load("squad.xml");
	$squadXML->getElementsByTagName("squad")->item(0)->appendChild($newMember);
	if ($squadXML->validate()) {
		$squadXML->save("squad.xml");
	} else {
		echo htmlentities("an error occurred appending the new <member> block");
		exit();
	}