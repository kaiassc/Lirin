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
	static public $topLeft;
	
	static public $YLoc = array();
	
	static public $main;
	
	
	function __construct($x_dimension, $y_dimension, $resolution, $x_offset = null, $unit = "Terran Comsat Station"){
		
		self::$xdimension = $x_dimension;
		self::$ydimension = $y_dimension;
		self::$resolution = $resolution;
		self::$xoffset = $x_offset;
		self::$unit = $unit;
		
		//if no offset specified, determine it
		if($x_offset === null){
			$x_offset = (int)round((Map::getWidth()-$x_dimension)/2);
		}
		
		// Create units along the bottom/
		for($i=0; $i<$x_dimension*32/$resolution; $i++){
			UnitManager::MintUnit(Grid::$unit, P12, $x_offset*32+$i*$resolution, (Map::getHeight()-3)*32, Invincible);
		}
		
		$vert = (Map::getHeight()-$y_dimension)/2;
		LocationManager::MintLocation("sandbox", $x_offset*32, $vert*32, ($x_offset+$x_dimension)*32, (Map::getHeight()-$vert)*32);
		self::$sandbox = new LirinLocation("sandbox");
		LocationManager::MintLocation("gridOrigin", ($x_dimension+$x_offset)*32+32, (Map::getHeight()-3)*32+32, ($x_dimension+$x_offset)*32-32, (Map::getHeight()-3)*32-32);
		self::$origin = new LirinLocation("gridOrigin");
		LocationManager::MintLocation("ShiftLeft",0,0,Map::getWidth()*32*2,0);
		self::$shiftLeft = new LirinLocation("ShiftLeft");
		LocationManager::MintLocation("ShiftUp",0,0,0,Map::getHeight()*32*2);
		self::$shiftUp = new LirinLocation("ShiftUp");
		LocationManager::MintLocation("TopLeft",0,0,0,0);
		self::$topLeft = new LirinLocation("TopLeft");
		
		for($i=0;$i<=$y_dimension*32/$resolution/2;$i++){
			LocationManager::MintLocation("YLoc$i", 0,0 , 0, $vert*32*2+$i*$resolution*2);
			self::$YLoc[] = new LirinLocation("YLoc$i");
		}
		
		LocationManager::MintLocation("SlideLeft1", 62-$resolution*2*1, 0, 0, 0);
		self::$slideLeft1 = new LirinLocation("SlideLeft1");
		LocationManager::MintLocation("SlideLeft8", 0, 0, $resolution*2*8-62, 0);
		self::$slideLeft8 = new LirinLocation("SlideLeft8");
		LocationManager::MintLocation("SlideLeft64", 0, 0, $resolution*2*64-62, 0);
		self::$slideLeft64 = new LirinLocation("SlideLeft64");
		
		LocationManager::MintLocationWithIndex("main", 8, 8, 8, 8, 147);
		self::$main = new ExtendableLocation("main", 4);
		
	}
	
	

	
	
	
	/////
	//ACTIONS
	//
	
	
	//put main closest to coordinate using the resolution (no pixel shifts)
	static function putMainRes($xcoord, $ycoord, TempSwitch $success = null) {
	
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
		if($success !== null){
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
			else{
				$text .= $success->set();
			}
		}
		
		
		//location to make putMainRes(const, const) more efficient
		$lastlocation = Grid::$origin;

		//X is a constant integer
		if( is_numeric($xcoord) ) {
			
			//xmove is a coordinate that starts at the grid's origin and counts over
			$xmove = (Grid::$xoffset+Grid::$xdimension)*32 - $xcoord;			
			if( $xmove > Grid::$xdimension*32 || $xmove < 0 )
				ERROR('X coordinate is out of the playing field');
			
			//make it so main is always within 4 pixels (this essentially rounds)
			$xmove += 3;
			
			//move 64*resolution left
			while( $xmove >= Grid::$resolution*64 ){
				$text .= Grid::$slideLeft64->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*64;
				$lastlocation = Grid::$slideLeft64;
			}
			//move 8*resolution left
			while( $xmove >= Grid::$resolution*8 ){
				$text .= Grid::$slideLeft8->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*8;
				$lastlocation = Grid::$slideLeft8;
			}
			//move 1*resolution left
			while( $xmove >= Grid::$resolution*1 ){
				$text .= Grid::$slideLeft1->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*1;
				$lastlocation = Grid::$slideLeft1;
			}
										
		}
		
		
		
		//X is a deathcounter
		if( $xcoord instanceof Deathcounter ) {

			$currentMax = (Grid::$xoffset+Grid::$xdimension)*32+3;
			$xtemp = new TempDC($currentMax);
			
			//xtemp starts at grid origin then counts left
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
		
		
		
		//Y is a constant
		if( is_numeric($ycoord) ) {
			
			//start Y at playing field (subtract any edge)
			$ycoord -= (Map::getHeight()-Grid::$ydimension)/2*32;
			$lastunit = Grid::$unit;
			
			//error check
			if( $ycoord > Grid::$ydimension*32 || $ycoord < 0 )
				ERROR('Y coordinate is out of the playing field');

			//top half of map
			if( $ycoord < Grid::$ydimension*32/2 - 4 ){
				$text .= Grid::$shiftUp->centerOn(P12, Grid::$unit, $lastlocation);
				$lastlocation = Grid::$shiftUp;
				$lastunit = "Map Revealer";
			}
			//bottom half of map (needs to invert Y for efficiency)
			else{
				$ycoord = Grid::$ydimension*32 - $ycoord;
			}
			
			//find which Y location is closest
			$y = (int)round( ($ycoord-1) / Grid::$resolution);
			$text .= Grid::$YLoc[$y]->centerOn(P12, $lastunit, $lastlocation);
			$lastlocation = Grid::$YLoc[$y];
			
			//center main
			$text .= Grid::$main->centerOn($lastlocation);


		}
		
		
		
		//Y is a deathcounter
		if( $ycoord instanceof Deathcounter ) {
			
			//if X was a consatnt, snap main to the nearest X coordinate (it wasn't there for efficiency's sake)
			if( is_numeric($xcoord) ) {
				$text .= Grid::$main->centerOn(P12, Grid::$unit, $lastlocation);
			}

			//Y temp so we don't have to use the argument passed in
			$ytemp = new TempDC(Map::getHeight()*32);
			
			//top half of map, just snap main to top half
			$text .= _if( $ycoord->atMost(Map::getHeight()*32/2 - 5) )->then(
				Grid::$shiftUp->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$shiftUp),
				$ytemp->setTo($ycoord),
			'');
			//bottom half of map; invert y for efficiency
			$text .= _if( $ycoord->atLeast(Map::getHeight()*32/2 - 4) )->then(
				$ytemp->setTo(Map::getHeight()*32-1),
				$ytemp->subtract($ycoord),
			'');
			
			//subtract the edge to start where the playing field starts
			$text .= $ytemp->subtract( (Map::getHeight()-Grid::$ydimension)/2*32 - 1 );
			
			//ignore for efficiency
			$ignore = new TempSwitch();
			$text .= $ignore->set();
			
			//snap main to closest y location
			for($i=Grid::$ydimension*32/Grid::$resolution/2; $i>0; $i--){
				$text .= _if( $ignore->is_set(), $ytemp->atLeast($i*Grid::$resolution-3) )->then(
					Grid::$YLoc[$i]->centerOn(Grid::$main),
					Grid::$main->centerOn(Grid::$YLoc[$i]),
					$ignore->clear(),
				'');
			}
			//if no location has been placed, it has to be YLoc[0]
			$text .= _if( $ignore->is_set() )->then(
				Grid::$YLoc[0]->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$YLoc[0]),
				$ignore->kill(),
			'');
			
			$text .= $ytemp->kill();
			
		}
		
		
		return $text;
		
	}
	
	
	
	
	
	
	
	// Function to snap Main to the input pixel
	public function putMain($xcoord, $ycoord, TempSwitch $success = null) {
		
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
		$horizSlide = '';
		$vertSlide = '';
		
		//check if the coordinate is on the playing field or not
		if($success !== null){
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
			else{
				$text .= $success->set();
			}
		}
		
		
		
		//location and unit to make putMainRes(const, const) more efficient
		$lastlocation = Grid::$origin;
		$lastunit = Grid::$unit;

		//X is a constant integer
		if( is_numeric($xcoord) ) {
			
			//xmove is a coordinate that starts at the grid's origin and counts over
			$xmove = (Grid::$xoffset+Grid::$xdimension)*32 - $xcoord;			
			if( $xmove > Grid::$xdimension*32 || $xmove < 0 )
				ERROR('X coordinate is out of the playing field');
			
			//make it so main is always within 4 pixels (this essentially rounds)
			$xmove += 3;
			
			//move 64*resolution left
			while( $xmove >= Grid::$resolution*64 ){
				$text .= Grid::$slideLeft64->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*64;
				$lastlocation = Grid::$slideLeft64;
			}
			//move 8*resolution left
			while( $xmove >= Grid::$resolution*8 ){
				$text .= Grid::$slideLeft8->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*8;
				$lastlocation = Grid::$slideLeft8;
			}
			//move 1*resolution left
			while( $xmove >= Grid::$resolution*1 ){
				$text .= Grid::$slideLeft1->centerOn(P12, Grid::$unit, $lastlocation);
				$xmove -= Grid::$resolution*1;
				$lastlocation = Grid::$slideLeft1;
			}
			
			//restore xmove to see how many pixels main should shift left or right (used at the bottom)
			$xmove -= 3;
			
			//pixel shift
			if ($xmove > 0)
				$horizSlide = Grid::$main->slideLeft($xmove);
			elseif ($xmove < 0)
				$horizSlide = Grid::$main->slideRight($xmove*-1);
			else
				$horizSlide = '';
							
		}
		
		
		
		//X is a deathcounter
		if( $xcoord instanceof Deathcounter ) {

			$currentMax = (Grid::$xoffset+Grid::$xdimension)*32+3;
			$xtemp = new TempDC($currentMax);
			
			//xtemp starts at grid origin then counts left
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
			
			//pixel shift
			$text .= _if($xtemp->exactly(7))->then(
				Grid::$main->slideLeft(4),
			'');
			$text .= _if($xtemp->exactly(6))->then(
				Grid::$main->slideLeft(3),
			'');
			$text .= _if($xtemp->exactly(5))->then(
				Grid::$main->slideLeft(2),
			'');
			$text .= _if($xtemp->exactly(4))->then(
				Grid::$main->slideLeft(1),
			'');
			$text .= _if($xtemp->exactly(2))->then(
				Grid::$main->slideRight(1),
			'');
			$text .= _if($xtemp->exactly(1))->then(
				Grid::$main->slideRight(2),
			'');
			$text .= _if($xtemp->exactly(0))->then(
				Grid::$main->slideRight(3),
			'');
			
			
			$text .= $xtemp->kill();
			$lastlocation = Grid::$main;
			$lastunit = "Map Revealer";
			
			

		}
		
		
		
		//Y is a constant
		if( is_numeric($ycoord) ) {
			
			//swap == 0 if top, == 1 if bottom
			$swap = 0;
			//start Y at playing field (subtract any edge)
			$ycoord -= (Map::getHeight()-Grid::$ydimension)/2*32;
			
			//error check
			if( $ycoord > Grid::$ydimension*32 || $ycoord < 0 )
				ERROR('Y coordinate is out of the playing field');

			//top half of map
			if( $ycoord < Grid::$ydimension*32/2 - 4 ){
				$text .= Grid::$shiftUp->centerOn(P12, $lastunit, $lastlocation);
				$lastlocation = Grid::$shiftUp;
				$lastunit = "Map Revealer";
			}
			//bottom half of map
			else{
				$ycoord = Grid::$ydimension*32 - $ycoord;
				$swap = 1;
			}
			
			//find which Y location is closest
			$y = (int)round( ($ycoord-1) / Grid::$resolution);
			$text .= Grid::$YLoc[$y]->centerOn(P12, $lastunit, $lastlocation);
			$lastlocation = Grid::$YLoc[$y];
			
			//manipulate ycoord to see how many pixels main should shift left or right (used at the bottom)
			$ycoord = ($ycoord+3) % Grid::$resolution;
			if( $swap == 0 ){ $ycoord -= 3; }
			else{ $ycoord = 4 - $ycoord; }
			
			//center main
			$text .= Grid::$main->centerOn($lastlocation);
			
			//pixel shift
			if ($ycoord > 0)
				$vertSlide = Grid::$main->slideDown($ycoord);
			elseif ($ycoord < 0)
				$vertSlide = Grid::$main->slideUp($ycoord*-1);
			else
				$vertSlide = '';


		}
		
		
		
		//Y is a deathcounter
		if( $ycoord instanceof Deathcounter ) {
			
			//if X was a constant, snap main to the nearest X coordinate (it wasn't there for efficiency's sake)
			if( is_numeric($xcoord) ) {
				$text .= Grid::$main->centerOn(P12, Grid::$unit, $lastlocation);
			}

			//Y temp so we don't have to use the argument passed in
			$ytemp = new TempDC(Map::getHeight()*32);
			$pixtemp = new TempDC(7);
			$bottom = new TempSwitch();
			
			//top half of map, just snap main to top half
			$text .= _if( $ycoord->atMost(Map::getHeight()*32/2 - 5) )->then(
				Grid::$shiftUp->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$shiftUp),
				$ytemp->setTo($ycoord),
			'');
			//bottom half of map; invert y for efficiency
			$text .= _if( $ycoord->atLeast(Map::getHeight()*32/2 - 4) )->then(
				$ytemp->setTo(Map::getHeight()*32-1),
				$ytemp->subtract($ycoord),
				$bottom->set(),
			'');
			
			//subtract the edge to start where the playing field starts
			$text .= $ytemp->subtract( (Map::getHeight()-Grid::$ydimension)/2*32 - 1 );
			
			//ignore for efficiency
			$ignore = new TempSwitch();
			$text .= $ignore->set();
			
			//snap main to closest y location
			for($i=Grid::$ydimension*32/Grid::$resolution/2; $i>0; $i--){
				$text .= _if( $ignore->is_set(), $ytemp->atLeast($i*Grid::$resolution-3) )->then(
					$ytemp->subtract($i*Grid::$resolution-3),
					Grid::$YLoc[$i]->centerOn(Grid::$main),
					Grid::$main->centerOn(Grid::$YLoc[$i]),
					$ignore->clear(),
				'');
			}
			//if no location has been placed, it has to be YLoc[0]
			$text .= _if( $ignore->is_set() )->then(
				$ytemp->add(3),
				Grid::$YLoc[0]->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$YLoc[0]),
				$ignore->kill(),
			'');
			
			//correct pixels
			$ytemp->Max(7);
			
			$text .= _if($bottom->is_clear())->then(
				$pixtemp->become($ytemp),
			'');
			$text .= _if($bottom->is_set())->then(
				$pixtemp->setTo(7),
				$pixtemp->subtractDel($ytemp),
				$pixtemp->add(1),
				$bottom->kill(),
			'');
			
			//pixel shift
			$text .= _if($pixtemp->exactly(8))->then(
				Grid::$main->slideDown(4),
			'');
			$text .= _if($pixtemp->exactly(7))->then(
				Grid::$main->slideDown(3),
			'');
			$text .= _if($pixtemp->exactly(6))->then(
				Grid::$main->slideDown(2),
			'');
			$text .= _if($pixtemp->exactly(5))->then(
				Grid::$main->slideDown(1),
			'');
			$text .= _if($pixtemp->exactly(3))->then(
				Grid::$main->slideUp(1),
			'');
			$text .= _if($pixtemp->exactly(2))->then(
				Grid::$main->slideUp(2),
			'');
			$text .= _if($pixtemp->exactly(1))->then(
				Grid::$main->slideUp(3),
			'');
			$text .= _if($pixtemp->exactly(0))->then(
				Grid::$main->slideUp(4),
			'');
			
			$ytemp->kill();
			$text .= $pixtemp->kill();
			
		}
		
		
		$text .= $horizSlide;
		$text .= $vertSlide;

		return $text;

	}
	

}


