<?php

class LirinStorage{
	
	// Properties
	
	/* @var Deathcounter[] $dcs */
	private $dcs = array();
	/* @var Deathcounter[] $storagedcs */
	private $storagedcs = array();
	
	private $player = null;
	private $totalBits = 0;
	
	// old
	//private $dc = array();
	//private $dcmax = array();
	//private $dcnum = array();
	//private $storage = array();
	//private $max = array();
	//private $truemax = array();
	//private $size = 0;
	
	
	// Constructor
	public function __construct($player, $anydcs){
		// Error
		if( func_num_args() === 0 ){
			Error("You must specify at least 1 deathcounter!");
		}
		if( func_num_args() === 1 && !($player instanceof Deathcounter)){
			Error("You must specify at least 1 deathcounter!");
		}
		
		// Populate dcs array, and player
		if( $player instanceof Player){
			$this->player = $player;
			$this->dcs = func_get_args();
			unset($this->dcs[0]);
		} elseif( $player instanceof Deathcounter) {
			$this->dcs = func_get_args();
		} else {
			Error("You must specify a Player or Deathcounter for the first argument!");
		}
		
		foreach($this->dcs as $dc){
			$this->totalBits += $dc->binaryPower();
		}
		
		$neededstorage = (int)ceil($this->totalBits/31);
		for($i=0; $i<=$neededstorage; $i++){
			if($player instanceof Player){
				$this->storagedcs[] = new Deathcounter($player);
			}
			else{
				$this->storagedcs[] = new Deathcounter();
			}
		}
		
	}

	/**
	 * $bitindex:   where in the storage dcs that you want to push a value in (0 based)
	 * $value:      value you want to push in
	 * @param $bitindex int
	 * @param $value int
	 * @return string
	 */
	private function addBits($bitindex, $value){
		$dcindex = (int)floor($bitindex/31);
		$dcbit = $bitindex % 31;
		
		if($dcindex > count($this->storagedcs)){ Error("You're trying to insert bits outside the range covered by the storage dcs"); }
		
		$dc = $this->storagedcs[$dcindex];
		
		// if it will exceed the deathcounter's capacity
		if( ($dcbit + getBinaryPower($value)) > 31){
			// split value's bits 
			$overflow = 31 - $dcbit;
			$modcap = pow(2,$overflow);
			$value1 = $value % $modcap;
			$value2 = floor($value / $modcap);

			if( ($dcindex+1) > count($this->storagedcs)){ Error("You're trying to insert bits outside the range covered by the storage dcs"); }
			
			// across next dc
			$dc2 = $this->storagedcs[$dcindex+1];
			return $dc->add($value1 << $dcbit) . $dc2->add($value2);
		}
		
		// otherwise just place in dc normally
		return $dc->add($value << $dcbit);
	}

	/**
	 * @param $bitindex int
	 * @param $dc Deathcounter
	 * @return string 
	 */
	private function exportBitInto($bitindex, Deathcounter $dc){
		// find appropriate storage dc, and bit inside it
		$dcindex = (int)floor($bitindex/31);
		$dcbit = $bitindex % 31;
		$value = 1 << $dcbit;
		
		if($dcindex > count($this->storagedcs)){ Error("You're trying to export bits outside the range covered by the storage dcs"); }
		
		$storagedc = $this->storagedcs[$dcindex];
		
		// if that bit is set, remove it and 
		return _if( $storagedc->atLeast($value) )->then(
			$storagedc->subtract($value),
			$dc->add($value),
		'');
	}
	
	private function clearStorage(){
		$text = '';
		foreach($this->storagedcs as $storage){
			$text .= $storage->setTo(0);
		}
		return $text;
	}


