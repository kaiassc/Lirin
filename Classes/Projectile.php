<?php



class Projectile{
	
	/* @var Deathcounter */ public $xpos;
	/* @var Deathcounter */ public $ypos;
	/* @var Deathcounter */ private $xpospart;
	/* @var Deathcounter */ private $ypospart;
	/* @var Deathcounter */ private $xvel;
	/* @var Deathcounter */ private $yvel;
	/* @var Deathcounter */ private $xacc;
	/* @var Deathcounter */ private $yacc;
	/* @var Deathcounter */ public $duration;
	/* @var Deathcounter */ public $eventTime;
	/* @var Deathcounter */ public $spellid;
	
	function __construct(Array $dcarray = null){
		
		if( $dcarray === null ){
			$this->xpos      = new Deathcounter(Map::getWidth()*32-1);
			$this->ypos      = new Deathcounter(Map::getHeight()*32-1);
			$this->xpospart  = new Deathcounter(2000);
			$this->ypospart  = new Deathcounter(2000);
			$this->xvel      = new Deathcounter(12800);
			$this->yvel      = new Deathcounter(12800);
			$this->xacc      = new Deathcounter(1600);
			$this->yacc      = new Deathcounter(1600);
			$this->duration  = new Deathcounter(720);
			$this->eventTime = new Deathcounter(720);
			$this->spellid   = new Deathcounter(100);
		}
		else if(is_array($dcarray)) {
			list($xpos, $ypos, $xpospart, $ypospart, $xvel, $yvel, $xacc, $yacc, $duration, $eventTime, $spellid) = $dcarray;
			$this->xpos      = $xpos;
			$this->ypos      = $ypos;
			$this->xpospart  = $xpospart;
			$this->ypospart  = $ypospart;
			$this->xvel      = $xvel;
			$this->yvel      = $yvel;
			$this->xacc      = $xacc;
			$this->yacc      = $yacc;
			$this->duration  = $duration;
			$this->eventTime = $eventTime;
			$this->spellid   = $spellid;
		}
		else {
			Error("\$dcarray must be an array");
		}
	}
	
	// Spell Constants
	const _Fireball         = 1;
	const _Lob              = 2;
	const _Lunge            = 3;
	const _Teleport         = 4;
	const _Meteor           = 5;
	const _Block            = 6;
	const _Disruption       = 7;
	const _Firewall         = 8;
	const _Barrier          = 9;
	const _Zap              = 10;
	const _Blaze            = 11;
	const _DanceOfFlames    = 12;
	const _Holocaust        = 13;
	const _Explosion        = 14;
	const _Claim            = 15;
	const _RainOfFire       = 16;
	const _Firebreath       = 17;
	const _Guided           = 18;
	
	
	/////
	// CONDITIONS
	///
	
	
	function notInUse(){
		return $this->duration->exactly(0);
	}
	
	function inUse(){
		return $this->duration->atLeast(1);
	}
	
	
	/////
	// ACTIONS
	///
	
	
	function setPosition($x, $y){
		if(is_numeric($x)){
			if($x > Map::getWidth()*32-1 || $x < 0)
				Error("Error! X needs to be within the bounds of the map");
		}
		elseif(!($x instanceof Deathcounter))
			Error("Error! X needs to be a number or a Deathcounter");
		if(is_numeric($y)){
			if($y > Map::getHeight()*32-1 || $y < 0)
				Error("Error! Y needs to be within the bounds of the map");
		}
		elseif(!($y instanceof Deathcounter))
			Error("Error! Y needs to be a number or a Deathcounter");
		
		return $this->xpos->setTo($x) . $this->ypos->setTo($y).
				$this->xpospart->setTo(1000) . $this->ypospart->setTo(1000);
	}
	function setVelocity($x, $y){
		if(is_numeric($x)){
			if($x > 64 || $x < -64)
				Error("Error! X needs to be between -64 and 64");
			$x = round(($x+64)*100);
		}
		elseif(!($x instanceof Deathcounter))
			Error("Error! X needs to be a number or a Deathcounter");
		if(is_numeric($y)){
			if($y > 64 || $y < -64)
				Error("Error! Y needs to be between -64 and 64");
			$y = round(($y+64)*100);
		}
		elseif(!($y instanceof Deathcounter))
			Error("Error! Y needs to be a number or a Deathcounter");
		
		
		return $this->xvel->setTo($x).
			$this->yvel->setTo($y);
	}
	function setAcceleration($x, $y){
		if(is_numeric($x)){
			if($x > 8 || $x < -8)
				Error("Error! X needs to be between -8 and 8");
			$x = round(($x+8)*100);
		}
		elseif(!($x instanceof Deathcounter))
			Error("Error! X needs to be a number or a Deathcounter");
		if(is_numeric($y)){
			if($y > 8 || $y < -8)
				Error("Error! Y needs to be between -8 and 8");
			$y = round(($y+8)*100);
		}
		elseif(!($y instanceof Deathcounter))
			Error("Error! Y needs to be a number or a Deathcounter");
		
		return $this->xacc->setTo($x).
			$this->yacc->setTo($y);
	}
	function setDuration($n){
		return $this->duration->setTo($n);
	}
	function setEventTime($n){
		return $this->eventTime->setTo($n);
	}
	
