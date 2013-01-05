<?php

class Hero extends BSUnit {
	
	
	
	// Private
	public $x;
	public $y;
	
	public function __construct($index, $unit=NULL, $player=NULL, $location=NULL){
		parent::__construct($index, $unit, $player, $location);
		
		// Let the IDE know what type of variable these are
		$this->maxhealth =  new TempDC();   $this->maxhealth    ->release();
		$this->maxmana =    new TempDC();   $this->maxmana      ->release();
		$this->x =          new TempDC();   $this->x            ->release();
		$this->y =          new TempDC();   $this->y            ->release();
	}
	
	
	/////
	//CONDITIONS
	//
	
	public function attacks(){
		
	}
	
	
	/////
	//ACTIONS
	//
	
	
	
		
}

