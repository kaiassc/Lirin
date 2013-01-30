<?php

class BSUnit extends IndexedUnit {
	
	/* @var int */          public $BSid;
	/* @var String */       public $dcplayer;
	
	/* @var Deathcounter */ public $attackTime;
	/* @var Deathcounter */ public $attackTarget;
	/* @var Deathcounter */ public $health;
	/* @var Deathcounter */ public $maxhealth;
	/* @var Deathcounter */ public $mana;
	/* @var Deathcounter */ public $maxmana;
	/* @var Deathcounter */ public $damage;
	/* @var Deathcounter */ public $armor;
	/* @var Deathcounter */ public $magicresist;
	
	/* @var Deathcounter */ public $x;
	/* @var Deathcounter */ public $y;
	
	
	/* @var PermSwitch */   public $enableScan;
	/* @var PermSwitch */   public $scanSwitch;
	
	/* @var LirinLocation */public  $Location;
	
	
	/** 
	 * 0 = no unit
	 * 1 = placeholded
	 * 2+ = various types
	 * @var Deathcounter 
	 */ 
	public $type;
	
	/**
	 * 0 = do nothing
	 * 1 = placehold
	 * 2+ = correspond to various types
	 * @var Deathcounter
	 */ 
	public $replacedc;
	
	/**
	 * corresponds the id of the current base unit
	 * @var Deathcounter
	 */ 
	public  $baseunitid;
	
	/**
	 * corresponds the id of the current apparent unit
	 * @var Deathcounter
	 */ 
	public  $apparentunitid;
	
	public function __construct($dcplayer, $BSid, $unit=NULL, $player=NULL){
		$location = LocationManager::MintLocation("BSLoc$BSid", 0, 0, 0, 0);
		
		$placeholder = UnitManager::$Placeholder;
		$this->Index = UnitManager::MintUnitWithAnyIndex($placeholder->Unit, $placeholder->Player, ($BSid*2+33.5)*32, 1.5*32);
		
		parent::__construct($this->Index, $unit, $player, $location);
		
		$this->BSid = $BSid;
		$this->dcplayer = GetPlayerShorthand($dcplayer);
		$this->replacedc = new Deathcounter(100);
		$this->scanSwitch = new PermSwitch();
		$this->enableScan = new PermSwitch();
		
	}
	
	protected function getTargets(){
		return BattleSystem::getBSUnits();
	}
	
	public function getTypes(){
		return Type::getAllTypes();
	}
	
