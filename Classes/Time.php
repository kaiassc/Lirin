<?php


class Time {
	
	/* @var Deathcounter */
	static $Clock;


	/* @var Deathcounter */
	static private $secondcount;
	
	/* @var Deathcounter */
	static private $minutecount;
	
	/* @var PermSwitch */
	static $oddLoop;
	
	/* @var Deathcounter */
	static $elapsedLoops;
	
	function __construct($MinutesPerDay){
		if( !is_numeric($MinutesPerDay) ){
			Error('Expecting a number for the number of (realtime) minutes each (ingame) day should be');
		}
		if( $MinutesPerDay < 2 ){
			Error('There must be at least 2 minutes per day');
		}
		
		$loopspergameminute = (int)round(($MinutesPerDay*(1000/84)) / 24);

		self::$Clock   = new Deathcounter(2400);
		self::$secondcount = new Deathcounter(12);
		self::$minutecount = new Deathcounter(714);
		self::$elapsedLoops = new Deathcounter();
		self::$oddLoop     = new PermSwitch();
		
		$clock = self::$Clock;
		$loopcounter    = new Deathcounter($loopspergameminute);
		$minutecounter  = new Deathcounter(60);
		
		
		$P1 = new Player(P1);
		
		$P1->always(
			$loopcounter->add(1),
			self::$secondcount->add(1),
			self::$minutecount->add(1),
			self::$elapsedLoops->add(1),
			self::$oddLoop->toggle(),
		'');
		$P1->_if( $loopcounter->atLeast($loopspergameminute) )->then(
			$loopcounter->setTo(0),
			$minutecounter->add(1),
			$clock->add(1),
		'');
		$P1->_if( $minutecounter->atLeast(60) )->then(
			$minutecounter->setTo(0),
			$clock->add(40),
		'');
		$P1->_if( $clock->atLeast(2400) )->then(
			$clock->setTo(0),
		'');
		
		$P1->_if( self::$secondcount->atLeast(12) )->then(
			self::$secondcount->setTo(0),
		'');
		$P1->_if( self::$minutecount->atLeast(714) )->then(
			self::$minutecount->setTo(0),
		'');
		
	}


	/**
	 * "true" every 12 loops, to signify a new realtime second
	 * 
	 */
	static function realSecondStrokes(){
		return self::$secondcount->exactly(0);
	}
	
	/**
	 * "true" once every 60 realtime seconds
	 * 
	 */
	static function realMinuteStrokes(){
		return self::$minutecount->exactly(0);
	}
	
	
	
	
}