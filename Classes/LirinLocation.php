<?php

class LirinLocation extends Location {
	
	// Properties
	public $Name;
	
	// Constructor
	public function __construct($name){
		parent::__construct($name);
		
	}
	
	// Custom
	public function explode($player = P8){
		return
		CreateUnitWithProperties($player,'Terran Wraith',1, $this, Cloaked).
		KillUnit($player, 'Terran Wraith');
	}
	
	public function largeExplode($player = P8){
		return
		CreateUnit($player,'Terran Battlecruiser',1, $this).
		KillUnit($player, 'Terran Battlecruiser');
	}
	
	public function blueExplode($player = P8){
		return
		CreateUnit($player,'Protoss Scout',1, $this).
		KillUnit($player, 'Protoss Scout');
	}
	
	public function largeBlueExplode($player = P8){
		return
		CreateUnit($player,'Protoss Carrier',1, $this).
		KillUnit($player, 'Protoss Carrier');
	}
	
	public function bloodsplat($player = P8){
		return
		CreateUnitWithProperties($player,'Devouring One (Zergling)',1, $this, Burrowed).
		KillUnit($player, 'Devouring One (Zergling)');
	}
	
	public function scourgesplat($player = P8){
		return
		CreateUnitWithProperties($player,'Zerg Scourge',1, $this, Invincible).
		KillUnit($player, 'Zerg Scourge');
	}
	
	public function airPuff($player = P8){
		return
		CreateUnitWithProperties($player,'Terran Wraith',1, $this, array(Cloaked, Hallucinated)).
		KillUnit($player, 'Terran Wraith');
	}
	
	public function placeAt($x, $y, TempSwitch $success = null){
		$text = '';
		if( $success !== null ){
			$text .= Grid::putMain($x, $y, $success);
		}
		else{
			$text .= Grid::putMain($x, $y);
		}
		$text .= Loc::$main->acquire($this);
		return $text;
	}
	
	public function placeAtRes($x, $y, TempSwitch $success = null){
		$text = '';
		if( $success !== null ){
			$text .= Grid::putMainRes($x, $y, $success);
		}
		else{
			$text .= Grid::putMainRes($x, $y);
		}
		$text .= Loc::$main->acquire($this);
		return $text;
	}
	
	
}