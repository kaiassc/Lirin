<?php

class BSUnit extends IndexedUnit {
	
	
	
	// Private
	
	/* @var Deathcounter */ public $type;
	/* @var Deathcounter */ public $attackTime;
	/* @var Deathcounter */ public $attackTarget;
	/* @var Deathcounter */ public $health;
	/* @var Deathcounter */ public $maxhealth;
	/* @var Deathcounter */ public $mana;
	/* @var Deathcounter */ public $damage;
	/* @var Deathcounter */ public $armor;
	
	public function __construct($index, $unit=NULL, $player=NULL, $location=NULL){
		parent::__construct($index, $unit, $player, $location);
		
		
	}
	
	
	/////
	//CONDITIONS
	//
	
	public function attacks(){
		
	}
	
	
	/////
	//ACTIONS
	//
	
	public function kill(){
		
	}
	
	
		
}

