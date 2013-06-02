<?php

class FRAGS{

	/* @var Deathcounter */ static public $x;
	/* @var Deathcounter */ static public $y;
	
	/* @var Deathcounter */ static public $Fragged;

	/* @var PermSwitch */ static private $movedP4;
	/* @var PermSwitch */ static private $movedP5;
	/* @var PermSwitch */ static private $movedP6;
	
	/* @var IndexedUnit[] */ static private $scourgeP4 = array();
	/* @var IndexedUnit[] */ static private $scourgeP5 = array();
	/* @var IndexedUnit[] */ static private $scourgeP6 = array();
	
	static $P4nodes = array();
	static $P5nodes = array();
	static $P6nodes = array();
	
	function __construct(){
		
		// Switches
		self::$movedP4 = new PermSwitch();
		self::$movedP5 = new PermSwitch();
		self::$movedP6 = new PermSwitch();
		
		
		$humans = new Player(P4, P5, P6);
		self::$Fragged = new Deathcounter($humans, 1);
		
		
		// Deathcounters
		$humans = new Player(P4, P5, P6);
		self::$x = new Deathcounter($humans, Map::getWidth()*32-1);
		self::$y = new Deathcounter($humans, Map::getHeight()*32-1);
		
		
		// Place FRAGS units
		/*
		$k = 32;
		for($j=0; $j<4; $j++){
			if($j==2){ $k = 0; }
			for($i=0; $i<5; $i++){
				if(($i+$j)%2 == 0){
					self::$scourgeP4[] = new IndexedUnit(UnitManager::MintUnitWithAnyIndex("Zerg Scourge", P12, 1008+1024*$i-$k, 496+1024*$j, Invincible), "Zerg Scourge", P4, "Anywhere");
					UnitManager::MintUnit("Infested Terran", P9, 1008+1024*$i-$k, 496+1024*$j, array(Invincible, Burrowed));
					self::$scourgeP5[] = new IndexedUnit(UnitManager::MintUnitWithAnyIndex("Zerg Scourge", P12, 1072+1024*$i-$k, 496+1024*$j, Invincible), "Zerg Scourge", P5, "Anywhere");
					UnitManager::MintUnit("Infested Terran", P10, 1072+1024*$i-$k, 496+1024*$j, array(Invincible, Burrowed));
					self::$scourgeP6[] = new IndexedUnit(UnitManager::MintUnitWithAnyIndex("Zerg Scourge", P12, 1008+1024*$i-$k, 560+1024*$j, Invincible), "Zerg Scourge", P6, "Anywhere");
					UnitManager::MintUnit("Infested Terran", P11, 1008+1024*$i-$k, 560+1024*$j, array(Invincible, Burrowed));
				}
			}
		}
		*/
		
		$index = 0;
		
		$k = 32;
		for($j=0; $j<4; $j++){
			if($j==2){ $k = 0; }
			for($i=0; $i<5; $i++){
				if(($i+$j)%2 == 0){
					self::$P4nodes[$index]["node"] = array("x" => 1008+1024*$i-$k, "y" => 496+1024*$j);
					self::$P5nodes[$index]["node"] = array("x" => 1072+1024*$i-$k, "y" => 496+1024*$j);
					self::$P6nodes[$index]["node"] = array("x" => 1008+1024*$i-$k, "y" => 560+1024*$j);
					$index++;
				}
			}
		}
		
		$index = 0;
		
		for($x=0;$x<=1;$x++){
			for($y=0;$y<=4;$y++){
				self::$P4nodes[$index]["spawn"] = array("x" => 4528+$x*64,       "y" => 624+$y*64);
				self::$P5nodes[$index]["spawn"] = array("x" => 4528+$x*64+5*32,  "y" => 624+$y*64);
				self::$P6nodes[$index]["spawn"] = array("x" => 4528+$x*64+10*32, "y" => 624+$y*64);
				$index++;
			}
		}
		
		for($i=0; $i<=9; $i++){
			self::$scourgeP4[] = new IndexedUnit(UnitManager::MintUnitWithAnyIndex("Zerg Scourge", P12, self::$P4nodes[$i]["spawn"]["x"], self::$P4nodes[$i]["spawn"]["y"], Invincible), "Zerg Scourge", P4, "Anywhere");
			UnitManager::MintUnit("Infested Terran", P9,  self::$P4nodes[$i]["node"]["x"], self::$P4nodes[$i]["node"]["y"], array(Invincible, Burrowed));
			self::$scourgeP5[] = new IndexedUnit(UnitManager::MintUnitWithAnyIndex("Zerg Scourge", P12, self::$P5nodes[$i]["spawn"]["x"], self::$P5nodes[$i]["spawn"]["y"], Invincible), "Zerg Scourge", P5, "Anywhere");
			UnitManager::MintUnit("Infested Terran", P10, self::$P5nodes[$i]["node"]["x"], self::$P5nodes[$i]["node"]["y"], array(Invincible, Burrowed));
			self::$scourgeP6[] = new IndexedUnit(UnitManager::MintUnitWithAnyIndex("Zerg Scourge", P12, self::$P6nodes[$i]["spawn"]["x"], self::$P6nodes[$i]["spawn"]["y"], Invincible), "Zerg Scourge", P6, "Anywhere");
			UnitManager::MintUnit("Infested Terran", P11, self::$P6nodes[$i]["node"]["x"], self::$P6nodes[$i]["node"]["y"], array(Invincible, Burrowed));
		}
		
		// Give FRAGS units
		
		
	}
	
	

