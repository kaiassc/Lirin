<?php

class BSUnit extends IndexedUnit {
	
	
	
	// Private
	
	/**#@+
	 * @var Deathcounter
	 */
	public $type;
	public $attackTime;
	public $attackTarget;
	public $health;
	public $maxhealth;
	public $mana;
	public $damage;
	public $armor;
	/**#@-*/
	
	public function __construct($index, $unit=NULL, $player=NULL, $location=NULL){
		parent::__construct($index, $unit, $player, $location);
		
		$this->type->add(1);
		$this->attackTime->add(1);
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

