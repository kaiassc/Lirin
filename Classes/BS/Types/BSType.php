<?php

class BSType {
	
	public $ID;
	public $BaseUnit;
	public $ApparentUnit;
	
	protected static $NextID = 2;
	
	function __construct($statsarray){
		$this->ID = static::$NextID;
		static::$NextID++;
		
		ValidUnitCheck($statsarray["baseunit"]);
		
		$this->BaseUnit = $statsarray["baseunit"];
		
		if(array_key_exists("apparentunit", $statsarray)){
			ValidUnitCheck($statsarray["apparentunit"]);
			$this->ApparentUnit = $statsarray["apparentunit"];
		}else {
			$this->ApparentUnit = $statsarray["baseunit"];
		}
	}
	
}


