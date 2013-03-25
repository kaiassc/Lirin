<?php

class Hero extends BSUnit {
	
	/* @var String */ public $visplayer;
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		
		if($player === null){ $player = $dcplayer; }
		
		if($player === P4){ $this->visplayer = P1; }
		if($player === P5){ $this->visplayer = P2; }
		if($player === P6){ $this->visplayer = P3; }
		
		parent::__construct($dcplayer, $BSid, $unit, $player, $location);
	}
	
	protected function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	public function getTypes(){
		return Type::getHeroTypes();
	}
	
	/////
	//CONDITIONS
	//
	
	
	
	/////
	//ACTIONS
	//
	
	protected function clearType(){
		return  $this->type->setTo(0).
				$this->attackTime->setTo(0).
				$this->attackTarget->setTo(0).
				#$this->health->setTo(0).
				#$this->maxhealth->setTo(0).
				#$this->mana->setTo(0).
				#$this->maxmana->setTo(0).
				#$this->damage->setTo(0).
				#$this->armor->setTo(0).
				#$this->magicresist->setTo(0).
				'';
	}
	
	protected function loadType($type){
		if( !($type instanceof HeroType) ){
			Error("\$type must be a HeroType...");
		}
		$text = '';
		$text .= $this->type->setTo($type->ID);
		$text .= $this->baseunitid->setTo((int)GetUnitID($type->BaseUnit));
		$text .= $this->apparentunitid->setTo((int)GetUnitID($type->ApparentUnit));
		
		$text .= $this->health->setTo(100);
		$text .= $this->maxhealth->setTo(100);
		$text .= $this->mana->setTo(100);
		$text .= $this->maxmana->setTo(100);
		
		return $text;
	}
	
		
}

