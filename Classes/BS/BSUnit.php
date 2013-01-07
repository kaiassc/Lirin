<?php

class BSUnit extends IndexedUnit {
	
	
	/* @var Deathcounter */ public $type;
	/* @var Deathcounter */ public $attackTime;
	/* @var Deathcounter */ public $attackTarget;
	/* @var Deathcounter */ public $health;
	/* @var Deathcounter */ public $maxhealth;
	/* @var Deathcounter */ public $mana;
	/* @var Deathcounter */ public $damage;
	/* @var Deathcounter */ public $armor;
	
	/* @var Deathcounter */ public $x;
	/* @var Deathcounter */ public $y;
	
	/* @var int */      public $BSid;
	/* @var String */   public $dcplayer;
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		
		$index = 1;
		parent::__construct($index, $unit, $player, $location);
		
		$this->dcplayer = $dcplayer;
		
		$this->type->add(1);
		$this->attackTime->add(1);
	}
	
	
	/////
	//CONDITIONS
	//
	
	public function swings(){
		/**
		$P1->_if( $this->attackCooldown(AtLeast, 1) )->then(
			$this->attackTime->add(1),
			
			// and just started swinging
			_if( $this->attackTime->exactly(1) )->then( 
				$this->getSpecificTargetIDs($this->attackTarget, $IDs),
				$switch->set(),
			''),
			
			// and didn't just start swinging
			_if( $this->attackTime->atLeast(2) )->then( 
				$this->checkSpecificTargetIDs($this->attackTarget, $switch, $IDs),
			''),
			
			_if( $switch->is_clear() )->then( 
				$this->attackTarget->add(100), 
				$this->attackTarget->setTo(0),
			''),
			
			$switch->clear(),
			
			_if( $this->type->Exactly(0), $this->attackTime->exactly(2) )->then( 
				$switch->set(),
			''),
			
			_if( $this->type->Exactly(1), $this->attackTime->exactly(2) )->then( 
				$switch->set(),
			''),
			
			_if( $this->type->Exactly(2), $this->attackTime->exactly(3) )->then( 
				$switch->set(),
			''),
			
			_if( $this->attackCooldown(AtMost, 2) )->then( 
				$this->attackTime->setTo(0),
			''),
			
		'');
		/**/

		// temporary
		return $this->attackCooldown(AtLeast, 1);
	}
	
	
	/////
	//ACTIONS
	//
	
	public function findTarget(TempSwitch $success){
		$text = $success->clear();
		
		// for each BSunit
		foreach(BattleSystem::getBSUnits() as $bsunit){
			// excluding itself
			if($bsunit->BSid !== $this->BSid){
				// if targetted, set the attackTarget to its ID
				$text .= _if( $this->isTargeting($bsunit->Index) )->then(
					$this->attackTarget = $bsunit->BSid,
					$success->set(),
				'');
			}
		}
		
		return $text;
	}
	
	public function kill(){
		
	}
	
	
		
}

