<?php

class Grid{

	static private $xdimension;
	static private $ydimension;
	
	static private $xoffset;
	
	static private $resolution;
	
	
	function __construct(int $x_dimension, int $y_dimension, int $x_offset, int $resolution){
		
		self::$xdimension = $x_dimension;
		self::$ydimension = $y_dimension;
		self::$xoffset = $x_offset;
		self::$resolution = $resolution;
		
		// Create units along the bottom
		Error("HEAHGIGIAEHG");
		
		//MintUnit("Terran Comsat Station", P12, 2246, 1164);
		//MintUnit(1049,P2, 2246, 1164);
		for($i=1;$i<=128;$i++){
			MintUnit(1049,P2, (64+$i)*32,(256-8)*32);
		}
		
		
		
		
	}
	
	

	
	//for($i=0; $i<$x_dimension*32/$resolution; $i++){
		//	MintUnit("Comsat Station", P12, $x_offset*32+$i*$resolution, /*(Map::height()-10)*/1*32, Invincible);
		//}
	
	/////
	//ACTIONS
	//


	
	
	
	
	
	
	
	
	/**
	public function putMain($xcoord, $ycoord) {
		//ERROR
		if( func_num_args() != 2 ){
			Error('COMPILER ERROR FOR PUTMAIN(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 2: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER))');
		}
		if( !(is_numeric($xcoord) || $xcoord instanceof Deathcounter) ) {
			Error('COMPILER ERROR FOR PUTMAIN(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if( !(is_numeric($ycoord) || $ycoord instanceof Deathcounter) ) {
			Error('COMPILER ERROR FOR PUTMAIN(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}


		$text = '';

		if( is_numeric($xcoord) ) {

			if( $xcoord > 625 ){
				$xcoord = 625;
			}
			if( $xcoord < 16 ){
				$xcoord = 16;
			}
			if( $xcoord > 383 ){
				$xcoord = 383;
			}
			if( $xcoord < 0 ){
				$xcoord = 0;
			}
			$xmove = 625 - $xcoord;
			
			$main = new Location('_Main');
			$X32 = new Location('_Grid32');
			$origin = new Location('_GridOrigin');
			
			if( $xmove >= 32 ){
				$text .= $X32->centerOn($origin);
				$endtext = $main->centerOn($X32);
				$xmove -= 3;
			}
			else if( $xmove > 0 ){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridOrigin');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1');
			}
			else{
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridOrigin');
			}
			while( $xmove >= 32 ){
				$text .= MoveLocation('_GridX32', 'Player 12', 'Right Wall Flame Trap', '_GridX32');
				$xmove -= 32;
			}
			if( $xmove > 0 && $xcoord <= 593){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridX32');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1');
			}
			while( $xmove > 0 ){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Right Wall Flame Trap', '_GridX1');
				$xmove--;
			}
			$text .= $endtext;

		}


		if( $xcoord instanceof Deathcounter ) {

			$switch = new TempSwitch();
			$tempdc = new TempDC();
			for( $i=4; $i>=0; $i-- ){
				$k = pow(2,$i);
				$text .= _if( $xcoord->atMost(16-$k) )->then(
					$xcoord->add($k),
					$tempdc->add($k),
				'');
			}
			$text .= _if( $xcoord->atLeast(625) )->then(
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridOrigin'),
			'');
			$text .= _if( $xcoord->atMost(593) )->then(
				MoveLocation('_GridX32', 'Player 12', 'Map Revealer', '_GridOrigin'),
				$xcoord->add(3),
				$tempdc->add(3),
				$switch->set(),
			'');
			for( $i=4; $i>=0; $i-- ){
				$k = pow(2,$i);
				$actions = '';
				for( $j=0; $j<$k; $j++ ){
					$actions .= MoveLocation('_GridX32', 'Player 12', 'Right Wall Flame Trap', '_GridX32');
				}
				$text .= _if( $xcoord->atMost(625-32*$k) )->then(
					$actions,
					$xcoord->add(32*$k),
					$tempdc->add(32*$k),
				'');
			}
			$text .= _if( $switch->is_clear(), $xcoord->atMost(624) )->then(
				MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridOrigin'),
			'');
			$text .= _if( $switch->is_set(), $xcoord->atMost(624) )->then(
				MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridX32'),
				$switch->clear(),
			'');
			for( $i=4; $i>=0; $i-- ){
				$k = pow(2,$i);
				$actions = '';
				for( $j=0; $j<$k; $j++ ){
					$actions .= MoveLocation('_GridX1', 'Player 12', 'Right Wall Flame Trap', '_GridX1');
				}
				$text .= _if( $xcoord->atMost(625-$k) )->then(
					$actions,
					$xcoord->add($k),
					$tempdc->add($k),
				'');
			}
			$text .= _if( $switch->is_clear(), $tempdc->atLeast(1) )->then(
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1'),
			'');
			$text .= _if( $switch->is_set() )->then(
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX32'),
				$switch->clear(),
			'');
			for( $i=9; $i>=0; $i-- ){
				$k = pow(2,$i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$xcoord->subtract($k),
					$tempdc->subtract($k),
			'');
			}
			$text .= $tempdc->kill().
					$switch->kill();

		}


		if( is_numeric($ycoord) ) {

			if( $ycoord < 192 ){
				$text .= MoveLocation('_YSwitch0', 'Player 12', 'Map Revealer', '_Main');
				$text .= MoveLocation('_Main', 'Player 12', 'Map Revealer', '_YSwitch0');
			}
			else{
				$ycoord = 383 - $ycoord;
			}
			if( $ycoord > 0 ){
				$text .= MoveLocation('_GridY'.($ycoord-1), 'Player 12', 'Map Revealer', '_Main');
				$text .= MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridY'.($ycoord-1));
			}

			return $text;

		}


		if( $ycoord instanceof Deathcounter ) {

			$switch = new TempSwitch();
			$ymove = new TempDC();
			$tempdc = new TempDC(383);
			$text .= _if( $ycoord->atMost(191) )->then(
				MoveLocation('_YSwitch0', 'Player 12', 'Map Revealer', '_Main'),
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_YSwitch0'),
			'');
			$text .= _if( $ycoord->atLeast(192) )->then(
				$ymove->setTo(383),
				$switch->set(),
			'');
			for( $i=8; $i>=0; $i-- ) {
				$k = pow(2,$i);
				$text .= _if( $switch->is_set(), $ycoord->atLeast($k) )->then(
					$ycoord->subtract($k),
					$ymove->subtract($k),
					$tempdc->add($k),
				'');
			}
			for( $i=7; $i>=0; $i-- ) {
				$k = pow(2,$i);
				$text .= _if( $switch->is_set(), $ymove->atLeast($k) )->then(
					$ycoord->add($k),
					$ymove->subtract($k),
				'');
			}
			for( $i=0; $i<=190; $i++ ) {
				$text .= _if( $ycoord->exactly($i+1) )->then(
					MoveLocation('_GridY'.$i, 'Player 12', 'Map Revealer', '_Main'),
					MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridY'.$i),
				'');
			}
			$text .= _if( $switch->is_set() )->then(
				$ycoord->setTo(0),
				$ycoord->becomeDel($tempdc),
			'');
			$text .= $ymove->kill().
					$switch->kill();

			return $text;

		}
		*/