	function moveScourges($player){
		$text = '';
		
		$nodes = array();
		$nodeplayer = '';
		if($player === P4){ $nodes = self::$P4nodes; $nodeplayer = P9; }
		if($player === P5){ $nodes = self::$P5nodes; $nodeplayer = P10; }
		if($player === P6){ $nodes = self::$P6nodes; $nodeplayer = P11; }
		
		for($i=0; $i<=9; $i++){
			$nodex = $nodes[$i]["node"]["x"];
			$nodey = $nodes[$i]["node"]["y"];
			if($nodex < 1024){ $nodex = 1024; }
			if($nodex > 5120){ $nodex = 5120; }
			if($nodey < 512 ){ $nodey = 512;  }
			if($nodey > 3584){ $nodey = 3584; }
			$text .= _if( Always() )->then(
				Loc::$aoe2x2->placeAt($nodes[$i]["spawn"]["x"], $nodes[$i]["spawn"]["y"]),
				Loc::$aoe5x5->placeAt($nodex,  $nodey),
				Loc::$aoe5x5->centerOn($nodeplayer, "Infested Terran", Loc::$aoe5x5),
				MoveUnit($player, "Zerg Scourge", 1, Loc::$aoe2x2, Loc::$aoe5x5),
			'');
		}
		
		return $text;
	}
	
