<?php

class Grid{

	static private $xdimension;
	static private $ydimension;
	static private $resolution;
	static private $xoffset;
	
	static private $unit;
	
	static public $slideLeft1;
	static public $slideLeft8;
	static public $slideLeft64;
	
	static public $sandbox;
	static public $origin;
	static public $shiftLeft;
	static public $shiftUp;
	
	static public $YLoc = array();
	
	static public $main;
	
	
	function __construct($x_dimension, $y_dimension, $resolution, $x_offset = 0, $unit = "Terran Comsat Station"){
		
		self::$xdimension = $x_dimension;
		self::$ydimension = $y_dimension;
		self::$resolution = $resolution;
		self::$xoffset = $x_offset;
		self::$unit = $unit;
		
		// Create units along the bottom/
		for($i=0; $i<$x_dimension*32/$resolution; $i++){
			MintUnit(Grid::$unit, P12, $x_offset*32+$i*$resolution, (Map::getHeight()-3)*32, Invincible);
		}
		
		$vert = (Map::getHeight()-$y_dimension)/2;
		MintLocation("sandbox", $x_offset*32, $vert*32, ($x_offset+$x_dimension)*32, (Map::getHeight()-$vert)*32);
		self::$sandbox = new LirinLocation("sandbox");
		MintLocation("gridOrigin", ($x_dimension+$x_offset)*32+32, (Map::getHeight()-3)*32+32, ($x_dimension+$x_offset)*32-32, (Map::getHeight()-3)*32-32);
		self::$origin = new LirinLocation("gridOrigin");
		MintLocation("ShiftLeft",0,0,Map::getWidth()*32*2,0);
		self::$shiftLeft = new LirinLocation("ShiftLeft");
		MintLocation("ShiftUp",0,0,0,Map::getHeight()*32*2);
		self::$shiftUp = new LirinLocation("ShiftUp");
		
		for($i=0;$i<=$y_dimension*32/$resolution/2;$i++){
			MintLocation("YLoc$i", 0,0 , 0, $vert*32*2+$i*$resolution*2);
			self::$YLoc[] = new LirinLocation("YLoc$i");
		}
		
		MintLocation("SlideLeft1", 62-$resolution*2*1, 0, 0, 0);
		self::$slideLeft1 = new LirinLocation("SlideLeft1");
		MintLocation("SlideLeft8", 0, 0, $resolution*2*8-62, 0);
		self::$slideLeft8 = new LirinLocation("SlideLeft8");
		MintLocation("SlideLeft64", 0, 0, $resolution*2*64-62, 0);
		self::$slideLeft64 = new LirinLocation("SlideLeft64");
		
		MintLocation("main", 8, 8, 8, 8);
		self::$main = new ExtendableLocation("main");
		
	}
	
	

	
	
	
	/////
	//ACTIONS
	//

	
	public function putMainRes($xcoord, $ycoord, TempSwitch $success) {
	
		//ERROR
		if( func_num_args() != 3 ){
			Error('COMPILER ERROR FOR PUTMAINRES(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 3: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER), SWITCH)');
		}
		if( !(is_numeric($xcoord) || $xcoord instanceof Deathcounter) ) {
			Error('COMPILER ERROR FOR PUTMAINRES(): ARGUMENT 1 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		if( !(is_numeric($ycoord) || $ycoord instanceof Deathcounter) ) {
			Error('COMPILER ERROR FOR PUTMAINRES(): ARGUMENT 2 NEEDS TO BE A DEATHCOUNTER OR A CONSTANT (INTEGER)');
		}
		
		
		$text = '';
		
		//check if the coordinate is on the playing field or not
		$condition = '';
		if( $xcoord instanceof Deathcounter || $ycoord instanceof Deathcounter ) {
			$text = $success->clear();
			if( $xcoord instanceof Deathcounter ) {
				$condition .= $xcoord->atLeast(Grid::$xoffset*32).$xcoord->atMost((Grid::$xoffset+Grid::$xdimension)*32);
			}
			if( $ycoord instanceof Deathcounter ) {
				$ydiff = (Map::getHeight()-Grid::$ydimension)/2;
				$condition .= $ycoord->atLeast($ydiff*32).$ycoord->atMost((Map::getHeight()-$ydiff)*32);
			}
			$text .= _if( $condition )->then(
				$success->set(),
			'');
		}
		
		
		
		$lastlocation = Grid::$origin;

		if( is_numeric($xcoord) ) {
			
			$xmove = (Grid::$xoffset+Grid::$xdimension)*32 - $xcoord;			
			if( $xmove > Grid::$xdimension*32 || $xmove < 0 )
				ERROR('X coordinate is out of the playing field');
			
			$xmove += 3;
			
			while( $xmove >= Grid::$resolution*64 ){
				$text .= Grid::$slideLeft64->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*64;
				$lastlocation = Grid::$slideLeft64;
			}
			while( $xmove >= Grid::$resolution*8 ){
				$text .= Grid::$slideLeft8->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*8;
				$lastlocation = Grid::$slideLeft8;
			}
			while( $xmove >= Grid::$resolution*1 ){
				$text .= Grid::$slideLeft1->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*1;
				$lastlocation = Grid::$slideLeft1;
			}
										
		}
		
		
		
		
		if( $xcoord instanceof Deathcounter ) {

			$currentMax = (Grid::$xoffset+Grid::$xdimension)*32+3;
			$xtemp = new TempDC($currentMax);
			$text .= $xtemp->setTo($currentMax);
			
			$text .= $xtemp->subtract($xcoord);
			
			//slide 64s
			$text .= Grid::$slideLeft64->centerOn(Grid::$origin);
			$pow = getBinaryPower( floor( $currentMax / (Grid::$resolution*64) ) );
			for($i=$pow; $i>=0; $i--){
				$actions = '';
				$k = pow(2,$i);
				for($j=$k; $j>=1; $j--){
					$actions .= Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64);
				}
				
				$text .= _if( $xtemp->atLeast($k*Grid::$resolution*64) )->then(
					$xtemp->subtract($k*Grid::$resolution*64),
					$actions,
				'');
			}
			
			//slide 8s
			$text .= Grid::$slideLeft8->centerOn(Grid::$slideLeft64);
			$pow = 2;
			$currentMax = $currentMax % (Grid::$resolution*8);
			for($i=$pow; $i>=0; $i--){
				$actions = '';
				$k = pow(2,$i);
				for($j=$k; $j>=1; $j--){
					$actions .= Grid::$slideLeft8->centerOn(P12, Grid::$unit, Grid::$slideLeft8);
				}
				
				$text .= _if( $xtemp->atLeast($k*Grid::$resolution*8) )->then(
					$xtemp->subtract($k*Grid::$resolution*8),
					$actions,
				'');
			}
			
			//slide 1s
			$text .= Grid::$slideLeft1->centerOn(Grid::$slideLeft8);
			$pow = 2;
			for($i=$pow; $i>=0; $i--){
				$actions = '';
				$k = pow(2,$i);
				for($j=$k; $j>=1; $j--){
					$actions .= Grid::$slideLeft1->centerOn(P12, Grid::$unit, Grid::$slideLeft1);
				}
				
				$text .= _if( $xtemp->atLeast($k*Grid::$resolution*1) )->then(
					$xtemp->subtract($k*Grid::$resolution*1),
					$actions,
				'');
			}
			
			//lock main
			$text .= Grid::$main->centerOn(Grid::$slideLeft1);
			$text .= $xtemp->kill();
			$lastlocation = Grid::$main;

		}
		
		
		
		
		if( is_numeric($ycoord) ) {
			
			$ycoord -= (Map::getHeight()-Grid::$ydimension)/2*32;
			$lastunit = Grid::$unit;
			if( $ycoord > Grid::$ydimension*32 || $ycoord < 0 )
				ERROR('Y coordinate is out of the playing field');

			if( $ycoord < Grid::$ydimension*32/2 - 4 ){
				$text .= Grid::$shiftUp->centerOn(P12, Grid::$unit, $lastlocation);
				$lastlocation = Grid::$shiftUp;
				$lastunit = "Map Revealer";
			}
			else{
				$ycoord = Grid::$ydimension*32 - $ycoord;
			}
			
			$y = (int)round( ($ycoord-1) / Grid::$resolution);
			
			$text .= Grid::$YLoc[$y]->centerOn(P12, $lastunit, $lastlocation);
			$lastlocation = Grid::$YLoc[$y];
							
			$text .= Grid::$main->centerOn($lastlocation);


		}
		
		
		if( $ycoord instanceof Deathcounter ) {
			
			if( is_numeric($xcoord) ) {
				$text .= Grid::$main->centerOn(P12, Grid::$unit, $lastlocation);
			}

			$ytemp = new TempDC(Map::getHeight()*32);
			
			$text .= _if( $ycoord->atMost(Map::getHeight()*32/2 - 5) )->then(
				Grid::$shiftUp->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$shiftUp),
				$ytemp->setTo($ycoord),
			'');
			$text .= _if( $ycoord->atLeast(Map::getHeight()*32/2 - 4) )->then(
				$ytemp->setTo(Map::getHeight()*32),
				$ytemp->subtract($ycoord),
			'');
			
			$text .= $ytemp->subtract( (Map::getHeight()-Grid::$ydimension)/2*32 - 1 );
			
			$ignore = new TempSwitch();
			$text .= $ignore->set();
			
			$P4 = new Player(P4);
			$text .= $P4->setGas($ytemp);
			
			for($i=Grid::$ydimension*32/Grid::$resolution/2; $i>0; $i--){
				$text .= _if( $ignore->is_set(), $ytemp->atLeast($i*Grid::$resolution-3) )->then(
					Grid::$YLoc[$i]->centerOn(Grid::$main),
					Grid::$main->centerOn(Grid::$YLoc[$i]),
					$ignore->clear(),
				'');
			}
			$text .= _if( $ignore->is_set() )->then(
				Grid::$YLoc[0]->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$YLoc[0]),
				$ignore->kill(),
			'');
			
