<?php

class Boss extends BSUnit {
	
	
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player = P8, $location=NULL){
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
	}
	
	protected function getTargets(){
		return array_merge(BattleSystem::getHeroes(), BattleSystem::getRoamers());
	}
	
	/////
	//CONDITIONS
	//
	
	
	
	/////
	//ACTIONS
	//
	
	
	
		
}