	/**
	 * $successSwitch:  will be set if it goes off without a hitch; 
	 *                  it will fail if a character is used that isn't recognized; 
	 *                  if it fails, storage dcs will be zeroed
	 * @param $line int
	 * @param $startblock int
	 * @param $endblock int
	 * @param TempSwitch $successSwitch
	 * @return string
	 */
	public function storeCode($line, $startblock, $endblock, TempSwitch $successSwitch){
		// Error
		if ( $line < 1 || $line > 11 ) {
			Error('Error: $line must be between 1 and 11');
		}
		
		global $CharCodes;
		
		$base = 0;
		$text = '';
		
		if( $line === 1  ){ $base = 186879; } if( $line === 2  ){ $base = 186933; }
		if( $line === 3  ){ $base = 186988; } if( $line === 4  ){ $base = 187042; }
		if( $line === 5  ){ $base = 187097; } if( $line === 6  ){ $base = 187151; }
		if( $line === 7  ){ $base = 187206; } if( $line === 8  ){ $base = 187260; }
		if( $line === 9  ){ $base = 187315; } if( $line === 10 ){ $base = 187369; }
		if( $line === 11 ){ $base = 187424; }
		
		$successcheck = new TempSwitch();
		
		$text .= $successSwitch->set();
		
		// each block
		for($i=$startblock; $i<=$endblock; $i++){
			$block = $i;
			$epd = new EPD($base + $block - 1);
			
			$text .= $successcheck->clear();
			
			// find characater
			foreach($CharCodes as $char=>$info){
				$index = $info['index'];
				$value = $info['value'];
				
				// check each possible value
				$text .= _if( $successSwitch, $epd->between( pow(2,24)*$index, pow(2,24)*($index+1)-1 ) )->then(
					$this->addBits($i*6, $value),
					$successcheck->set(),
				'');
			}
			
			$text .= _if( $successcheck->is_clear() )->then(
				$this->clearStorage(),
				$successSwitch->clear(),
			'');
		}
		$text .= $successcheck->release();
		
		return $text;
	}
	
	public function export(){
		$text = '';
		
		/** @var $dcs Deathcounter[] */
		$dcs = array_reverse($this->dcs);
		
		$currentbit = $this->totalBits;
		
		// foreach dc
		foreach($dcs as $dc){
			$end = $dc->binaryPower();
			$text .= $dc->setTo(0);
			
			// get each of its bit from storage
			for($i=1; $i<=$end; $i++){
				$text .= $this->exportBitInto($currentbit,$dc);
				$currentbit--;
			}
		}
		
		return $text;
	}
	
