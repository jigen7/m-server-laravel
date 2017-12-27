<?php

class XmlParser {
	
	var $hasErrorFlag = false;
	var $hasResponseFlag = false;
	var $newPhotosFlag = false;
	var $currentTableName = "";
	var $currentColumnName = "";
	var $currentColumnValue = "";
	
	var $results;

	function parseStringToDataArray($xml) {
		$this->results = null;
		$this->hasErrorFlag = false;
		$this->hasResponseFlag = false;
		$this->newPhotosFlag = false;
		$this->currentTableName = "";
		$this->currentColumnName = "";
		$this->currentColumnValue = "";
		
		$parser = xml_parser_create();
		xml_set_object($parser, $this);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_element_handler($parser, "onElementStart", "onElementEnd");
		xml_set_character_data_handler($parser, "onElementData");
		
		if(!xml_parse($parser, $xml)) {
			trigger_error("XML data can not be parsed", E_USER_ERROR);
		}
		
		xml_parser_free($parser);
		return $this->results;
	}
	
	function onElementStart($parser, $elementName, $elementAttributes) {	
		if($this->hasErrorFlag) return;

		if(strcmp($elementName, "error") == 0) {
			$this->hasErrorFlag = true;
		}
		else if(strcmp($elementName, "response") == 0) {
			// instantiate a new array
			$this->hasResponseFlag = true;
			$this->results = array();
		}
		else if($this->hasResponseFlag) {
			if(strcmp($elementName, "data") == 0) {
		        // do nothing at the start of a new data
				$this->results[] = array();
		    }
			else if(strcmp($elementName, "Photos") == 0) {
				$this->newPhotosFlag = true;
				$this->results[count($this->results)-1]["Photos"] = array();
			}
		    else if(strcmp($this->currentTableName, "") == 0) {
		        $this->currentTableName = $elementName;
				if(!$this->newPhotosFlag)
					$this->results[count($this->results)-1][$this->currentTableName] = array();
		    }
		    else{
				$this->currentColumnName = $elementName;
				$this->currentColumnValue = "";
		    }	
		}
				
	}
	
	function onElementEnd($parser, $elementName) {
		if($this->hasErrorFlag || !$this->hasResponseFlag) {
			// do nothing
			return;
		}

		if(strcmp("response", $elementName) == 0) {
			$this->hasResponseFlag = false;
		}
		else if(strcmp($this->currentTableName, $elementName) == 0) {
			$this->currentTableName = "";
		}
		else if(strcmp($this->currentColumnName, $elementName) == 0) {
			if($this->newPhotosFlag) {
				$this->results[count($this->results)-1]["Photos"][$this->currentTableName][$this->currentColumnName] = $this->currentColumnValue;	
			}else{
				$this->results[count($this->results)-1][$this->currentTableName][$this->currentColumnName] = $this->currentColumnValue;	
			}
			$this->currentColumnName = "";
			$this->currentColumnValue = "";
	    }
		else if(strcmp($elementName, "Photos") == 0) {
			$this->newPhotosFlag = false;
		}
	}
	
	function onElementData($parser, $data) {
		if($this->hasErrorFlag || !$this->hasResponseFlag) {
			// do nothing
			return;
		}
		
		if(strcmp($this->currentColumnName, "") != 0) {
			$this->currentColumnValue = $data;
		}
	}
}

?>
