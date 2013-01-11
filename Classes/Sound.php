<?php

class Sound {
	
	/* @var SFXManager */ 
	protected static $Manager = null;
	
	private $Name;
	
	function __construct($name){
		if( !is_string($name) ){ Error("You need to pass in a string for the wav's name (e.g. 'loudbang')"); }
		if( !isset(static::$Manager) ){
			static::$Manager = SFXManager::getInstance();
		}
		
		$this->Name = $name;
		
		static::$Manager->registerWav($this->Name);
		
	}
	
	////
	// Actions
	//
	
	public function play(){
		static::$Manager->mintRegular($this->Name);
		return PlayWav($this->Name);
	}
	
	public function playFor($player){
		return static::$Manager->getPlayerCommand($this->Name, $player);
	}
	
	public function playAt($x, $y){
		if($x instanceof Deathcounter){
			if($x->Player === CP || $x->Player === Allies || $x->Player === Foes){
				Error('The deathcounter you\'re using for the $x parameter can\'t be for "Current Player", "Allies", or "Foes". This is because the logic for this command may take place in another player\'s triggers');
			}
			if($x instanceof TempDC){
				Error('$x parameter cannot be a TempDC');
			}
		}
		if($y instanceof Deathcounter){
			if($y->Player === CP || $y->Player === Allies || $y->Player === Foes){
				Error('The deathcounter you\'re using for the $y parameter can\'t be for "Current Player", "Allies", or "Foes". This is because the logic for this command may take place in another player\'s triggers');
			}
			if($y instanceof TempDC){
				Error('$x parameter cannot be a TempDC');
			}
		}
		return static::$Manager->getPlayAtCommand($this->Name, $x, $y);
	}
	
	
}