<?php

class Hero extends BSUnit {
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		
		$oplyr = P10;
		if($dcplayer === P5){
			$oplyr = P11;
		}
		if($dcplayer === P6){
			$oplyr = P12;
		}
		
		$y = 64*32; $x = ($BSid*2+33)*32;
		$this->Index = UnitManager::MintUnitWithAnyIndex("Protoss Zealot", $oplyr, $x, $y);
		
		$P = new Player($dcplayer);
		$P8 = new Player(P8);
		$P->justonce(
			Give($oplyr, "Protoss Zealot", 1, $dcplayer, Anywhere),
		'');
		$P8->justonce(
			RemoveUnitAtLocation($oplyr, "Protoss Zealot", 1, Anywhere),
		'');
		
		if($player === null){
			$player = $dcplayer;
		}
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
		$targets = BattleSystem::getBSUnits();
		foreach($targets as $bsunit){
			if($bsunit->BSid !== $this->BSid){
				$this->TargetIDs[] = $bsunit->Index;
			}
		}
		
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

