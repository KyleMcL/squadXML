<?php
class squadXMLMember extends DOMElement{
	function __construct($playerID, $nick, $name = NULL, $email = NULL, $remark = NULL) {
		parent::__construct("member");
		$this->setAttribute("id", $playerID);
		$this->setAttribute("nick", $nick);

		//these children are optional to fill in, but must be present, at least as blank tags
		$this->appendChild(new DOMElement("name", $name));
		$this->appendChild(new DOMElement("email", $email));
		$this->appendChild(new DOMElement("icq", NULL)); //fuck icq, who uses that any more?
		$this->appendChild(new DOMElement("remark", $remark));
	}
}