<?php

class BSUnit extends IndexedUnit {
	
	/* @var int */          public $BSid;
	/* @var String */       public $dcplayer;
	
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
	

	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		$this->BSid = $BSid;

		$index = $BSid;
		parent::__construct($index, $unit, $player, $location);
		
		$this->dcplayer = GetPlayerShorthand($dcplayer);
		
	}
	
	protected function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	private function getGroupIDsContainingTarget(){
		static $array = array();
		if( empty($array) ){
			$index = 0;
			$targets = $this->getTargets();
			foreach(BattleSystem::$dcgroups as $group){
				foreach($group as $bsunit){
					if(in_array($bsunit,$targets)){
						$array[] = $index;
						break;
					}
				}
				$index++;
			}
		}
		return $array;
	}
	
	/////
	//CONDITIONS
	//
	
	public function swings(){
		$reserve = new TempSwitch();
		$switch = new TempSwitch();
		
		$text = _if( $this->attackCooldown(AtLeast, 1) )->then(
			$this->attackTime->add(1),
			
			// and just started swinging
			_if( $this->attackTime->exactly(1) )->then( 
				$this->findTarget($switch),
			''),
			
			// and didn't just start swinging
			_if( $this->attackTime->atLeast(2) )->then( 
				$this->verifyTarget($switch),
			''),
			
			_if( $switch->is_clear() )->then( 
				$this->attackTime->add(100), 
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
		
		return CreateCondition($reserve, $switch, $text);
	}
	
	
	/////
	//ACTIONS
	//
	
	public function findTarget(TempSwitch $success){
		$text = $success->clear();
		
		// for each BSunit
		foreach($this->getTargets() as $bsunit){
			// excluding itself
			if($bsunit->BSid !== $this->BSid){
				// if targetted, set the attackTarget to its ID
				$text .= _if( $this->isTargeting($bsunit->Index) )->then(
					$this->attackTarget->setTo($bsunit->BSid),
					$success->set(),
				'');
			}
		}
		$text .= _if( $success->is_clear() )->then(
			$this->attackTarget->setTo(1700),
			$success->set(),
		'');
		
		return $text;
	}
	
	private function verifyTarget(TempSwitch $success){
		$text = $success->clear();
		
		// for each BSunit
		foreach($this->getTargets() as $bsunit){
			// excluding itself
			if($bsunit->BSid !== $this->BSid){
				// if same target as before, then success!
				$text .= _if( $this->isTargeting($bsunit->Index), $this->attackTarget->exactly($bsunit->BSid) )->then(
					$success->set(),
				'');
			}
		}
		
		return $text;
	}
		
	public function dealDamageToTarget(){
		
		$dcgroupid = new TempDC(7);
		$tempdc = new TempDC(127);
		
		$text = repeat(1,
			
			// set ally
			BattleSystem::setAllyByTarget($this->attackTarget, $dcgroupid),
			
			// load target's armor
			$this->loadArmor($tempdc, $dcgroupid),
			
			// armor calculation
			BattleSystem::convertToVulnerability($tempdc),
			$tempdc->multiplyBy($this->damage),
			$tempdc->max(12700),
			_if( $tempdc->atMost(50) )->then( $tempdc->setTo(50) ),
			
			// deal damage
			$this->dealDamage($tempdc, $dcgroupid),
			
			// restore
			SetAlly(AllPlayers),
			$tempdc->release(),
			$dcgroupid->release(),
			$this->attackTarget->setTo(0),
			
		'');
		
		return $text;
	}
	
	private function dealDamage(Deathcounter $damagex100, Deathcounter $groupid){
		$text = '';
		$GroupIDs = $this->getGroupIDsContainingTarget();
		foreach($GroupIDs as $id){
			$text .= _if( $groupid->exactly($id) )->then( 
				BattleSystem::$healthDCs[$id]->Allies->subDivBecome($damagex100, 100),
			'');
		}
		
		return $text;
	}
	
	private function loadArmor(Deathcounter $receivingdc, Deathcounter $groupid){
		$text = '';
		$GroupIDs = $this->getGroupIDsContainingTarget();
		foreach($GroupIDs as $id){
			$text .= _if( $groupid->exactly($id) )->then( 
				$receivingdc->setTo(BattleSystem::$armorDCs[$id]->Allies),
			'');
		}
		
		return $text;
	}
	
	public function kill(){
		
	}
	
	
		
}

