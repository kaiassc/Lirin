<?php

class Roamer extends BSUnit {
	
	
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player = P7, $location=NULL){
		
		$y = 20*32; $x = ($BSid*2+33)*32;
		$this->Index = UnitManager::MintUnitWithAnyIndex("Protoss High Templar", P7, $x, $y);
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
	}
	
	protected function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	/////
	//CONDITIONS
	//
	
	
	
	/////
	//ACTIONS
	//
	
	
	
		
}

