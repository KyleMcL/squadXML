<?php
	//in-game name
	$ingameName = strip_tags((string)$_POST['ingame_name']);
	str_replace("\r", "", (string)$ingameName);
	str_replace("\n", "", (string)$ingameName);
	$ingameName = htmlentities($ingameName);

	//player ID
	$playerID = strip_tags($_POST['player_id']);
	if (!settype($_POST['player_id'], "int")) {
		echo "invalid player ID";
		exit();
	}

	//'real' name
	$name = strip_tags((string)$_POST['name']);
	str_replace("\r", "", (string)$name);
	str_replace("\n", "", (string)$name);
	$name = htmlentities($name);

	//email
	$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
	if ($email === false) {
		unset($email);
	}

	//remark
	$remark = strip_tags((string)$_POST['remark']);
	str_replace("\r", "", (string)$remark);
	str_replace("\n", "", (string)$remark);
	$remark = htmlentities($remark);

	//chosen squad xml file
	$squadXMLFile = strip_tags((string)$_POST['logo']);
	str_replace("\\", "/", $squadXMLFile); //in case someone tries some trickery fuckery magic
	if (count(explode("/", $squadXMLFile)) !== 2 || !file_exists($_SERVER['DOCUMENT_ROOT'] ."/$squadXMLFile.xml")) {
		echo "squad XML for selected logo no longer exists, please select another";
		exit();
	}

	//load the squad XML and check if playerID already exists
	$squadXML = new DOMDocument();
	$squadXML->formatOutput = true;
	$squadXML->load($_SERVER['DOCUMENT_ROOT'] ."/$squadXMLFile.xml");
	$existingMembers = $squadXML->getElementsByTagName("member");
	for($i = 0; $i < ($existingMembers->length); $i++) {
		if ($existingMembers->item($i)->attributes->getNamedItem("id")->nodeValue == $playerID) {
			echo "specified player ID already exists";
			exit();
		}
	}

	//fill member block out
	$memberBlock = $squadXML->createElement("member");
	$memberBlock->setAttribute("id", $playerID);
	$memberBlock->setAttribute("nick", $nick);
	$memberBlock->setIdAttribute("id", true);

	//these children are optional to fill in, but must be present, at least as blank tags
	$memberBlock->appendChild(new DOMElement("name", $name));
	$memberBlock->appendChild(new DOMElement("email", $email));
	$memberBlock->appendChild(new DOMElement("icq", "N/A")); //fuck icq, who uses that any more?
	$memberBlock->appendChild(new DOMElement("remark", $remark));

	//add member to squad XML
	appendMemberInfo($memberBlock, $playerID, $ingameName, $name, $email, $remark);
	$squadXML->getElementsByTagName("squad")->item(0)->appendChild($memberBlock);
	if ($squadXML->save($_SERVER['DOCUMENT_ROOT'] ."/$squadXMLFile.xml") !== false) {
		echo "you were successfully added";
	} else {
		echo htmlentities("an error occurred appending the new <member> block");
		exit();
	}