	function getPossibleUnits(){
		$units = array();
		foreach($this->getTypes() as $type){
			if( !in_array($type["unit"], $units) ){
				$units[] = $type["unit"];
			}
		}
		
		return $units;
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
	
	public function isType(BSType $type){
		return $this->type->exactly($type->ID);
	}
	
	/////
	//ACTIONS
	//
	
	public function scanUnit(){
		//unit is moving
		$text = _if( $this->enableScan->is_set(), $this->orderCoordinate(AtLeast, 1), $this->attackCooldown(AtMost, 0) )->then(
		    $this->scanSwitch->set(),
		'');
		
		//clear scan
		$text .= _if( $this->scanSwitch->is_set(), $this->enableScan->is_clear() )->then(
		    $this->scanSwitch->clear(),
		'');
		
		//scan
		$text .= _if( $this->scanSwitch->is_set() )->then(
		    Grid::scan($this),
		'');
		
		//keep switch set 1 loop after it "stops"
		$text .= _if( $this->scanSwitch->is_set(), $this->orderCoordinate(Exactly, 0) )->then(
		    $this->scanSwitch->clear(),
		'');
		
		return $text;
	}
	
	public function showHealth(){
		$maxpower = getBinaryPower($this->maxhealth->Max);
	
		$text = '';
		$tempdc = new TempDC($this->maxhealth->Max);
		
		/* @var TempDC[] $nums */
		$nums = array();

		for($i=0; $i<20; $i++){
			$nums[$i] = new TempDC();
			$text .= $nums[$i]->setTo(1);
		}
		for($i=$maxpower; $i>=0; $i--){
			$k=pow(2,$i);
			$numstext = '';
			for($j=0; $j<20; $j++){
				$numstext .= $nums[$j]->add($k*($j+1));
			}
			$text .= _if( $this->maxhealth->atLeast($k) )->then(
				$this->maxhealth->subtract($k),
				$numstext,
				$tempdc->add($k),
			'');
		}
		
		$text .= $this->maxhealth->become($tempdc);
		$tempdc->Max = $this->health->Max;
		
		for($j=$maxpower; $j>=0; $j--){
			$k=pow(2,$j);
			$numstext = '';
			foreach($nums as $num){
				/* @var tempDC $num */
				$numstext .= $num->subtract($k*20);
			}
			$text .= _if( $this->health->atLeast($k) )->then(
				$this->health->subtract($k),
				$numstext,
				$tempdc->add($k),
			'');
		}
		
		$text .= $this->health->becomeDel($tempdc);
		
		$clearnums = '';
		foreach($nums as $num){
			/* @var tempDC $num */
			$clearnums .= $num->setTo(0);
		}
		//over max health
		$P4 = new Player(P4);
		
		$text .= _if( $nums[19]->atMost(0) )->then(
			$this->health->setTo($this->maxhealth),
			ModifyHealth($this->Player, Men, 1, $this->Location, 100),
			$clearnums,
			$P4->addGas(1),
		'');
		//bulk of healths
		for($i=0; $i<19; $i++){
			$text .= _if( $nums[$i]->atLeast(1) )->then(
				ModifyHealth($this->Player, Men, 1, $this->Location, $i*5+5),
				$clearnums,
			'');
		}
		//barely scratched
		$text .= _if( $nums[19]->atLeast(2) )->then(
			ModifyHealth($this->Player, Men, 1, $this->Location, 99),
			$clearnums,
		'');
		//perfectly healthy
		$text .= _if( $nums[19]->exactly(1) )->then(
			ModifyHealth($this->Player, Men, 1, $this->Location, 100),
			$clearnums,
		'');
		//kill unit
		$text .= _if( $this->maxhealth->atLeast(1), $this->health->atMost(0) )->then(
		    $this->kill(),
		'');
		
		
		foreach($nums as $num){
			/* @var tempDC $num */
			$num->kill();
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
	
	protected function findTarget(TempSwitch $success){
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
	
	protected function verifyTarget(TempSwitch $success){
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
	
	protected function dealDamage(Deathcounter $damagex100, Deathcounter $groupid){
		$text = '';
		$GroupIDs = $this->getGroupIDsContainingTarget();
		foreach($GroupIDs as $id){
			$text .= _if( $groupid->exactly($id) )->then( 
				BattleSystem::$healthDCs[$id]->Allies->subDivBecome($damagex100, 100),
			'');
		}
		
		return $text;
	}
	
	protected function loadArmor(Deathcounter $receivingdc, Deathcounter $groupid){
		$text = '';
		$GroupIDs = $this->getGroupIDsContainingTarget();
		foreach($GroupIDs as $id){
			$text .= _if( $groupid->exactly($id) )->then( 
				$receivingdc->setTo(BattleSystem::$armorDCs[$id]->Allies),
			'');
		}
		
		return $text;
	}
	
	protected function putMainOnHoldingCell(){
		$text = '';
		$x = (33.5+$this->BSid*2)*32;
		
		$text .= Grid::putMainRes($x, Map::getHeight()/2*32);
		$text .= Loc::$main->acquire(Loc::$shiftUp);
		$text .= Loc::$shiftUp->acquire(Loc::$aoe3x3);
		$text .= Loc::$aoe3x3->acquire(Loc::$main);
		
		return $text;
	}
	
	protected function loadType($type){
		if( !($type instanceof BSType) ){
			Error("\$type must be a Type...");
		}
		$text = '';
		$text .= $this->type->setTo($type->ID);
		$text .= $this->baseunitid->setTo((int)GetUnitID($type->BaseUnit));
		$text .= $this->apparentunitid->setTo((int)GetUnitID($type->ApparentUnit));
		
		return $text;
	}
	
	protected function clearType(){
		return  $this->type->setTo(0).
				$this->attackTime->setTo(0).
				$this->attackTarget->setTo(0).
				$this->health->setTo(0).
				$this->maxhealth->setTo(0).
				$this->mana->setTo(0).
				$this->maxmana->setTo(0).
				$this->damage->setTo(0).
				$this->armor->setTo(0).
				$this->scanSwitch->clear().
				$this->enableScan->clear().
				$this->magicresist->setTo(0);
	}
	
	public function kill(){
		return  //$this->clearType().
				$this->scanSwitch->clear().
				$this->replacedc->setTo(1);
	}


	/**
	 * Assumes that the BSUnit's location is where you want the unit created.
	 * @return string
	 */
	public function create(){
		$text = '';
		
		$placeholder = UnitManager::$Placeholder;
		
		$text .= _if( $this->replacedc->exactly(1) )->then(
			$this->putMainOnHoldingCell(),
			$placeholder->createAt(Loc::$main, 1),
			$this->clearType(),
			$this->type->setTo(1),
			$this->replacedc->setTo(0),
			$this->scanSwitch->clear(),
			$this->enableScan->clear(),
		'');
		foreach($this->getTypes() as $type){
			$text .= _if( $this->replacedc->exactly($type->ID) )->then(
				CreateUnit($this->Player, $type->BaseUnit, 1, Loc::$spawnbox),
				MoveUnit($this->Player, $type->BaseUnit, 1, Loc::$spawnbox, $this->Location),
				Order($this->Player, $type->BaseUnit, $this->Location, Attack, $this->Location),
				$this->replacedc->setTo(0),
				$this->loadType($type),
				$this->scanSwitch->set(),
				$this->enableScan->set(),
			'');
		}
		
		return $text;
	}
	
	public function remove(){
		$text  = $this->clearType();
		$text .= RemoveUnitAtLocation($this->Player, Men, All, $this->Location);
		$text .= $this->putMainOnHoldingCell();
		$text .= RemoveUnitAtLocation(UnitManager::$Placeholder->Player, UnitManager::$Placeholder->Unit, All, Loc::$main);
		$text .= RemoveUnitAtLocation($this->Player, Men, All, Loc::$main);
		return $text;
	}
	
	public function spawnAs($typeid, $x=null, $y=null){
		if( ($x === null && $y !== null) || ($x !== null && $y === null) ){ Error("x and y must be either both specified or neither (not just one)"); }
		if( !is_int($x) && !($x instanceof Deathcounter) ){ Error("x must be either an integer or a Deathcounter"); }
		if( !is_int($y) && !($y instanceof Deathcounter) ){ Error("y must be either an integer or a Deathcounter"); }
		
		if($typeid instanceof BSType){
			$typeid = $typeid->ID;
		}
		
		$text = '';
		if($x !== null && $y !== null){
			$text .= Grid::putMain($x, $y);
			$text .= Loc::$main->acquire($this->Location);
		}
		$text .= $this->replacedc->setTo($typeid);
		
		return $text;
	}
	
	public function deathAnimation(){
		return  _if($this->type->atLeast(2))->then(
					$this->Location->bloodsplat(),
				'');
	}
	
	public function display(){
		return Display("\t\t\tBSid: $this->BSid").Display("\t\t\tType: $this->type");
	}
	
	
}

