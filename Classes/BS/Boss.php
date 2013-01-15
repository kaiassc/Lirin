<?php

class Boss extends BSUnit {
	
	private $xspawn;
	private $yspawn;
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		
		$y = 20*32; $x = ($BSid*2+33)*32;
		$this->Index = UnitManager::MintUnitWithAnyIndex("Zerg Ultralisk", P8, $x, $y);
		
		$this->xspawn = $x;
		$this->yspawn = $y;
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
		
	}
	
	protected function getTargets(){
		return array_merge(BattleSystem::getHeroes(), BattleSystem::getRoamers());
	}
	
	function deathTrig(){
		$P1 = new Player(1);
		
		$success = new TempSwitch();
		$P1->_if( $this->health->exactly(0) )->then_justonce(
			Grid::putMainRes($this->xspawn, $this->yspawn, $success),
			KillUnitAtLocation(P8, "Zerg Ultralisk", 1, Grid::$main),
			$success->release(),
		'');
	}
	
	/////
	//CONDITIONS
	//
	
	
	
	/////
	//ACTIONS
	//
	
	
	
		
}