	function giveScourges($player){
		$text = '';
		
		$nodes = array();
		if($player === P4){ $nodes = self::$P4nodes; }
		if($player === P5){ $nodes = self::$P5nodes; }
		if($player === P6){ $nodes = self::$P6nodes; }
		
		for($i=0; $i<=9; $i++){
			$text .= _if( Always() )->then(
				Loc::$main->placeAt($nodes[$i]["spawn"]["x"], $nodes[$i]["spawn"]["y"]),
				Give(P12, "Zerg Scourge", 1, $player, Loc::$main),
			'');
			
		}
		
		return $text;
	}
	
	
	function CreateEngine(){
		$P1 = new Player(P1);
		$P4 = new Player(P4);
		$P5 = new Player(P5);
		$P6 = new Player(P6);
		$humans = new Player(P4, P5, P6);
		
		$ready = new Deathcounter($humans, 1);
		
		$P4->justonce( $this->giveScourges(P4) );
		$P5->justonce( $this->giveScourges(P5) );
		$P6->justonce( $this->giveScourges(P6) );
		
		$P1->_if( $ready->P4->exactly(0) )->then( $ready->P4->setTo(1) );
		$P1->_if( $ready->P5->exactly(0) )->then( $ready->P5->setTo(1) );
		$P1->_if( $ready->P6->exactly(0) )->then( $ready->P6->setTo(1) );
		
		$P1->_if( $ready->P4->exactly(1),
			self::$scourgeP4[0]->orderCoordinate(AtMost, 0), self::$scourgeP4[1]->orderCoordinate(AtMost, 0), 
			self::$scourgeP4[2]->orderCoordinate(AtMost, 0), self::$scourgeP4[3]->orderCoordinate(AtMost, 0), 
			self::$scourgeP4[4]->orderCoordinate(AtMost, 0), self::$scourgeP4[5]->orderCoordinate(AtMost, 0), 
			self::$scourgeP4[6]->orderCoordinate(AtMost, 0), self::$scourgeP4[7]->orderCoordinate(AtMost, 0), 
			self::$scourgeP4[8]->orderCoordinate(AtMost, 0), self::$scourgeP4[9]->orderCoordinate(AtMost, 0) 
		)->then(
			$ready->P4->setTo(0),
		'');
		
		$P1->_if( $ready->P5->exactly(1),
			self::$scourgeP5[0]->orderCoordinate(AtMost, 0), self::$scourgeP5[1]->orderCoordinate(AtMost, 0), 
			self::$scourgeP5[2]->orderCoordinate(AtMost, 0), self::$scourgeP5[3]->orderCoordinate(AtMost, 0), 
			self::$scourgeP5[4]->orderCoordinate(AtMost, 0), self::$scourgeP5[5]->orderCoordinate(AtMost, 0), 
			self::$scourgeP5[6]->orderCoordinate(AtMost, 0), self::$scourgeP5[7]->orderCoordinate(AtMost, 0), 
			self::$scourgeP5[8]->orderCoordinate(AtMost, 0), self::$scourgeP5[9]->orderCoordinate(AtMost, 0) 
		)->then(
			$ready->P5->setTo(0),
		'');
		
		$P1->_if( $ready->P6->exactly(1),
			self::$scourgeP6[0]->orderCoordinate(AtMost, 0), self::$scourgeP6[1]->orderCoordinate(AtMost, 0), 
			self::$scourgeP6[2]->orderCoordinate(AtMost, 0), self::$scourgeP6[3]->orderCoordinate(AtMost, 0), 
			self::$scourgeP6[4]->orderCoordinate(AtMost, 0), self::$scourgeP6[5]->orderCoordinate(AtMost, 0), 
			self::$scourgeP6[6]->orderCoordinate(AtMost, 0), self::$scourgeP6[7]->orderCoordinate(AtMost, 0), 
			self::$scourgeP6[8]->orderCoordinate(AtMost, 0), self::$scourgeP6[9]->orderCoordinate(AtMost, 0) 
		)->then(
			$ready->P6->setTo(0),
		'');
		
		$P1->_if( $ready->P4->exactly(1) )->then_justonce( $this->moveScourges(P4) );
		$P1->_if( $ready->P5->exactly(1) )->then_justonce( $this->moveScourges(P5) );
		$P1->_if( $ready->P6->exactly(1) )->then_justonce( $this->moveScourges(P6) );
		
		$P1->_if( $ready->P4->atLeast(1) )->then( $this->determineCoordinates(FRAGS::$scourgeP4, FRAGS::$movedP4, FRAGS::$x->P4, FRAGS::$y->P4, FRAGS::$Fragged->P4, P9,  0,  0)  );
		$P1->_if( $ready->P5->atLeast(1) )->then( $this->determineCoordinates(FRAGS::$scourgeP5, FRAGS::$movedP5, FRAGS::$x->P5, FRAGS::$y->P5, FRAGS::$Fragged->P5, P10, 64, 0)  );
		$P1->_if( $ready->P6->atLeast(1) )->then( $this->determineCoordinates(FRAGS::$scourgeP6, FRAGS::$movedP6, FRAGS::$x->P6, FRAGS::$y->P6, FRAGS::$Fragged->P6, P11, 0,  64) );
		
	}
	
	
	/////
	//ACTIONS
	//
	
	
	// get coordinates
	public function getCoordinate(){
		$text = '';
		
		$text .= $this->determineCoordinates(FRAGS::$scourgeP4, FRAGS::$movedP4, FRAGS::$x->P4, FRAGS::$y->P4, FRAGS::$Fragged->P4, P9, 0, 0);
		$text .= $this->determineCoordinates(FRAGS::$scourgeP5, FRAGS::$movedP5, FRAGS::$x->P5, FRAGS::$y->P5, FRAGS::$Fragged->P5, P10, 64, 0);
		$text .= $this->determineCoordinates(FRAGS::$scourgeP6, FRAGS::$movedP6, FRAGS::$x->P6, FRAGS::$y->P6, FRAGS::$Fragged->P6, P11, 0, 64);
		
		return $text;
	}
	
	
	
