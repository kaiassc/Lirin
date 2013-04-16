<?php

class UnitManager {
	
	private static $Specified = array();
	private static $Unspecified = array();
	private static $DontCount = array();
	
	private static $PreplacedIndex;
	
	/* @var IndexedUnit */
	static $Catcher;
	/* @var UnitGroup */
	static $Buffer;
	/* @var UnitGroup */
	static $Placeholder;
	
	function __construct($lastpreplacedindex){
		if(!is_int($lastpreplacedindex)){ Error("You have to specify an integer for the Last Preplaced Unit on the minted map"); }
		self::$PreplacedIndex = $lastpreplacedindex;
		
		$index = self::MintUnitWithAnyIndex("Bengalaas (Jungle)", P8, 33.5*32, 1.5*32);
		
		self::$Catcher      = new IndexedUnit($index, "Bengalaas (Jungle)", P8, Anywhere);
		self::$Placeholder  = new UnitGroup("Bengalaas (Jungle)", P7, Anywhere);
		self::$Buffer       = new UnitGroup("Kakaru (Twilight)", P8, Anywhere);
		
	}
	
	public function firstTrigs(){
		
		$P1 = new Player(P1);
		$catcher = self::$Catcher;
		$success = new TempSwitch();
		
		$P1->_if( $catcher->notAt(Anywhere) )->then(
			
			$this->findCatcher($success),
			_if( $success )->then(
				Grid::putMainRes(33.5*32, Map::getHeight()/2*32),
				Loc::$main->acquire(Loc::$shiftUp),
				Loc::$shiftUp->acquire(Loc::$aoe3x3),
				$catcher->createAt(Loc::$aoe3x3, 1),
				
				$this->createBSUnits(),
				
			''),
			
			$success->release(),
			
		'');
		
	}
	
	private function findCatcher(TempSwitch $success){
		$buffer = self::$Buffer;
		$catcher = self::$Catcher;
		
		$text = $success->clear();
		
		for($i=1; $i<=100; $i++){
			$text .= _if( $success->is_clear(), $catcher->isNAIID() )->then(
				$success->set(),
			'');
			$text .= _if( $success->is_clear() )->then(
				$buffer->create(1),
				$buffer->remove(),
			'');
		}
		return $text;
	}
	private function createBSUnits(){
		$P4 = new Player(P4);
		
		$text = '';
		foreach(array_reverse(BattleSystem::getBSUnits()) as $bsunit){
			/* @var BSUnit $bsunit */
			$text .= _if( $bsunit->replacedc->atLeast(1) )->then(
				$bsunit->create(),
				$P4->addOre(1),
			'');
		}
		return $text;
	}
	
	public function lastTrigs(){
		/* BSUNIT REPLACEMENT */
		$P8 = new Player(P8);
		
		$catcher = self::$Catcher;
		$bufferunit = self::$Buffer;
		
		$unitdied = new TempSwitch();
		foreach(BattleSystem::getBSUnits() as $bsunit){
			$P8->_if( $bsunit->replacedc->atLeast(1) )->then(
				$unitdied->set(),
			'');
		}
		
		$P8->_if( $unitdied )->then(
			$bufferunit->create(1),
			$bufferunit->remove(All),
		'');
		
		foreach(BattleSystem::getBSUnits() as $bsunit){
			$P8->_if( $bsunit->replacedc->atLeast(1) )->then(
				$bsunit->deathAnimation(),
				$bsunit->remove(),
				
			'');
		}
		
		$P8->_if( $unitdied )->then(
			$catcher->remove(All),
			
			$bufferunit->create(1),
			$bufferunit->remove(All),
			
			$unitdied->release(),
		'');
		
	}
	
	
	static function MintUnit($unit, $player, $x, $y, $properties = null){
		if( !is_int($x) || !is_int($y) ){
			Error("The X and Y coordinates need to be integers");
		}
		
		if($player instanceof Player){
			$props = null;
			if(isset($properties)){
				if(is_array($properties)){
					$props = $properties;
				}
				else{
					$props = array_slice(func_get_args(), 4);
				}
			}
			foreach($player->PlayerList as $plyr){
				self::MintUnit($unit, $plyr, $x, $y, $props);
			}
		}
		if($unit === "Map Revealer" || $unit === "Start Location"){
			self::$DontCount[] = array($unit, $player, $x, $y, null);
			return;
		}
		
		$props = null;
		if(isset($properties)){
			if(is_array($properties)){
				$props = $properties;
			}
			else{
				$props = array_slice(func_get_args(), 4);
			}
		}
		
		self::$Unspecified[] = array($unit, $player, $x, $y, $props);
		
		
	}
	
