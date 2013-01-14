<?php

class UnitManager {
	
	private static $Specified = array();
	private static $Unspecified = array();
	private static $DontCount = array();
	
	private static $PreplacedIndex;
	
	function __construct($lastpreplacedindex){
		if(!is_int($lastpreplacedindex)){ Error("You have to specify an integer for the Last Preplaced Unit on the minted map"); }
		self::$PreplacedIndex = $lastpreplacedindex;
		
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
			$props = array_slice(func_get_args(), 5);
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
			$props = array_slice(func_get_args(), 5);
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
			if($spIndex >= $spMax){ break; }
			
			// buffer unit
			MintUnit($buffer, P12, 64, 64, Invincible);
		}
		
	}  
	
}
