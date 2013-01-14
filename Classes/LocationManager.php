<?php

class LocationManager {
	
	private static $Names = array();
	
	private static $Specified = array();
	private static $Unspecified = array();
	
	
	function __construct(){
		
		self::MintLocationWithIndex(Anywhere, 0, 0, 0, 0, 64);
	}
	
	static function MintLocation($name, $x1, $y1, $x2, $y2){
		if( !is_int($x1) || !is_int($y1) || !is_int($x2) || !is_int($y2) ){
			Error("The X and Y coordinates need to be integers");
		}
		
		if(in_array($name, self::$Names)){
			Error("The name $name is already taken");
		} else {
			self::$Names[] = $name;
		}
		
		self::$Unspecified[] = array($name, $x1, $y1, $x2, $y2);
		return new LirinLocation($name);
	}
	
	static function MintLocationWithIndex($name, $x1, $y1, $x2, $y2, $index){
		if( !is_int($x1) || !is_int($y1) || !is_int($x2) || !is_int($y2) ){
			Error("The X and Y coordinates need to be integers");
		}
		if(!is_int($index)){
			Error("Index must be an integer");
		}
		if($index > 255){
			Error("The index must be between 0 and 1699");
		}
		if(array_key_exists($index, self::$Specified)){
			Error("You're trying to mint a location for an index that is already taken!");
		}
				
		if(in_array($name, self::$Names)){
			Error("The name $name is already taken");
		} else {
			self::$Names[] = $name;
		}
		
		self::$Specified[$index] = array($name, $x1, $y1, $x2, $y2);
		return new LirinLocation($name);
	}

	/**
	 * Returns the integer of the index its reserving for you.
	 * 
	 */
	static function MintLocationWithAnyIndex($name, $x1, $y1, $x2, $y2){
		if( !is_int($x1) || !is_int($y1) || !is_int($x2) || !is_int($y2) ){
			Error("The X and Y coordinates need to be integers");
		}
		
		if(in_array($name, self::$Names)){
			Error("The name $name is already taken");
		} else {
			self::$Names[] = $name;
		}
		
		$index = null;
		for($i=0; $i<=255; $i++){
			if(!array_key_exists($i, self::$Specified)){
				$index = $i;
				break;
			}
		}
		if($index === null){
			Error("Couldn't find an available index for some reason");
		}
		
		self::$Specified[$index] = array($name, $x1, $y1, $x2, $y2);
		return $index;
	}
	
	public function CreateEngine(){
		$buffername = "bufferloc";
		$bufferindex = 0;

		$unIndex = 0;
		$unMax = count(self::$Unspecified);
		$spIndex = 0;
		$spMax = count(self::$Specified);
		for($i=0; $i<=255; $i++){
			if(array_key_exists($i, self::$Specified)){
				list($name, $x1, $y1, $x2, $y2) = self::$Specified[$i];
				if($name !== Anywhere){
					MintLocation($name, $x1, $y1, $x2, $y2);
				}
				$spIndex++;
				continue;
			}
			if($unIndex < $unMax){
				list($name, $x1, $y1, $x2, $y2) = self::$Unspecified[$unIndex];
				MintLocation($name, $x1, $y1, $x2, $y2);
				$unIndex++;
				continue;
			}
			if($spIndex >= $spMax){ break; }

			// buffer unit
			$name = $buffername.$bufferindex;
			MintLocation($name, 16, 48, 16, 48);
			$bufferindex++;
		}
		
	}  
	
}
