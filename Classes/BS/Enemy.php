<?php

class Enemy extends BSUnit {
	
	
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player = P8, $location=NULL){
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
	}
	
	protected function getTargets(){
		return array_merge(BattleSystem::getHeroes(), BattleSystem::getRoamers());
	}
	
	public function getTypes(){
		return Type::getEnemyTypes();
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
		if( !($type instanceof EnemyType) ){
			Error("\$type must be an EnemyType...");
		}
		$text = '';
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
	
	public function deathAnimation(){
		$text = '';
		
		$text .= _if($this->type->atLeast(2))->then(
			_if( $this->isType(Type::$GloreHulk) )->then(
				$this->Location->airPuff(),
			''),
			_if( $this->isType(Type::$Squirt) )->then(
				$this->Location->bloodsplat(),
			''),
			_if( $this->isType(Type::$Champion) )->then(
				$this->Location->bloodsplat(),
			''),
			_if( $this->isType(Type::$Scurrier) )->then(
				$this->Location->explode(),
			''),
		'');
		
		return $text;
	}
	
	function display(){
		$text = '';
		
		foreach($this->getTypes() as $type){
			$text .= _if($this->type->exactly($type->ID))->then(
				Display("\t\t\t\t\\x004$type->Name - $type->Codex"),
				Display("\t\t\t\t\t\\x01EID: $this->BSid"),
				Display(" "),
				Display("\t\t\t\t\t\\x01Ehealth: \t$this->health"),
				Display("\t\t\t\t\t\\x01Edmg:    \t$type->Damage"),
				Display("\t\t\t\t\t\\x01Earmr:   \t$type->Armor"),
				Display("\t\t\t\t\t\\x01Emr:     \t$type->MagicResist"),
			'');
		}
		return $text;
	}
		
}