	// process to determine which units FRAGged and find its coordinates
	private function determineCoordinates($scourges, PermSwitch $moved, Deathcounter $x, Deathcounter $y, Deathcounter $Fragged, $player, $addx, $addy){
		
		/* @var TempDC[] $angles */
		$angles = array();
		for($i=0; $i<10; $i++){ $angles[] = new TempDC(37); }
		$clearangles = '';
		foreach($angles as $asd){ $clearangles .= $asd->setTo(0); }
		
		$dirs = array();
		for($i=0; $i<10; $i++){ $dirs[] = new TempDC(3); }
		$cleardirs = '';
		foreach($dirs as $asd){ $cleardirs .= $asd->setTo(0); }
		
		$y1 = new TempDC(Map::getHeight()*32-1);
		$y2 = new TempDC(Map::getHeight()*32-1);
		
		$angle = new TempDC(255);
		$diff = new TempDC(Map::getHeight()*32-1);
		$left = new TempSwitch();
		$down = new TempSwitch();
		$exact = new TempSwitch();
		
		$pick = new TempDC(10);
		$picked = new TempSwitch();
		
		$xoffset = new TempDC(1188);
		
		$text = '';
		
		
		//get directions, angles, move
		$text .= _if( $moved->is_set() )->then(
			//get angles and y's
		    self::getData($scourges, $angles, $dirs, $y1, $y2),
			
			//check if coordinate is exact or not
			$diff->absDifference($y1, $y2),
			_if( $diff->atMost(24) )->then(
				$exact->set(),
			''),
			
			//pick scourge
			self::selectScourge($y1, $angles, $dirs, $pick, $picked, $addy),
			
			//load individual angle and dir to universal DC
			self::loadProperData($pick, $angle, $angles, $dirs, $x, $y2, $left, $down, $addx, $addy),
			
			//set y coordinate
			$y->setTo($y1),
			_if( $exact->is_clear() )->then(
				$y->add(32),
				_if( $down->is_clear() )->then( $y1->add(64) ),
			''),
			$diff->absDifference($y1, $y2),
			
			//find x offset
			_if( $angle->atLeast(1) )->then(
				FRAGS::getSlope($xoffset, $angle),
				$xoffset->multiplyBy($diff),
				$xoffset->max((Map::getHeight()*32-1)*1188),
				$xoffset->roundedDivideBy(1000),
				$xoffset->max(Map::getWidth()*32-1),
				
				//use xoffset
				_if( $left->is_clear(), $angle->atLeast(1) )->then(
				    $x->add($xoffset),
					_if( $exact->is_clear() )->then( $x->add(32) ),
				''),
				_if( $left->is_set() )->then(
				    $x->subtract($xoffset),
					_if( $exact->is_clear() )->then( $x->subtract(32) ),
				''),
			''),
			//special case: vertical
			_if( $angle->atMost(0) )->then(
				$x->add(16),
				_if( $pick->exactly(0), $angles[5]->atLeast(1) )->then( $x->subtract(32) ),
				_if( $pick->exactly(1), $angles[6]->atLeast(1) )->then( $x->subtract(32) ),
				_if( $pick->exactly(2), $angles[7]->atLeast(1) )->then( $x->subtract(32) ),
				_if( $pick->exactly(3), $angles[8]->atLeast(1) )->then( $x->subtract(32) ),
				_if( $pick->exactly(4), $angles[9]->atLeast(1) )->then( $x->subtract(32) ),
				_if( $pick->exactly(5), $angles[0]->atMost(0) )->then( $x->subtract(32) ),
				_if( $pick->exactly(6), $angles[1]->atMost(0) )->then( $x->subtract(32) ),
				_if( $pick->exactly(7), $angles[2]->atMost(0) )->then( $x->subtract(32) ),
				_if( $pick->exactly(8), $angles[3]->atMost(0) )->then( $x->subtract(32) ),
				_if( $pick->exactly(9), $angles[4]->atMost(0) )->then( $x->subtract(32) ),
			''),
			
			//clear temps
			$y1->kill(),
			$y2->kill(),
			$xoffset->kill(),
			$angle->kill(),
			$diff->kill(),
			$exact->kill(),
			$left->kill(),
			$down->kill(),
			$pick->kill(),
			$picked->kill(),
			$clearangles,
			$cleardirs,
			
			//set output
			$Fragged->setTo(1),
			self::resetUnits($player),
		'');

		foreach($angles as $asd){
			$asd->kill();
		}
		foreach($dirs as $asd){
			$asd->kill();
		}

		
		//start FRAGS process
		$text .= $moved->set();
		$text .= _if( $scourges[0]->orderCoordinate(AtMost, 0), $scourges[1]->orderCoordinate(AtMost, 0), $scourges[2]->orderCoordinate(AtMost, 0),
					  $scourges[3]->orderCoordinate(AtMost, 0), $scourges[4]->orderCoordinate(AtMost, 0), $scourges[5]->orderCoordinate(AtMost, 0),
					  $scourges[6]->orderCoordinate(AtMost, 0), $scourges[7]->orderCoordinate(AtMost, 0), $scourges[8]->orderCoordinate(AtMost, 0),
					  $scourges[9]->orderCoordinate(AtMost, 0) )->then(
			$moved->clear(),
		'');
		
		return $text;
	}
	
	
	