			$text .= $ytemp->kill();
			
		}
		
		
		
		return $text;
		
	}
	
	
	
	
	
	
	

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
		$lastlocation = Grid::$origin;

		if( is_numeric($xcoord) ) {
			
			$xmove = (Grid::$xoffset+Grid::$xdimension)*32 - $xcoord;			
			if( $xmove > Grid::$xdimension*32 || $xmove < 0 )
				ERROR('X coordinate is out of the playing field');
			
			$xmove += 3;
			
			while( $xmove >= Grid::$resolution*64 ){
				$text .= Grid::$slideLeft64->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*64;
				$lastlocation = Grid::$slideLeft64;
			}
			while( $xmove >= Grid::$resolution*8 ){
				$text .= Grid::$slideLeft8->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*8;
				$lastlocation = Grid::$slideLeft8;
			}
			while( $xmove >= Grid::$resolution*1 ){
				$text .= Grid::$slideLeft1->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*1;
				$lastlocation = Grid::$slideLeft1;
			}
			
			$xmove -= 3;
							
		}

		/**
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
		*/

		if( is_numeric($ycoord) ) {
			
			$swap = 0;
			$ycoord -= (Map::getHeight()-Grid::$ydimension)/2*32;
			$lastunit = Grid::$unit;
			if( $ycoord > Grid::$ydimension*32 || $ycoord < 0 )
				ERROR('Y coordinate is out of the playing field');

			if( $ycoord < Grid::$ydimension*32/2 - 4 ){
				$text .= Grid::$shiftUp->centerOn(P12, Grid::$unit, $lastlocation);
				$lastlocation = Grid::$shiftUp;
				$lastunit = "Map Revealer";
			}
			else{
				$ycoord = Grid::$ydimension*32 - $ycoord;
				$swap = 1;
			}
			
			$y = (int)round( ($ycoord-1) / Grid::$resolution);
			
			$text .= Grid::$YLoc[$y]->centerOn(P12, $lastunit, $lastlocation);
			$lastlocation = Grid::$YLoc[$y];
			
			$ycoord = ($ycoord+3) % Grid::$resolution;
			if( $swap == 0 ){ $ycoord -= 3; }
			else{ $ycoord = 4 - $ycoord; }
							
			$text .= Grid::$main->centerOn($lastlocation);


		}


		/**
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
		 */

		return $text;

	}












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