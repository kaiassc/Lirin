<?php

class Roamer extends BSUnit {
	
	
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player = P7, $location=NULL){
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
	}
	
	protected function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	public function getTypes(){
		return Type::getRoamerTypes();
	}
	
	protected function enumerateType(){
		$enumarray = array();
		foreach($this->getTypes() as $type){
			$enumarray[$type->ID] = $type->Name; 
		}
		$this->type->enumerate($enumarray);
	}
	
	/////
	//CONDITIONS
	//
	
	
	
	/////
	//ACTIONS
	//
	
	protected function loadType($type){
		if( !($type instanceof RoamerType) ){
			Error("\$type must be a RoamerType...");
		}
		$text  = '';
		$text .= $this->type            ->setTo($type->ID);
		$text .= $this->baseunitid      ->setTo((int)GetUnitID($type->BaseUnit));
		$text .= $this->apparentunitid  ->setTo((int)GetUnitID($type->ApparentUnit));
		$text .= $this->damage          ->setTo($type->Damage);
		$text .= $this->health          ->setTo($type->Health);
		$text .= $this->maxhealth       ->setTo($type->Health);
		$text .= $this->mana            ->setTo($type->Mana);
		$text .= $this->maxmana         ->setTo($type->Mana);
		$text .= $this->armor           ->setTo($type->Armor);
		$text .= $this->magicresist     ->setTo($type->MagicResist);
		
		return $text;
	}
	
	function display(){
		$text = '';
		
		foreach($this->getTypes() as $type){
			$text .= _if($this->type->exactly($type->ID))->then(
				Display("\t\t\t\t\\x004$type->Name"),
				Display("\t\t\t\t\t\\x01EID: $this->BSid"),
				Display(" "),
				Display("\t\t\t\t\t\\x01Ehealth: \t$type->Health"),
				Display("\t\t\t\t\t\\x01Edmg:    \t$type->Damage"),
				Display("\t\t\t\t\t\\x01Earmr:   \t$type->Armor"),
				Display("\t\t\t\t\t\\x01Emr:     \t$type->MagicResist"),
			'');
		}
		return $text;
	}
	
		
}