	function setSpellID($n){
		return $this->spellid->setTo($n);
	}
	
	
	//remove lifespan of projectile
	function tickDown(){
		return $this->duration->subtract(1).
			$this->eventTime->subtract(1);
	}
	
	
	// move projectile
	function move(){
		$text = '';
		
		$switch = new TempSwitch();
		
		// enable movement by default
		$text .= _if( $this->duration->atLeast(1) )->then(
			$switch->set(),
			
			// some spells clear movement at times...
			_if( $this->spellid->exactly(self::_DanceOfFlames), $this->eventTime->exactly(0) )->then( $switch->clear() ), // dance of flames
			_if( $this->spellid->exactly(self::_Holocaust), $this->eventTime->atLeast(1) )->then( $switch->clear() ), // holocaust
		'');
		
		// add velocity, acceleration, and make the changes
		$text .= _if( $switch )->then(	
			$this->VelToPos($this->xvel, $this->xpos, $this->xpospart),
			$this->VelToPos($this->yvel, $this->ypos, $this->ypospart),
			$this->xpos->subtract(64),
			$this->ypos->subtract(64),
			
			$this->xvel->add($this->xacc),
			$this->yvel->add($this->yacc),
			$this->xvel->subtract(800),
			$this->yvel->subtract(800),
			
			_if($this->xvel->atLeast(12801))->then($this->xvel->setTo(12800)),
			_if($this->yvel->atLeast(12801))->then($this->yvel->setTo(12800)),
			
			$switch->release(),
		'');
		
		return $text;
	}
	
	private function VelToPos(Deathcounter $vel, Deathcounter $pos, Deathcounter $pospart){
		
		$temp = new TempDC(12800);
		
		//prepare acceleration to load in
		$text = '';
		for($i=7; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($vel->atLeast($k*100))->then(
				$vel->subtract($k*100),
				$temp->add($k*100),
				$pos->add($k),
			'');
		}
		for($i=6; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($vel->atLeast($k))->then(
				$vel->subtract($k),
				$temp->add($k),
				$pospart->add($k),
			'');
		}
		$text .= $vel->become($temp);
		$temp->kill();
		
		$text .= _if($pospart->atLeast(1051))->then($pospart->subtract(100), $pos->add(1));
		$text .= _if($pospart->atMost(949))->then($pospart->add(100), $pos->subtract(1));
		
		
		return $text;
	}
	
	
	//output
	function show(){
		$text = '';
		
		// Sounds
		$bam = new Sound("bam");
		
		// Switches
		$switch = new TempSwitch();
		$success = new TempSwitch();
		
		// enable output by default
		$text .= _if( $this->duration->atLeast(1) )->then(
			$switch->set(),
			
			// some spells are invisible at times...
			_if( $this->spellid->exactly(self::_Holocaust), $this->eventTime->atLeast(1) )->then( $switch->clear() ), // holocaust
		'');
		
		// output
		$text .= _if( $switch )->then(
			Grid::putMain($this->xpos, $this->ypos, $success),
			_if( $success->is_set() )->then(
				
				// Fireball
				_if( $this->spellid->exactly(self::_Fireball), $this->duration->atLeast(2) )->then(
					Grid::$main->explode(),
				''),
				_if( $this->spellid->exactly(self::_Fireball), $this->duration->exactly(1) )->then(
					Grid::$main->largeExplode(),
				''),
				// lob
				_if( $this->spellid->exactly(self::_Lob), $this->duration->atLeast(2) )->then(
					Grid::$main->explode(),
				''),
				_if( $this->spellid->exactly(self::_Lob), $this->duration->exactly(1) )->then(
					Grid::$main->largeExplode(),
				''),
				// Lunge
				// Teleport
				// Meteor
				// Block
				// Disruption
				// Firewall
				// Barrier
				// Zap
				_if( $this->spellid->exactly(self::_Zap), $this->duration->atLeast(1) )->then(
					Grid::$main->blueExplode(),
				''),
				// Blaze
				// Dance of Flames
				_if( $this->spellid->exactly(self::_DanceOfFlames), $this->duration->atLeast(2) )->then(
					Grid::$main->explode(),
				''),
				_if( $this->spellid->exactly(self::_DanceOfFlames), $this->duration->exactly(1) )->then(
					Grid::$main->largeExplode(),
				''),
				// Holocaust
				_if( $this->spellid->exactly(self::_Holocaust), $this->duration->atLeast(1) )->then(
					Grid::$main->largeExplode(),
				''),
				// Explosion
				// Claim
				// Rain of Fire
				// Firebreath
				_if( $this->spellid->exactly(self::_Firebreath), $this->duration->atLeast(1) )->then(
					Grid::$main->explode(),
				''),
				// Guided
				_if( $this->spellid->exactly(self::_Guided), $this->duration->atLeast(2) )->then(
					Grid::$main->explode(),
				''),
				_if( $this->spellid->exactly(self::_Guided), $this->duration->exactly(1) )->then(
					Grid::$main->largeExplode(),
				''),
				
			''),
			$success->release(),
			$switch->release(),
		'');
		
		return $text;
	}
	
	
	//kill projectile
	function kill(){
		$text = '';
		
		$text .= _if( $this->duration->exactly(1) )->then(
			#$this->xpos->setTo(0),
			#$this->ypos->setTo(0),
			#$this->xpospart->setTo(0),
			#$this->ypospart->setTo(0),
			$this->xvel->setTo(0),
			$this->yvel->setTo(0),
			$this->xacc->setTo(0),
			$this->yacc->setTo(0),
			$this->duration->setTo(0),
		'');
		
		return $text;
	}
	
	
	// combined engine
	function engine(){
		return
			$this->move().
			$this->show().
			$this->kill().
			$this->tickDown();
	}
	
	
	
}