	static function MintUnitWithIndex($unit, $player, $x, $y, $index, $properties = null){
		if(!is_int($index)){
			Error("Index must be an integer");
		}
		if($index > 1699){
			Error("The index must be between 0 and 1699");
		}
		if(array_key_exists($index, self::$Specified)){
			Error("You're trying to mint a unit for an index that is already taken!");
		}
		if($index <= self::$PreplacedIndex){
			Error("You're trying to mint a unit for an index that is already taken by a unit preplaced on the input map (index <= lastpreplacedindex)!");
		}
		if($player instanceof Player){
			if(count($player->PlayerList) > 1){
				Error("You're trying to mint unit for a specific index with multiple players");
			}
		}
		
		
		$props = null;
		if(is_array($properties)){
			$props = $properties;
		}
		else{
			$props = array_slice(func_get_args(), 4);
		}
		
		self::$Specified[$index] = array($unit, $player, $x, $y, $props);
		
		return new IndexedUnit($index, $unit, $player);
	}

	/**
	 * Returns the integer of the index its reserving for you.
	 * 
	 * @param $unit
	 * @param $player
	 * @param $x
	 * @param $y
	 * @param null $properties
	 * @return int|null
	 */
	static function MintUnitWithAnyIndex($unit, $player, $x, $y, $properties = null){
		if($player instanceof Player){
			if(count($player->PlayerList) > 1){
				Error("You're trying to mint unit for an index with multiple players");
			}
		}

		$props = null;
		if(is_array($properties)){
			$props = $properties;
		}
		else{
			$props = array_slice(func_get_args(), 4);
		}
		
		
		
		$index = null;
		$first = self::$PreplacedIndex+1;
		for($i=$first; $i<=1699; $i++){
			if(!array_key_exists($i, self::$Specified)){
				$index = $i;
				break;
			}
		}
		if($index === null){
			Error("Couldn't find an available index for some reason");
		}
		
		self::$Specified[$index] = array($unit, $player, $x, $y, $props);
		return $index;
	}
	
	static function MintMapRevealers($players){
		$pArray = func_get_args();
		if($players instanceof Player){
			$pArray = $players->PlayerList;
		}
		
		foreach($pArray as $player ){
			for($x=8; $x<=Map::getWidth(); $x+=16){
				for($y=8; $y<=Map::getHeight(); $y+=16){
					self::MintUnit('Map Revealer',$player,$x*32,$y*32);
				}
			}
		}
	}
	

	public function CreateEngine(){
		$buffer = "Terran Machine Shop";

		$first = self::$PreplacedIndex+1;
		
		$Inspecific = self::$Unspecified;
		foreach(self::$DontCount as $array){
			$Inspecific[] = $array;
		}
		$dcIndex = 0;
		$dcMax = count(self::$DontCount);
		$unIndex = 0;
		$unMax = count(self::$Unspecified);
		$spIndex = 0;
		$spMax = count(self::$Specified);
		for($i=$first; $i<=1699; $i++){
			if(array_key_exists($i, self::$Specified)){
				list($unit, $player, $x, $y, $props) = self::$Specified[$i];
				MintUnit($unit, $player, $x, $y, $props);
				$spIndex++;
				continue;
			}
			if($unIndex < $unMax){
				list($unit, $player, $x, $y, $props) = self::$Unspecified[$unIndex];
				MintUnit($unit, $player, $x, $y, $props);
				$unIndex++;
				continue;
			}
			if($spIndex >= $spMax){ 
				if($dcIndex < $dcMax){
					list($unit, $player, $x, $y, $props) = self::$DontCount[$dcIndex];
					MintUnit($unit, $player, $x, $y, $props);
					$dcIndex++;
					continue;
				} else {
					break;
				}
			}
			
			// buffer unit
			MintUnit($buffer, P12, 64, 64, Invincible);
		}
		
	}  
	
}