		/*
		//X AND Y ARE BOTH CONSTANTS (INTEGERS)
		if( is_numeric($xcoord) && is_numeric($ycoord) ) {

			if( $xcoord > 625 ){
				$xcoord = 625;
			}
			if( $xcoord < 16 ){
				$xcoord = 16;
			}
			if( $xcoord > 383 ){
				$xcoord = 383;
			}
			if( $xcoord < 0 ){
				$xcoord = 0;
			}
			$xmove = 625 - $xcoord;
			$ymove = $ycoord;

			$text = '';
			if( $xmove >= 32 ){
				$text .= MoveLocation('_GridX32', 'Player 12', 'Map Revealer', '_GridOrigin');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX32');
				$xmove -= 3;
			}
			else if( $xmove > 0 ){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridOrigin');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1');
			}
			else{
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridOrigin');
			}
			while( $xmove >= 32 ){
				$text .= MoveLocation('_GridX32', 'Player 12', 'Right Wall Flame Trap', '_GridX32');
				$xmove -= 32;
			}
			if( $xmove > 0 && $xcoord <= 593){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridX32');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1');
			}
			while( $xmove > 0 ){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Right Wall Flame Trap', '_GridX1');
				$xmove--;
			}
			$text .= $endtext;

			if( $ycoord < 192 ){
				$text .= MoveLocation('_YSwitch0', 'Player 12', 'Map Revealer', '_Main');
				$text .= MoveLocation('_Main', 'Player 12', 'Map Revealer', '_YSwitch0');
			}
			else{
				$ymove = 383 - $ycoord;
			}
			if( $ymove > 0 ){
				$text .= MoveLocation('_GridY'.($ymove-1), 'Player 12', 'Map Revealer', '_Main');
				$text .= MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridY'.($ymove-1));
			}

			return $text;

		}


		//X IS CONSTANT (INTEGER), Y IS DEATHCOUNTER
		if( is_numeric($xcoord) ) {

			if( $xcoord > 625 ){
				$xcoord = 625;
			}
			if( $xcoord < 16 ){
				$xcoord = 16;
			}
			if( $xcoord > 383 ){
				$xcoord = 383;
			}
			if( $xcoord < 0 ){
				$xcoord = 0;
			}
			$xmove = 625 - $xcoord;

			$text = '';
			if( $xmove >= 32 ){
				$text .= MoveLocation('_GridX32', 'Player 12', 'Map Revealer', '_GridOrigin');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX32');
				$xmove -= 3;
			}
			else if( $xmove > 0 ){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridOrigin');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1');
			}
			else{
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridOrigin');
			}
			while( $xmove >= 32 ){
				$text .= MoveLocation('_GridX32', 'Player 12', 'Right Wall Flame Trap', '_GridX32');
				$xmove -= 32;
			}
			if( $xmove > 0 && $xcoord <= 593){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridX32');
				$endtext = MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1');
			}
			while( $xmove > 0 ){
				$text .= MoveLocation('_GridX1', 'Player 12', 'Right Wall Flame Trap', '_GridX1');
				$xmove--;
			}
			$text .= $endtext;

			$switch = new TempSwitch();
			$ymove = new TempDC();
			$tempdc = new TempDC(383);
			$text .= _if( $ycoord->atMost(191) )->then(
				MoveLocation('_YSwitch0', 'Player 12', 'Map Revealer', '_Main'),
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_YSwitch0'),
			'');
			$text .= _if( $ycoord->atLeast(192) )->then(
				$ymove->setTo(383),
				$switch->set(),
			'');
			for( $i=8; $i>=0; $i-- ) {
				$k = pow(2,$i);
				$text .= _if( $switch->is_set(), $ycoord->atLeast($k) )->then(
					$ycoord->subtract($k),
					$ymove->subtract($k),
					$tempdc->add($k),
				'');
			}
			for( $i=7; $i>=0; $i-- ) {
				$k = pow(2,$i);
				$text .= _if( $switch->is_set(), $ymove->atLeast($k) )->then(
					$ycoord->add($k),
					$ymove->subtract($k),
					'');
			}
			for( $i=0; $i<=190; $i++ ) {
				$text .= _if( $ycoord->exactly($i+1) )->then(
					MoveLocation('_GridY'.$i, 'Player 12', 'Map Revealer', '_Main'),
					MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridY'.$i),
				'');
			}
			$text .= _if( $switch->is_set() )->then(
				$ycoord->setTo(0),
				$ycoord->becomeDel($tempdc),
			'');
			$text .= $ymove->kill().
					$switch->kill();

			return $text;

		}


		//X IS DEATHCOUNTER, Y IS CONSTANT (INTEGER)
		if( is_numeric($ycoord) ) {

			$switch = new TempSwitch();
			$tempdc = new TempDC();
			$text = '';
			for( $i=4; $i>=0; $i-- ){
				$k = pow(2,$i);
				$text .= _if( $xcoord->atMost(16-$k) )->then(
					$xcoord->add($k),
					$tempdc->add($k),
				'');
			}
			$text .= _if( $xcoord->atLeast(625) )->then(
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridOrigin'),
			'');
			$text .= _if( $xcoord->atMost(593) )->then(
				MoveLocation('_GridX32', 'Player 12', 'Map Revealer', '_GridOrigin'),
				$xcoord->add(3),
				$tempdc->add(3),
				$switch->set(),
			'');
			for( $i=4; $i>=0; $i-- ){
				$k = pow(2,$i);
				$actions = '';
				for( $j=0; $j<$k; $j++ ){
					$actions .= MoveLocation('_GridX32', 'Player 12', 'Right Wall Flame Trap', '_GridX32');
				}
				$text .= _if( $xcoord->atMost(625-32*$k) )->then(
					$actions,
					$xcoord->add(32*$k),
					$tempdc->add(32*$k),
				'');
			}
			$text .= _if( $switch->is_clear(), $xcoord->atMost(624) )->then(
				MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridOrigin'),
			'');
			$text .= _if( $switch->is_set(), $xcoord->atMost(624) )->then(
				MoveLocation('_GridX1', 'Player 12', 'Map Revealer', '_GridX32'),
				$switch->clear(),
			'');
			for( $i=4; $i>=0; $i-- ){
				$k = pow(2,$i);
				$actions = '';
				for( $j=0; $j<$k; $j++ ){
					$actions .= MoveLocation('_GridX1', 'Player 12', 'Right Wall Flame Trap', '_GridX1');
				}
				$text .= _if( $xcoord->atMost(625-$k) )->then(
					$actions,
					$xcoord->add($k),
					$tempdc->add($k),
				'');
			}
			$text .= _if( $switch->is_clear(), $tempdc->atLeast(1) )->then(
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX1'),
			'');
			$text .= _if( $switch->is_set() )->then(
				MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridX32'),
				$switch->clear(),
			'');
			for( $i=9; $i>=0; $i-- ){
				$k = pow(2,$i);
				$text .= _if( $tempdc->atLeast($k) )->then(
					$xcoord->subtract($k),
					$tempdc->subtract($k),
				'');
			}

			$ymove = $ycoord;
			if( $ycoord < 192 ){
				$text .= MoveLocation('_YSwitch0', 'Player 12', 'Map Revealer', '_Main');
				$text .= MoveLocation('_Main', 'Player 12', 'Map Revealer', '_YSwitch0');
			}
			else{
				$ymove = 383 - $ycoord;
			}
			if( $ymove > 0 ){
				$text .= MoveLocation('_GridY'.($ymove-1), 'Player 12', 'Map Revealer', '_Main');
				$text .= MoveLocation('_Main', 'Player 12', 'Map Revealer', '_GridY'.($ymove-1));
			}

			$text .= $tempdc->kill().
					$switch->kill();

			return $text;

		}
		*/

	//}
	
	
	
}

?>