	/////
	//STORE
	//
	public function store($playerSpecifier=NULL){
		//ERROR
		if(func_num_args() > 1){
			Error('COMPILER ERROR FOR STORE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: PLAYER');
		}
		if(func_num_args() == 1 && !IsStandardPlayer($playerSpecifier)){
			Error('COMPILER ERROR FOR STORE(): ARGUMENT MUST SPECIFY A PLAYER (P1, P2, P3, P4, P5, P6, P7, P8, Allies, Foes, AllPlayers, CP)');
		}
		if(func_num_args() == 1 && $this->player == NULL){
			Error('COMPILER ERROR FOR STORE(): ARGUMENT SPECIFIES A PLAYER, BUT NO PLAYER WAS SPECIFIED IN DCSTORAGE DECLARATION');
		}
		
		$size = $this->size;
		$max = $this->max[$size];
		$tempdc = new TempDC();
		
		$text = '';
		if(func_num_args()==0){
			$storage = $this->storage[$size];
			foreach($this->storage as $storage){
				$text .= $storage->setTo(0);
			}
		}else{
			$storage = clone $this->storage[$size];
            $storage->Player = $playerSpecifier;
			foreach($this->storage as $stor){
				$asdf = clone $stor;
                $asdf->Player = $playerSpecifier;
				$text .= $asdf->setTo(0);
			}
		}
		
		for($i=$this->dcnum-1; $i>=0; $i--){
			if($this->dcmax[$i] < $max){
				$max /= ($this->dcmax[$i]+1);
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $this->dc[$i]->atLeast($k) )->then(
					    $this->dc[$i]->subtract($k),
						$storage->add($max*$k),
						$tempdc->add($k),
					'');
				}
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $tempdc->atLeast($k) )->then(
					    $this->dc[$i]->add($k),
						$tempdc->subtract($k),
					'');
				}
			} else{
				$dcpow = getBinaryPower($this->dcmax[$i]);
				for($j=getBinaryPower($max-1); $j>=0; $j--, $dcpow--){
					$k1 = pow(2,$j);
					$k2 = pow(2,$dcpow);
					$text .= _if( $this->dc[$i]->atLeast($k2) )->then(
					    $this->dc[$i]->subtract($k2),
						$storage->add($k1),
						$tempdc->add($k2),
					'');
				}
				$size--;
				if(func_num_args()==0){
					$storage = $this->storage[$size];
				}else{
					$storage = clone $this->storage[$size];
	                $storage->Player = $playerSpecifier;
				}
				
				$max = $this->max[$size];
				for($j=$dcpow; $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $this->dc[$i]->atLeast($k) )->then(
					    $this->dc[$i]->subtract($k),
						$storage->add($max*$k),
						$tempdc->add($k),
					'');
				}
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $tempdc->atLeast($k) )->then(
					    $this->dc[$i]->add($k),
						$tempdc->subtract($k),
					'');
				}
			}
		}
		$text .= $tempdc->kill();
		
		return $text;
		
	}

	
	/////
	//RETRIEVE
	//
	public function retrieve($playerSpecifier=NULL){
		//ERROR
		if(func_num_args() > 1){
			Error('COMPILER ERROR FOR RETRIEVE(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 0 OR 1: PLAYER');
		}
		if(func_num_args() == 1 && !IsStandardPlayer($playerSpecifier)){
			Error('COMPILER ERROR FOR RETRIEVE(): ARGUMENT MUST SPECIFY A PLAYER (P1, P2, P3, P4, P5, P6, P7, P8, Allies, Foes, AllPlayers, CP)');
		}
		if(func_num_args() == 1 && $this->player == NULL){
			Error('COMPILER ERROR FOR RETRIEVE(): ARGUMENT SPECIFIES A PLAYER, BUT NO PLAYER WAS SPECIFIED IN DCSTORAGE DECLARATION');
		}
		
		$size = $this->size;
		$max = $this->max[$size];
		$tempdc = new TempDC();
		
		$text = '';
		foreach($this->dc as $dc){
			$text .= $dc->setTo(0);
		}
		if(func_num_args()==0){
			$storage = $this->storage[$size];
		}else{
			$storage = clone $this->storage[$size];
            $storage->Player = $playerSpecifier;
		}
		for($i=$this->dcnum-1; $i>=0; $i--){
			if($this->dcmax[$i] < $max){
				$max /= ($this->dcmax[$i]+1);
				for($j=getBinaryPower($this->dcmax[$i]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $storage->atLeast($max*$k) )->then(
					    $this->dc[$i]->add($k),
						$storage->subtract($max*$k),
						$tempdc->add($max*$k),
					'');
				}
			} else{
				$dcpow = getBinaryPower($this->dcmax[$i]);
				for($j=getBinaryPower($max-1); $j>=0; $j--, $dcpow--){
					$k1 = pow(2,$j);
					$k2 = pow(2,$dcpow);
					$text .= _if( $storage->atLeast($k1) )->then(
					    $this->dc[$i]->add($k2),
						$storage->subtract($k1),
						$tempdc->add($k1),
					'');
				}
				for($j=getBinaryPower($this->truemax[$size]); $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $tempdc->atLeast($k) )->then(
					    $storage->add($k),
						$tempdc->subtract($k),
					'');
				}
				$size--;
				if(func_num_args()==0){
					$storage = $this->storage[$size];
				}else{
					$storage = clone $this->storage[$size];
	                $storage->Player = $playerSpecifier;
				}
				$max = $this->max[$size];
				for($j=$dcpow; $j>=0; $j--){
					$k = pow(2,$j);
					$text .= _if( $storage->atLeast($max*$k) )->then(
					    $this->dc[$i]->add($k),
						$storage->subtract($max*$k),
						$tempdc->add($max*$k),
					'');
				}
			}
		}
		if(func_num_args()==0){
			$storage = $this->storage[0];
		}else{
			$storage = clone $this->storage[0];
            $storage->Player = $playerSpecifier;
		}
		for($i=getBinaryPower($this->truemax[0]); $i>=0; $i--){
			$k = pow(2,$i);
			$text .= _if( $tempdc->atLeast($k) )->then(
			    $storage->add($k),
				$tempdc->subtract($k),
			'');
		}
		$text .= $tempdc->kill();
		
		return $text;
		
	}
	
	

	
	
}

