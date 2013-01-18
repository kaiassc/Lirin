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
	
	/* @var LirinLocation */ public $Loc;
	
	/* @var Deathcounter */ private $replacedc;
	

	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL, $location=NULL){
		parent::__construct($this->Index, $unit, $player, $location);
		
		$this->BSid = $BSid;
		$this->dcplayer = GetPlayerShorthand($dcplayer);
		$this->replacedc = new Deathcounter(100);
		$this->Loc = LocationManager::MintLocation("bsunitloc$BSid", 0, 0, 0, 0);
			
	}
	
	protected function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	private function getGroupIDsContainingTarget(){
		$array = array();
		$index = 0;
		$targets = $this->getTargets();
		foreach(BattleSystem::$dcgroups as $group){
			$success = false;
			foreach($group as $bsunit){
				foreach($targets as $target){
					if($target->BSid === $bsunit->BSid){
						$success = true;
						break;
					}
				}
				if( $success === true ){
					break;
				}
			}
			if( $success === true ){
				$array[] = $index;
			}
			$index++;
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
			// count how long you've been attacking
			$this->attackTime->add(1),
			
			// if first loop, record your target
			_if( $this->attackTime->exactly(1) )->then( 
				$this->findTarget($switch),
			''),
			
			// if isn't first loop, verify that the target is the same as before
			_if( $this->attackTime->atLeast(2) )->then( 
				$this->verifyTarget($switch),
				
				// if verification fails then make attack time too high
				_if( $switch->is_clear() )->then( 
					$this->attackTime->add(100), 
					$this->attackTarget->setTo(0),
				''),
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
			
		'');
		
		$text .= _if( $this->attackCooldown(AtMost, 2) )->then( 
			$this->attackTime->setTo(0),
		'');
		
		return CreateCondition($reserve, $switch, $text);
	}
	
	public function dies(){
		
		return '';
	}
	
	/////
	//ACTIONS
	//
	
	private function findTarget(TempSwitch $success){
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
			$this->attackTarget->setTo(0),
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
		
		$P4 = new Player(P4);
		
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
		
		return '';
	}
	
	public function remove(){
		
		return '';
	}
	
	public function spawn(){
		
		return '';
	}
	
	public function deathAnimation(){
		
		return '';
	}
	
		
}

