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
	
	static public $detectX1;

	/* @var LirinLocation[] */
	static public $saveLoc = array();

	static public $sandbox;
	static public $origin;
	static public $shiftLeft;
	static public $shiftUp;

	/* @var LirinLocation[] */
	static public $YLoc = array();
	/* @var LirinLocation[] */
	static public $pixX = array();
	/* @var LirinLocation[] */
	static public $pixY = array();
	
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
		
		for($i=0;$i<=$y_dimension*32/$resolution/2;$i++){
			LocationManager::MintLocation("YLoc$i", 0,0 , 0, $vert*32*2+$i*$resolution*2);
			self::$YLoc[] = new LirinLocation("YLoc$i");
		}
		
		/**/
		LocationManager::MintLocation("SlideLeft1", 62-$resolution*2*1, 0, 0, 0);
		self::$slideLeft1 = new LirinLocation("SlideLeft1");
		LocationManager::MintLocation("SlideLeft8", 0, 0, $resolution*2*8-62, 0);
		self::$slideLeft8 = new LirinLocation("SlideLeft8");
		LocationManager::MintLocation("SlideLeft64", 0, 0, $resolution*2*64-62, 0);
		self::$slideLeft64 = new LirinLocation("SlideLeft64");
		
		$i=$y_dimension*32/$resolution/2;
		self::$detectX1 = new LirinLocation("YLoc$i");
		
		for($i=1;$i<$resolution;$i++){
			LocationManager::MintLocation("pixX$i", $i*2, 0, 0, Map::getHeight()*32);
			self::$pixX[] = new LirinLocation("pixX$i");
		}
		for($i=1;$i<$resolution;$i++){
			LocationManager::MintLocation("pixY$i", 0, $i*2, 0, 0);
			self::$pixY[] = new LirinLocation("pixY$i");
		}
		
		self::$saveLoc[0] = self::$pixY[0];
		self::$saveLoc[1] = self::$pixY[1];
		self::$saveLoc[2] = self::$pixY[2];
		self::$saveLoc[3] = self::$pixY[3];
		self::$saveLoc[4] = self::$pixY[4];
		self::$saveLoc[5] = self::$pixY[5];
		self::$saveLoc[6] = self::$pixY[6];
		self::$saveLoc[7] = self::$slideLeft1;
		self::$saveLoc[8] = self::$slideLeft8;
		self::$saveLoc[9] = self::$slideLeft64;
		
		LocationManager::MintLocationWithIndex("main", 8, 8, 8, 8, 147);
		self::$main = new ExtendableLocation("main", 4);
		
	}
	
	

	
	
	
	/////
	//ACTIONS
	//
	
	
	
	//scan for the unit
	static function scan(BSUnit $unit) {
		
		$ignore = new TempSwitch();
		$text = '';
		
		//clear values for scan
		$text .= ModifyHealth($unit->Player, Men, All, "Anywhere", 100);
		
		//X SCAN
		
		$xbegin = (Grid::$xoffset+Grid::$xdimension)*32;
		
		//find 2-tile
		$text .= _if( $unit->currentXCoordinate(AtLeast, $xbegin-Grid::$resolution*2-Grid::$resolution*8) )->then(
			Grid::$slideLeft1->centerOn(Grid::$origin),
			Grid::$detectX1->centerOn(Grid::$slideLeft1),
			ModifyHealth($unit->Player, Men, All, Grid::$detectX1, 90),
			$unit->x->setTo($xbegin),
		    $ignore->set(),
		'');
		for($i=1; $i<Grid::$xdimension/2; $i++){
			$j=$i;
			$snap = '';
			$lastlocation = Grid::$origin;
			//snap slideLeft64
			while( $j >= 8 ){
				$snap .= Grid::$slideLeft64->centerOn(P12, Grid::$unit, $lastlocation);
				$j -= 8;
				$lastlocation = Grid::$slideLeft64;
			}
			//snap slideLeft8
			while( $j >= 1 ){
				$snap .= Grid::$slideLeft8->centerOn(P12, Grid::$unit, $lastlocation);
				$j -= 1;
				$lastlocation = Grid::$slideLeft8;
			}
			//snap slideLeft1
			$snap .= Grid::$slideLeft1->centerOn(P12, Grid::$unit, $lastlocation);
			
			$text .= _if( $ignore->is_clear(), $unit->currentXCoordinate(AtLeast, $xbegin-Grid::$resolution*2-Grid::$resolution*8*($i+1)) )->then(
				$snap,
				Grid::$detectX1->centerOn(Grid::$slideLeft1),
				ModifyHealth($unit->Player, Men, All, Grid::$detectX1, 90),
				$unit->x->setTo($xbegin-Grid::$resolution*8*$i),
			    $ignore->set(),
			'');
		}
		
		
		//resolution scan
		for($i=0; $i<9; $i++){
			$text .= _if( $unit->health(AtLeast, 10000) )->then(
				Grid::$slideLeft1->centerOn(P12, Grid::$unit, Grid::$slideLeft1),
				Grid::$detectX1->centerOn(Grid::$slideLeft1),
				ModifyHealth($unit->Player, Men, All, Grid::$detectX1, 90),
				$unit->x->subtract(Grid::$resolution),
			'');
		}
		$text .= _if( $unit->health(AtLeast, 10000) )->then(
			Grid::$slideLeft1->centerOn(P12, Grid::$unit, Grid::$slideLeft1),
			Grid::$detectX1->centerOn(Grid::$slideLeft1),
			$unit->x->subtract(Grid::$resolution),
		'');
		
		
		//scan X pixel
		$text .= $ignore->clear();
		$text .= Grid::$main->centerOn(Grid::$slideLeft1);
		$text .= Grid::$pixX[0]->centerOn(Grid::$slideLeft1);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$pixX[0], 60);
		
		for($i=1; $i<7; $i++){
			$text .= _if( $unit->health(AtMost, (61-$i)*100) )->then(
				Grid::$pixX[$i]->centerOn(Grid::$detectX1),
				ModifyHealth($unit->Player, Men, All, Grid::$pixX[$i], 60-$i),
				$unit->x->add(1),
			'');
		}
		$text .= _if( $unit->health(AtMost, 5400) )->then(
			$unit->x->add(1),
		'');
		
		
		
		//Y SCAN
		$ybegin = (Map::getHeight()-Grid::$ydimension)*32/2;
		
		//find 2-tile
		$text .= _if( $unit->currentYCoordinate(AtMost, $ybegin+Grid::$resolution*8) )->then(
			Grid::$shiftUp->centerOn(Grid::$main),
			Grid::$YLoc[0]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[1]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[2]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[3]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[4]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[5]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[6]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[7]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[8]->centerOn(Grid::$shiftUp),
			Grid::$main->centerOn(Grid::$YLoc[0]),
			Grid::$saveLoc[0]->centerOn(Grid::$YLoc[1]),
			Grid::$saveLoc[1]->centerOn(Grid::$YLoc[2]),
			Grid::$saveLoc[2]->centerOn(Grid::$YLoc[3]),
			Grid::$saveLoc[3]->centerOn(Grid::$YLoc[4]),
			Grid::$saveLoc[4]->centerOn(Grid::$YLoc[5]),
			Grid::$saveLoc[5]->centerOn(Grid::$YLoc[6]),
			Grid::$saveLoc[6]->centerOn(Grid::$YLoc[7]),
			Grid::$saveLoc[7]->centerOn(Grid::$YLoc[8]),
			ModifyHealth($unit->Player, Men, All, Grid::$main, 40),
			$unit->y->setTo($ybegin),
		    $ignore->set(),
		'');
		for($i=1; $i<Grid::$ydimension/2/2; $i++){
			$text .= _if( $ignore->is_clear(), $unit->currentYCoordinate(AtMost, $ybegin+Grid::$resolution*8*($i+1)) )->then(
				Grid::$shiftUp->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-2]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8-1]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+0]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+1]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+2]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+3]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+4]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+5]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+6]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+7]->centerOn(Grid::$shiftUp),
				Grid::$YLoc[$i*8+8]->centerOn(Grid::$shiftUp),
				Grid::$main->centerOn(Grid::$YLoc[$i*8-2]),
				Grid::$saveLoc[0]->centerOn(Grid::$YLoc[$i*8-1]),
				Grid::$saveLoc[1]->centerOn(Grid::$YLoc[$i*8+0]),
				Grid::$saveLoc[2]->centerOn(Grid::$YLoc[$i*8+1]),
				Grid::$saveLoc[3]->centerOn(Grid::$YLoc[$i*8+2]),
				Grid::$saveLoc[4]->centerOn(Grid::$YLoc[$i*8+3]),
				Grid::$saveLoc[5]->centerOn(Grid::$YLoc[$i*8+4]),
				Grid::$saveLoc[6]->centerOn(Grid::$YLoc[$i*8+5]),
				Grid::$saveLoc[7]->centerOn(Grid::$YLoc[$i*8+6]),
				Grid::$saveLoc[8]->centerOn(Grid::$YLoc[$i*8+7]),
				Grid::$saveLoc[9]->centerOn(Grid::$YLoc[$i*8+8]),
				ModifyHealth($unit->Player, Men, All, Grid::$main, 40),
				$unit->y->setTo($ybegin+Grid::$resolution*8*$i-Grid::$resolution*2),
			    $ignore->set(),
			'');
		}
		$text .= _if( $ignore->is_clear(), $unit->currentYCoordinate(AtMost, $ybegin+Grid::$resolution*8*($i+1)) )->then(
			Grid::$shiftUp->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-2]->centerOn(Grid::$shiftUp),
			Grid::$YLoc[$i*8-1]->centerOn(Grid::$shiftUp),
			Grid::$saveLoc[1]->centerOn(Grid::$YLoc[$i*8-2]),
			Grid::$saveLoc[0]->centerOn(Grid::$YLoc[$i*8-1]),
			//
			Grid::$YLoc[$i*8-0]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-1]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-2]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-3]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-4]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-5]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-6]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-7]->centerOn(Grid::$main),
			Grid::$YLoc[$i*8-8]->centerOn(Grid::$main),
			Grid::$main->centerOn(Grid::$saveLoc[1]),
			Grid::$saveLoc[1]->centerOn(Grid::$YLoc[$i*8-0]),
			Grid::$saveLoc[2]->centerOn(Grid::$YLoc[$i*8-1]),
			Grid::$saveLoc[3]->centerOn(Grid::$YLoc[$i*8-2]),
			Grid::$saveLoc[4]->centerOn(Grid::$YLoc[$i*8-3]),
			Grid::$saveLoc[5]->centerOn(Grid::$YLoc[$i*8-4]),
			Grid::$saveLoc[6]->centerOn(Grid::$YLoc[$i*8-5]),
			Grid::$saveLoc[7]->centerOn(Grid::$YLoc[$i*8-6]),
			Grid::$saveLoc[8]->centerOn(Grid::$YLoc[$i*8-7]),
			Grid::$saveLoc[9]->centerOn(Grid::$YLoc[$i*8-8]),
			ModifyHealth($unit->Player, Men, All, Grid::$main, 40),
			$unit->y->setTo($ybegin+Grid::$resolution*8*$i-Grid::$resolution*2),
		    $ignore->set(),
		'');
		$ybegin = Map::getHeight()*32-$ybegin;
		for($i=(int)round(Grid::$ydimension/2/2-1); $i>0; $i--){
			$text .= _if( $ignore->is_clear(), $unit->currentYCoordinate(AtMost, $ybegin-Grid::$resolution*8*($i-1)) )->then(
				Grid::$YLoc[$i*8+2]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8+1]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-0]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-1]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-2]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-3]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-4]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-5]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-6]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-7]->centerOn(Grid::$main),
				Grid::$YLoc[$i*8-8]->centerOn(Grid::$main),
				Grid::$main->centerOn(Grid::$YLoc[$i*8+2]),
				Grid::$saveLoc[0]->centerOn(Grid::$YLoc[$i*8+1]),
				Grid::$saveLoc[1]->centerOn(Grid::$YLoc[$i*8-0]),
				Grid::$saveLoc[2]->centerOn(Grid::$YLoc[$i*8-1]),
				Grid::$saveLoc[3]->centerOn(Grid::$YLoc[$i*8-2]),
				Grid::$saveLoc[4]->centerOn(Grid::$YLoc[$i*8-3]),
				Grid::$saveLoc[5]->centerOn(Grid::$YLoc[$i*8-4]),
				Grid::$saveLoc[6]->centerOn(Grid::$YLoc[$i*8-5]),
				Grid::$saveLoc[7]->centerOn(Grid::$YLoc[$i*8-6]),
				Grid::$saveLoc[8]->centerOn(Grid::$YLoc[$i*8-7]),
				Grid::$saveLoc[9]->centerOn(Grid::$YLoc[$i*8-8]),
				ModifyHealth($unit->Player, Men, All, Grid::$main, 40),
				$unit->y->setTo($ybegin-Grid::$resolution*8*$i-Grid::$resolution*2),
			    $ignore->set(),
			'');
		}
		
		//resolution scan
		$tempDC = new TempDC();
		for($i=0; $i<9; $i++){
			$text .= _if( $unit->health(AtLeast, 5000) )->then(
			    Grid::$main->centerOn(Grid::$saveLoc[$i]),
				ModifyHealth($unit->Player, Men, All, Grid::$main, 40),
				$unit->y->add(Grid::$resolution),
				$tempDC->add(1),
			'');
		}
		$text .= _if( $unit->health(AtLeast, 5000) )->then(
		    Grid::$main->centerOn(Grid::$saveLoc[$i]),
			$unit->y->add(Grid::$resolution),
			$tempDC->add(1),
		'');
		
		//correct for bottom of map
		$text .= _if( $unit->currentYCoordinate(AtLeast, Map::getHeight()*32/2-Grid::$resolution*8+1), $unit->currentYCoordinate(AtMost, Map::getHeight()*32/2), $tempDC->atLeast(10) )->then(
			$unit->y->subtract(1),
		'');
		$text .= _if( $unit->currentYCoordinate(AtLeast, Map::getHeight()*32/2+1), $unit->currentYCoordinate(AtMost, Map::getHeight()*32/2+Grid::$resolution*8), $tempDC->atLeast(2) )->then(
			$unit->y->subtract(1),
		'');
		$text .= _if( $unit->currentYCoordinate(AtLeast, Map::getHeight()*32/2+Grid::$resolution*8+1) )->then(
			$unit->y->subtract(1),
		'');
		
		//scan Y pixel
		$text .= Grid::$pixY[0]->centerOn(Grid::$main);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$pixY[0], 20);
		
		for($i=1; $i<7; $i++){
			$text .= _if( $unit->health(AtMost, (21-$i)*100) )->then(
				Grid::$pixY[$i]->centerOn(Grid::$main),
				ModifyHealth($unit->Player, Men, All, Grid::$pixY[$i], 20-$i),
				$unit->y->subtract(1),
			'');
		}
		$text .= _if( $unit->health(AtMost, 1400) )->then(
			$unit->y->subtract(1),
		'');
		
		
		//UNIT TYPE
		global $unitdata;
		
		foreach($unit->getTypes() as $type){
			$text .= _if( $unit->type->exactly($type->ID) )->then(
		        $unit->x->subtract($unitdata[$type->BaseUnit]["right"]),
			    $unit->y->add($unitdata[$type->BaseUnit]["up"]),
			'');
		}
		
		//restore and end
		$text .= $ignore->kill();
		$text .= $tempDC->kill();
		$text .= $unit->Location->centerOn($unit->Player, Men, Grid::$main);
		$text .= ModifyHealth($unit->Player, Men, All, "Anywhere", 100);
		
		return $text;
		
	}
	
	
	
	//put main closest to coordinate using the resolution (no pixel shifts)
	static function putMainRes($xcoord, $ycoord, TempSwitch $success = null) {
	
		//ERROR
		if( func_num_args() != 2 && func_num_args() != 3 ){
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
			/* @var Deathcounter $xcoord */
			/* @var Deathcounter $ycoord */
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
			/* @var int $xcoord */
			
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
			/* @var Deathcounter $xcoord */

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
			/* @var int $ycoord */
			
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
			/* @var Deathcounter $ycoord */
			
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
			for($i=(int)round(Grid::$ydimension*32/Grid::$resolution/2); $i>0; $i--){
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
	static public function putMain($xcoord, $ycoord, TempSwitch $success = null) {
		
		//ERROR
		if( func_num_args() != 2 && func_num_args() != 3 ){
			Error('COMPILER ERROR FOR PUTMAIN(): INCORRECT NUMBER OF ARGUMENTS (NEEDS 3: DEATHCOUNTER OR CONSTANT (INTEGER), DEATHCOUNTER OR CONSTANT (INTEGER), SWITCH)');
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
			/* @var Deathcounter $xcoord */
			/* @var Deathcounter $ycoord */
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
			/* @var int $xcoord */
			
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
			/* @var Deathcounter $xcoord */

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
			/* @var int $ycoord */
			
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
			/* @var Deathcounter $ycoord */
			
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
	
	
	
	
	
	/**
	//scan for the unit
	static function scan(BSUnit $unit) {
		
		$text = '';
		
		//clear values for scan
		$text .= $unit->x->setTo((Grid::$xoffset+Grid::$xdimension)*32);
		$text .= $unit->y->setTo(0);
		$text .= ModifyHealth($unit->Player, Men, All, "Anywhere", 100);
		
		//X SCAN
		
		//scan X 64
		$text .= Grid::$detectX64->centerOn(Grid::$origin);
		$text .= Grid::$slideLeft64->centerOn(Grid::$origin);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$detectX64, 90);
		
		for($i=1; $i<Grid::$xdimension*32/(Grid::$resolution*64)-1; $i++){
			$text .= _if( $unit->health(Exactly, 10000) )->then(
				Grid::$detectX64->centerOn(P12, Grid::$unit, Grid::$slideLeft64),
				Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64),
				ModifyHealth($unit->Player, Men, All, Grid::$detectX64, 90),
				$unit->x->subtract(Grid::$resolution*64),
			'');
		}
		$text .= _if( $unit->health(Exactly, 10000) )->then(
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64),
			$unit->x->subtract(Grid::$resolution*64),
		'');
		
		//scan X 8
		$text .= Grid::$detectX8->centerOn(Grid::$slideLeft64);
		$text .= Grid::$slideLeft8->centerOn(Grid::$slideLeft64);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$detectX8, 80);
		
		for($i=0; $i<6; $i++){
			$text .= _if( $unit->health(AtLeast, 9000) )->then(
				Grid::$detectX8->centerOn(P12, Grid::$unit, Grid::$slideLeft8),
				Grid::$slideLeft8->centerOn(P12, Grid::$unit, Grid::$slideLeft8),
				ModifyHealth($unit->Player, Men, All, Grid::$detectX8, 80),
				$unit->x->subtract(Grid::$resolution*8),
			'');
		}
		$text .= _if( $unit->health(AtLeast, 9000) )->then(
			Grid::$slideLeft8->centerOn(P12, Grid::$unit, Grid::$slideLeft8),
			$unit->x->subtract(Grid::$resolution*8),
		'');
		
		//scan X 1
		$text .= Grid::$detectX1->centerOn(Grid::$slideLeft8);
		$text .= Grid::$slideLeft1->centerOn(Grid::$slideLeft8);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$detectX1, 70);

		for($i=0; $i<8; $i++){
			$text .= _if( $unit->health(AtLeast, 8000) )->then(
				Grid::$detectX1->centerOn(P12, Grid::$unit, Grid::$slideLeft1),
				Grid::$slideLeft1->centerOn(P12, Grid::$unit, Grid::$slideLeft1),
				ModifyHealth($unit->Player, Men, All, Grid::$detectX1, 70),
				$unit->x->subtract(Grid::$resolution*1),
			'');
		}
		$text .= _if( $unit->health(AtLeast, 8000) )->then(
			Grid::$slideLeft1->centerOn(P12, Grid::$unit, Grid::$slideLeft1),
			$unit->x->subtract(Grid::$resolution*1),
		'');
		
		//scan X pixel
		$text .= Grid::$main->centerOn(Grid::$slideLeft1);
		$text .= Grid::$pixX[0]->centerOn(Grid::$slideLeft1);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$pixX[0], 60);
		
		for($i=1; $i<7; $i++){
			$text .= _if( $unit->health(AtMost, (61-$i)*100) )->then(
				Grid::$pixX[$i]->centerOn(Grid::$detectX1),
				ModifyHealth($unit->Player, Men, All, Grid::$pixX[$i], 60-$i),
				$unit->x->add(1),
			'');
		}
		$text .= _if( $unit->health(AtMost, 5400) )->then(
			$unit->x->add(1),
		'');
		
		
		
		
		//Y SCAN
		$tempY = new TempDC(Grid::$ydimension*32/2);
		
		//switch Y
		$text .= _if( $unit->currentYCoordinate(AtMost, Map::getHeight()*32/2-1) )->then(
			Grid::$shiftUp->centerOn(Grid::$main),
			Grid::$main->centerOn(Grid::$shiftUp),
		'');
		
		//scan Y 32
		$text .= Grid::$YLoc[0]->centerOn(Grid::$main);
		$text .= Grid::$detectY32->centerOn(Grid::$YLoc[0]);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$detectY32, 50);
		
		for($i=1; $i<floor(Grid::$ydimension/2/Grid::$resolution)-1; $i++){
			$text .= _if( $unit->health(AtLeast, 5100) )->then(
				Grid::$YLoc[32*$i]->centerOn(Grid::$main),
				Grid::$detectY32->centerOn(Grid::$YLoc[32*$i]),
				ModifyHealth($unit->Player, Men, All, Grid::$detectY32, 50),
				$tempY->add(Grid::$resolution*32),
			'');
		}
		
		$text .= _if( $unit->health(AtLeast, 5100) )->then(
			Grid::$YLoc[32*$i]->centerOn(Grid::$main),
			Grid::$detectY32->centerOn(Grid::$YLoc[32*$i]),
			$tempY->add(Grid::$resolution*32),
		'');
		
		
		//scan Y 8
		$text .= Grid::$detectY8->centerOn(Grid::$detectY32);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$detectY8, 40);
		
		for($i=1; $i<Grid::$ydimension*32/(Grid::$resolution*2*8); $i++){
			if($i%4==0){ }
			elseif($i%4==3){
				$text .= _if( $tempY->exactly(($i-1)*Grid::$resolution*8), $unit->health(AtLeast, 5000) )->then(
					Grid::$YLoc[$i*8]->centerOn(Grid::$main),
					Grid::$detectY8->centerOn(Grid::$YLoc[$i*8]),
					$tempY->add(Grid::$resolution*8),
				'');
			}
			else{
				$text .= _if( $tempY->exactly(($i-1)*Grid::$resolution*8), $unit->health(AtLeast, 5000) )->then(
					Grid::$YLoc[$i*8]->centerOn(Grid::$main),
					Grid::$detectY8->centerOn(Grid::$YLoc[$i*8]),
				    ModifyHealth($unit->Player, Men, All, Grid::$detectY8, 40),
					$tempY->add(Grid::$resolution*8),
				'');
			}
		}
		
		
		//scan Y 1
		$text .= Grid::$detectY1->centerOn(Grid::$detectY8);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$detectY1, 30);
		 
		for($i=1; $i<Grid::$ydimension*32/(Grid::$resolution*2); $i++){
			$text .= _if( $tempY->exactly(($i-1)*Grid::$resolution), $unit->health(AtLeast, 4000) )->then(
				Grid::$YLoc[$i]->centerOn(Grid::$main),
				Grid::$detectY1->centerOn(Grid::$YLoc[$i]),
			    ModifyHealth($unit->Player, Men, All, Grid::$detectY1, 30),
				$tempY->add(Grid::$resolution),
			'');
		}
		$text .= _if( $tempY->exactly(($i-1)*Grid::$resolution), $unit->health(AtLeast, 4000), $unit->currentYCoordinate(AtMost, Map::getHeight()*32/2-1) )->then(
			Grid::$YLoc[$i]->centerOn(Grid::$main),
			Grid::$detectY1->centerOn(Grid::$YLoc[$i]),
			$tempY->add(Grid::$resolution-1),
		'');
		$text .= _if( $tempY->exactly(($i-1)*Grid::$resolution), $unit->health(AtLeast, 4000), $unit->currentYCoordinate(AtLeast, Map::getHeight()*32/2) )->then(
			Grid::$YLoc[$i]->centerOn(Grid::$main),
			Grid::$detectY1->centerOn(Grid::$YLoc[$i]),
			$tempY->add(Grid::$resolution),
		'');
		
		
		//scan Y pixel
		$text .= Grid::$pixY[0]->centerOn(Grid::$detectY1);
		$text .= ModifyHealth($unit->Player, Men, All, Grid::$pixY[0], 20);
		
		for($i=1; $i<7; $i++){
			$text .= _if( $unit->health(AtMost, (21-$i)*100) )->then(
				Grid::$pixY[$i]->centerOn(Grid::$detectY1),
				ModifyHealth($unit->Player, Men, All, Grid::$pixY[$i], 20-$i),
				$tempY->subtract(1),
			'');
		}
		$text .= _if( $unit->health(AtMost, 1400) )->then(
			$tempY->subtract(1),
		'');
		
		
		//UNIT TYPE
		global $unitdata;
		$text .= _if( $unit->currentYCoordinate(AtMost, Map::getHeight()*32/2-1) )->then(
		    _if( $unit->type->exactly(0) )->then(
		        $unit->x->subtract($unitdata["Protoss Zealot"]["right"]),
			    $tempY->add($unitdata["Protoss Zealot"]["up"]),
		    ''),
			//others
		'');
		$text .= _if( $unit->currentYCoordinate(AtLeast, Map::getHeight()*32/2) )->then(
		    _if( $unit->type->exactly(0) )->then(
		        $unit->x->subtract($unitdata["Protoss Zealot"]["right"]),
			    $tempY->add($unitdata["Protoss Zealot"]["down"]),
		    ''),
			//others
		'');
		
		
		//correct for bottom of map
		$text .= _if( $unit->currentYCoordinate(AtMost, Map::getHeight()*32/2-1) )->then(
			$unit->y->become($tempY),
			$unit->y->add((Map::getHeight()-Grid::$ydimension)/2*32),
		'');
		$text .= _if( $unit->currentYCoordinate(AtLeast, Map::getHeight()*32/2) )->then(
			$unit->y->setTo(Map::getHeight()*32-(Map::getHeight()-Grid::$ydimension)/2*32-1),
			$unit->y->subtractDel($tempY),
		'');
		

		
		//restore and end
		$text .= $tempY->kill();
		$text .= $unit->Loc->centerOn($unit->Player, Men, Grid::$main);
		$text .= ModifyHealth($unit->Player, Men, All, "Anywhere", 100);
		
		return $text;
		
	}
	/**/
	

}


