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
	
	public function placeAt($x, $y){
		
		
		
		
		
		
	}
	
}