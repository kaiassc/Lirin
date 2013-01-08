<?php

class Hero extends BSUnit {
	
	
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
		$targets = BattleSystem::getBSUnits();
		foreach($targets as $bsunit){
			if($bsunit->BSid !== $this->BSid){
				$this->TargetIDs[] = $bsunit->Index;
			}
		}
		
	}
	
	private function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	/////
	//CONDITIONS
	//
	
	
	
	/////
	//ACTIONS
	//
	
	
	
		
}