	//select best scourge
	private function selectScourge($y1, $angles, $dirs, $pick, $picked, $addy){
		
		return
			_if( $y1->atMost(464+$addy) )->then(
			    _if( $angles[0]->atMost(36), $dirs[0]->atMost(1) )->then( $pick->setTo(0), $picked->set() ),
				_if( $picked->is_clear(), $angles[1]->atMost(36), $dirs[1]->atMost(1) )->then( $pick->setTo(1), $picked->set() ),
				_if( $picked->is_clear(), $angles[2]->atMost(36), $dirs[2]->atMost(1) )->then( $pick->setTo(2), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->atLeast(3600+$addy) )->then(
			    _if( $angles[8]->atMost(36), $dirs[8]->atLeast(2) )->then( $pick->setTo(8), $picked->set() ),
				_if( $picked->is_clear(), $angles[9]->atMost(36), $dirs[9]->atLeast(2) )->then( $pick->setTo(9), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->atMost(1488+$addy) )->then(
			    _if( $angles[3]->atMost(36), $dirs[3]->atMost(1) )->then( $pick->setTo(3), $picked->set() ),
				_if( $picked->is_clear(), $angles[4]->atMost(36), $dirs[4]->atMost(1) )->then( $pick->setTo(4), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->atLeast(2576+$addy) )->then(
			    _if( $angles[5]->atMost(36), $dirs[5]->atLeast(2) )->then( $pick->setTo(5), $picked->set() ),
				_if( $picked->is_clear(), $angles[6]->atMost(36), $dirs[6]->atLeast(2) )->then( $pick->setTo(6), $picked->set() ),
				_if( $picked->is_clear(), $angles[7]->atMost(36), $dirs[7]->atLeast(2) )->then( $pick->setTo(7), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->between(528+$addy,1520+$addy) )->then(
			    _if( $angles[0]->atMost(36), $dirs[0]->atLeast(2) )->then( $pick->setTo(0), $picked->set() ),
				_if( $picked->is_clear(), $angles[1]->atMost(36), $dirs[1]->atLeast(2) )->then( $pick->setTo(1), $picked->set() ),
				_if( $picked->is_clear(), $angles[2]->atMost(36), $dirs[2]->atLeast(2) )->then( $pick->setTo(2), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->between(2544+$addy, 3536+$addy) )->then(
			    _if( $angles[8]->atMost(36), $dirs[8]->atMost(1) )->then( $pick->setTo(8), $picked->set() ),
				_if( $picked->is_clear(), $angles[9]->atMost(36), $dirs[9]->atMost(1) )->then( $pick->setTo(9), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->atMost(2512+$addy) )->then(
			    _if( $angles[5]->atMost(36), $dirs[5]->atMost(1) )->then( $pick->setTo(5), $picked->set() ),
				_if( $picked->is_clear(), $angles[6]->atMost(36), $dirs[6]->atMost(1) )->then( $pick->setTo(6), $picked->set() ),
				_if( $picked->is_clear(), $angles[7]->atMost(36), $dirs[7]->atMost(1) )->then( $pick->setTo(7), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->atLeast(1552+$addy) )->then(
			    _if( $angles[3]->atMost(36), $dirs[3]->atLeast(2) )->then( $pick->setTo(3), $picked->set() ),
				_if( $picked->is_clear(), $angles[4]->atMost(36), $dirs[4]->atLeast(2) )->then( $pick->setTo(4), $picked->set() ),
			'').
			//sweepers
			_if( $picked->is_clear(), $y1->atMost(2512+$addy) )->then(
			    _if( $angles[8]->atMost(36), $dirs[8]->atMost(1) )->then( $pick->setTo(8), $picked->set() ),
				_if( $picked->is_clear(), $angles[9]->atMost(36), $dirs[9]->atMost(1) )->then( $pick->setTo(9), $picked->set() ),
			'').
			_if( $picked->is_clear(), $y1->atLeast(528+$addy) )->then(
			    _if( $angles[0]->atMost(36), $dirs[0]->atLeast(2) )->then( $pick->setTo(0), $picked->set() ),
				_if( $picked->is_clear(), $angles[1]->atMost(36), $dirs[1]->atLeast(2) )->then( $pick->setTo(1), $picked->set() ),
				_if( $picked->is_clear(), $angles[2]->atMost(36), $dirs[2]->atLeast(2) )->then( $pick->setTo(2), $picked->set() ),
			'').
			_if( $picked->is_clear() )->then(
			    $pick->setTo(10), $picked->set(),
			'');
	}
	
	
	
	//load proper data
	private function loadProperData($pick, $angle, $angles, $dirs, $x, $y2, $left, $down, $addx, $addy){
		return
			_if( $pick->exactly(0) )->then(
				_if( $dirs[0]->atLeast(2) )->then($dirs[0]->subtract(2), $down->set() ),
				_if( $dirs[0]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[0]),
				$x->setTo(976+$addx), $y2->setTo(496+$addy) ).
			_if( $pick->exactly(1) )->then(
				_if( $dirs[1]->atLeast(2) )->then( $dirs[1]->subtract(2), $down->set() ),
				_if( $dirs[1]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[1]),
				$x->setTo(3024+$addx), $y2->setTo(496+$addy) ).
			_if( $pick->exactly(2) )->then(
				_if( $dirs[2]->atLeast(2) )->then( $dirs[2]->subtract(2), $down->set() ),
				_if( $dirs[2]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[2]),
				$x->setTo(5072+$addx), $y2->setTo(496+$addy) ).
			_if( $pick->exactly(3) )->then(
				_if( $dirs[3]->atLeast(2) )->then( $dirs[3]->subtract(2), $down->set() ),
				_if( $dirs[3]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[3]),
				$x->setTo(2000+$addx), $y2->setTo(1520+$addy) ).
			_if( $pick->exactly(4) )->then(
				_if( $dirs[4]->atLeast(2) )->then( $dirs[4]->subtract(2), $down->set() ),
				_if( $dirs[4]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[4]),
				$x->setTo(4048+$addx), $y2->setTo(1520+$addy) ).
			_if( $pick->exactly(5) )->then(
				_if( $dirs[5]->atLeast(2) )->then( $dirs[5]->subtract(2), $down->set() ),
				_if( $dirs[5]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[5]),
				$x->setTo(1008+$addx), $y2->setTo(2544+$addy) ).
			_if( $pick->exactly(6) )->then(
				_if( $dirs[6]->atLeast(2) )->then( $dirs[6]->subtract(2), $down->set() ),
				_if( $dirs[6]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[6]),
				$x->setTo(3056+$addx), $y2->setTo(2544+$addy) ).
			_if( $pick->exactly(7) )->then(
				_if( $dirs[7]->atLeast(2) )->then( $dirs[7]->subtract(2), $down->set() ),
				_if( $dirs[7]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[7]),
				$x->setTo(5104+$addx), $y2->setTo(2544+$addy) ).
			_if( $pick->exactly(8) )->then(
				_if( $dirs[8]->atLeast(2) )->then( $dirs[8]->subtract(2), $down->set() ),
				_if( $dirs[8]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[8]),
				$x->setTo(2032+$addx), $y2->setTo(3568+$addy) ).
			_if( $pick->exactly(9) )->then(
				_if( $dirs[9]->atLeast(2) )->then( $dirs[9]->subtract(2), $down->set() ),
				_if( $dirs[9]->atLeast(1) )->then( $left->set() ),
				$angle->become($angles[9]),
				$x->setTo(4080+$addx), $y2->setTo(3568+$addy) ).
		//TEST
			_if( $pick->exactly(10) )->then(
			    Display("FRAGS ERROR"),
			'');
		//TEST
	}
	
	
	
	// get y coordinate and all angles
	private function getData($scourges, $angles, $dir, $y1, $y2){
		
		return
			$scourges[0]->getYOrderCoordinate($y1, Map::getHeight(), 8).
			$scourges[9]->getYOrderCoordinate($y2, Map::getHeight(), 32).
			
			FRAGS::getAngle($scourges[0], $angles[0], $dir[0]).
			FRAGS::getAngle($scourges[1], $angles[1], $dir[1]).
			FRAGS::getAngle($scourges[2], $angles[2], $dir[2]).
			FRAGS::getAngle($scourges[3], $angles[3], $dir[3]).
			FRAGS::getAngle($scourges[4], $angles[4], $dir[4]).
			FRAGS::getAngle($scourges[5], $angles[5], $dir[5]).
			FRAGS::getAngle($scourges[6], $angles[6], $dir[6]).
			FRAGS::getAngle($scourges[7], $angles[7], $dir[7]).
			FRAGS::getAngle($scourges[8], $angles[8], $dir[8]).
			FRAGS::getAngle($scourges[9], $angles[9], $dir[9]).
		'';
	}
	
	
	
	// get individual's angles
	private function resetUnits($player){
		return  _if( Always() )->then(
					//2,7
					Grid::$slideLeft64->centerOn(Grid::$origin).
					Grid::$shiftUp->centerOn(Grid::$slideLeft64).
					Grid::$YLoc[0]->centerOn(Grid::$shiftUp).
					Grid::$YLoc[127]->centerOn(Grid::$slideLeft64).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					//4,9
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$shiftUp->centerOn(Grid::$slideLeft64).
					Grid::$YLoc[127]->centerOn(Grid::$shiftUp).
					Grid::$YLoc[0]->centerOn(Grid::$slideLeft64).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					//1,6
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$shiftUp->centerOn(Grid::$slideLeft64).
					Grid::$YLoc[0]->centerOn(Grid::$shiftUp).
					Grid::$YLoc[127]->centerOn(Grid::$slideLeft64).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					//3,8
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$shiftUp->centerOn(Grid::$slideLeft64).
					Grid::$YLoc[127]->centerOn(Grid::$shiftUp).
					Grid::$YLoc[0]->centerOn(Grid::$slideLeft64).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					//0,5
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
					Grid::$shiftUp->centerOn(Grid::$slideLeft64).
					Grid::$YLoc[0]->centerOn(Grid::$shiftUp).
					Grid::$YLoc[127]->centerOn(Grid::$slideLeft64).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
					Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
					Loc::$aoe1x1->centerOn($player, "Infested Terran", Loc::$aoe3x3).
					MoveUnit(AllPlayers, "Zerg Scourge", 1, Loc::$aoe1x1, Loc::$aoe1x1).
				'');
	}
	
	
	
	// get necessary scourge angles
	private function getAngle($scourge, $angle, $dir){
		$text = '';
        $ignore = new TempSwitch();

		//angles 0 through 36 are valid, 37 is invalid
		//+128 means down, +64 means left
		
	    //top left (+64)
        for($i=1; $i<=36; $i++){
            $text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 256-$i) )->then(
                $angle->setTo($i),
	            $dir->setTo(1),
                $ignore->set(),
            '');
        }
		$text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 192) )->then(
                $angle->setTo(37),
				$dir->setTo(1),
                $ignore->set(),
            '');
		//bottom left (+64+128)
		$text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 128+37) )->then(
                $angle->setTo(37),
				$dir->setTo(3),
                $ignore->set(),
            '');
		for($i=36; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 128+$i) )->then(
                $angle->setTo($i),
	            $dir->setTo(3),
                $ignore->set(),
            '');
        }
		//bottom right (+128)
        for($i=0; $i<=36; $i++){
            $text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 128-$i) )->then(
                $angle->setTo($i),
	            $dir->setTo(2),
                $ignore->set(),
            '');
        }
		$text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 64) )->then(
                $angle->setTo(37),
				$dir->setTo(2),
                $ignore->set(),
            '');
		//top right (+0)
		$text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, 37) )->then(
                $angle->setTo(37),
                $ignore->set(),
            '');
		for($i=36; $i>0; $i--){
            $text .= _if( $ignore->is_clear(), $scourge->direction(AtLeast, $i) )->then(
                $angle->setTo($i),
                $ignore->set(),
            '');
        }
        $text .= _if($ignore->is_clear())->then(
            $angle->setTo(0),
        '');
	    
	    $text .= $ignore->kill();

        return $text;
	}
	
	
	
	//convert angle to slope
	private function getSlope($slope, $angle){
		$text = '';
		
		for($i=1; $i <= 36; $i++){
			$text .= _if( $angle->exactly($i) )->then(
				$slope->setTo( abs(round( tan(deg2rad(($i-0.5)*90/64))*1000 )) ),
			'');
		}
		
		return $text;
	}
	
	
	
	public function giveFrags($player1, $player2){
		return
			//2,7
			Grid::$slideLeft64->centerOn(Grid::$origin).
			Grid::$shiftUp->centerOn(Grid::$slideLeft64).
			Grid::$YLoc[0]->centerOn(Grid::$shiftUp).
			Grid::$YLoc[127]->centerOn(Grid::$slideLeft64).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			//4,9
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$shiftUp->centerOn(Grid::$slideLeft64).
			Grid::$YLoc[127]->centerOn(Grid::$shiftUp).
			Grid::$YLoc[0]->centerOn(Grid::$slideLeft64).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			//1,6
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$shiftUp->centerOn(Grid::$slideLeft64).
			Grid::$YLoc[0]->centerOn(Grid::$shiftUp).
			Grid::$YLoc[127]->centerOn(Grid::$slideLeft64).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			//3,8
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$shiftUp->centerOn(Grid::$slideLeft64).
			Grid::$YLoc[127]->centerOn(Grid::$shiftUp).
			Grid::$YLoc[0]->centerOn(Grid::$slideLeft64).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			//0,5
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$slideLeft64->centerOn(P12, Grid::$unit, Grid::$slideLeft64).
			Grid::$shiftUp->centerOn(Grid::$slideLeft64).
			Grid::$YLoc[0]->centerOn(Grid::$shiftUp).
			Grid::$YLoc[127]->centerOn(Grid::$slideLeft64).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[0]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
			Loc::$aoe3x3->centerOn(Grid::$YLoc[127]).
			Loc::$aoe1x1->centerOn($player2, "Infested Terran", Loc::$aoe3x3).
			Give(P12, "Zerg Scourge", 1, $player1, Loc::$aoe1x1).
		'';
	}
	
	

}


