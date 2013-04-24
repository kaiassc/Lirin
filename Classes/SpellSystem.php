<?php



class SpellSystem {
	
	/* @var Projectile[] */ private $Projectiles = array();
	/* @var Projectile[] */ private $CPprojectiles = array();
	/* @var Projectile[] */ private $P4projectiles = array();
	/* @var Projectile[] */ private $P5projectiles = array();
	/* @var Projectile[] */ private $P6projectiles = array();
	/* @var Projectile[] */ private $P7projectiles = array();
	/* @var Projectile[] */ private $P8projectiles = array();
	
	
	/* @var Deathcounter[] */ private $xposDCs = array();
	/* @var Deathcounter[] */ private $yposDCs = array();
	/* @var Deathcounter[] */ private $xpospartDCs = array();
	/* @var Deathcounter[] */ private $ypospartDCs = array();
	/* @var Deathcounter[] */ private $xvelDCs = array();
	/* @var Deathcounter[] */ private $yvelDCs = array();
	/* @var Deathcounter[] */ private $xaccDCs = array();
	/* @var Deathcounter[] */ private $yaccDCs = array();
	/* @var Deathcounter[] */ private $durationDCs = array();
	
	const _Hero      = 1;
	const _Point1    = 2;
	const _Point2    = 3;
	const _Cursor    = 4;
	
	function __construct($projPerPlayer = 4){
		
		$projowners = new Player(P4, P5, P6, P7, P8);
		
		for($i=0; $i<$projPerPlayer; $i++){
			$this->xposDCs[]        = $xpos      = new Deathcounter($projowners, Map::getWidth()*32-1);
			$this->yposDCs[]        = $ypos      = new Deathcounter($projowners, Map::getWidth()*32-1);
			$this->xpospartDCs[]    = $xpospart  = new Deathcounter($projowners, 2000);
			$this->ypospartDCs[]    = $ypospart  = new Deathcounter($projowners, 2000);
			$this->xvelDCs[]        = $xvel      = new Deathcounter($projowners, 6400);
			$this->yvelDCs[]        = $yvel      = new Deathcounter($projowners, 6400);
			$this->xaccDCs[]        = $xacc      = new Deathcounter($projowners, 1600);
			$this->yaccDCs[]        = $yacc      = new Deathcounter($projowners, 1600);
			$this->durationDCs[]    = $duration  = new Deathcounter($projowners, 720);
			
			$this->CPprojectiles[] = new Projectile(array($xpos->CP, $ypos->CP, $xpospart->CP, $ypospart->CP, $xvel->CP, $yvel->CP, $xacc->CP, $yacc->CP, $duration->CP));
			$this->Projectiles[] = $this->P4projectiles[] = new Projectile(array($xpos->P4, $ypos->P4, $xpospart->P4, $ypospart->P4, $xvel->P4, $yvel->P4, $xacc->P4, $yacc->P4, $duration->P4));
			$this->Projectiles[] = $this->P5projectiles[] = new Projectile(array($xpos->P5, $ypos->P5, $xpospart->P5, $ypospart->P5, $xvel->P5, $yvel->P5, $xacc->P5, $yacc->P5, $duration->P5));
			$this->Projectiles[] = $this->P6projectiles[] = new Projectile(array($xpos->P6, $ypos->P6, $xpospart->P6, $ypospart->P6, $xvel->P6, $yvel->P6, $xacc->P6, $yacc->P6, $duration->P6));
			$this->Projectiles[] = $this->P7projectiles[] = new Projectile(array($xpos->P7, $ypos->P7, $xpospart->P7, $ypospart->P7, $xvel->P7, $yvel->P7, $xacc->P7, $yacc->P7, $duration->P7));
			$this->Projectiles[] = $this->P8projectiles[] = new Projectile(array($xpos->P8, $ypos->P8, $xpospart->P8, $ypospart->P8, $xvel->P8, $yvel->P8, $xacc->P8, $yacc->P8, $duration->P8));
			
		}
		
	}
	
	
	function CreateEngine(){
		
		$P1 =           new Player(P1);
		$P4 =           new Player(P4);
		$P5 =           new Player(P5);
		$P6 =           new Player(P6);
		$humans =       new Player(P4, P5, P6);
		$projowners =   new Player(P4, P5, P6, P7, P8);
		
		$P1->justonce(
			$this->xvelDCs[0]->leaderboard("xvelDCs[0]"),
		'');
		
		
		$spelliscast = new TempSwitch();
		
		$xmax = Map::getWidth()*32-1;
		$ymax = Map::getHeight()*32-1;
		

		$bsX = BattleSystem::$xDCs[0];
		$bsY = BattleSystem::$yDCs[0];
		
		$point1X = FRAGS::$x->CP;
		$point1Y = FRAGS::$y->CP;
		$point2X = FRAGS::$x->CP;
		$point2Y = FRAGS::$y->CP;
		
		
		// Projectile Variables
		$positionx =        new TempDC(Map::getWidth()*32-1);
		$positiony =        new TempDC(Map::getHeight()*32-1);
		$velocityx =        new TempDC(6400);
		$velocityy =        new TempDC(6400);
		$accelerationx =    new TempDC(1600);
		$accelerationy =    new TempDC(1600);
		$duration =         new TempDC(720);
		
		// Spell Variables
		$DistanceOriginIndex =          new TempDC($projowners);
		$DistanceDestinationIndex =     new TempDC($projowners);
		$ComponentOriginIndex =         new TempDC($projowners);
		$ComponentDestinationIndex =    new TempDC($projowners);
		
		$MaxCastRange =                 new TempDC($projowners);
		
		$PositionIndex =                new TempDC($projowners);
		$StaticOffsetX =                new TempDC($projowners);
		$StaticOffsetY =                new TempDC($projowners);
		
		$VelocityLoadIndex =            new TempDC($projowners);
		$VelocityMultiplyByDCIndex =    new TempDC($projowners);
		$VelocityMultiplier =           new TempDC($projowners, 100);
		$VelocityDivisor =              new TempDC($projowners, 100);
		$VelocityRawY =                  new TempDC($projowners, 6400);
		$VelocityAdjustForSigned =      new TempDC($projowners);
		
		
		
		// Pseudo fireball cast
		$P4->_if( FRAGS::$P4Fragged )->then(
			FRAGS::$P4Fragged->clear(),
			$spelliscast->set(),
		'');
		$P5->_if( FRAGS::$P5Fragged )->then(
			FRAGS::$P5Fragged->clear(),
			$spelliscast->set(),
		'');
		$P6->_if( FRAGS::$P6Fragged )->then(
			FRAGS::$P6Fragged->clear(),
			$spelliscast->set(),
		'');
		
		$humans->_if( $spelliscast )->then(
			
			Display("Invoke fireball settings"),
			
			$DistanceOriginIndex        ->setTo(self::_Hero),
			$DistanceDestinationIndex   ->setTo(self::_Point1),
			
			$ComponentOriginIndex       ->setTo(self::_Hero),
			$ComponentDestinationIndex  ->setTo(self::_Point1),
			
			// unused
			$MaxCastRange->setTo(1000/*px*/),
			
			// Set Position
			$PositionIndex->setTo(1), // Load Distance's Origin
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1), // Load components
			$VelocityMultiplyByDCIndex->setTo(0), // none
			$VelocityMultiplier->setTo(16),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), // Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(24),
			
		'');
		
		// Lob 
		$humans->_if( $spelliscast, Never() )->then(
			
			Display("Invoke lob settings"),
			
			$DistanceOriginIndex        ->setTo(self::_Hero),
			$DistanceDestinationIndex   ->setTo(self::_Point1),
			
			$ComponentOriginIndex       ->setTo(self::_Hero),
			$ComponentDestinationIndex  ->setTo(self::_Point1),
			
			// unused
			$MaxCastRange->setTo(1000/*px*/),
			
			// Set Position
			$PositionIndex->setTo(1), // Load Distance's Origin
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1), // Load components
			$VelocityMultiplyByDCIndex->setTo(1), // vel *= distance
			$VelocityMultiplier->setTo(0),
			$VelocityDivisor->setTo(16),
			$VelocityAdjustForSigned->setTo(1), // Add/subtracts for signed
			//$VelocityRawY->setTo(3200-2975),      // 3200 is zero
			$VelocityRawY->setTo(3200-2625),      // 3200 is zero
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800+350),
			
			// Set Duration
			$duration->setTo(16),
			
		'');
		
		$distX1 = new TempDC($xmax);
		$distY1 = new TempDC($ymax);
		$distX2 = new TempDC($xmax);
		$distY2 = new TempDC($ymax);
		
		$compX1 = new TempDC($xmax);
		$compY1 = new TempDC($ymax);
		$compX2 = new TempDC($xmax);
		$compY2 = new TempDC($ymax);
		
		$cursorx = new Deathcounter(Map::getWidth()*32-1);
		$cursory = new Deathcounter(Map::getHeight()*32-1);
		
		
		$humans->_if( $spelliscast )->then(
			
			//GetCursor($cursorx, $cursory, Map::getWidth(), Map::getHeight()),
			
			// Load the Distance Calculation's Origin and Destination
			_if( $DistanceOriginIndex->exactly(self::_Hero) )->then(
				$distX1->setTo($bsX->CP),
				$distY1->setTo($bsY->CP),
			''),
			_if( $DistanceOriginIndex->exactly(self::_Point1) )->then(
				$distX1->setTo($point1X),
				$distY1->setTo($point1Y),
			''),
			_if( $DistanceOriginIndex->exactly(self::_Point2) )->then(
				$distX1->setTo($point2X),
				$distY1->setTo($point2Y),
			''),
			_if( $DistanceOriginIndex->exactly(self::_Cursor) )->then(
				//$distX1->setTo($cursorx),
				//$distY1->setTo($cursory),
			''),
			
			_if( $DistanceDestinationIndex->exactly(self::_Hero) )->then(
				$distX2->setTo($bsX->CP),
				$distY2->setTo($bsY->CP),
			''),
			_if( $DistanceDestinationIndex->exactly(self::_Point1) )->then(
				$distX2->setTo($point1X),
				$distY2->setTo($point1Y),
			''),
			_if( $DistanceDestinationIndex->exactly(self::_Point2) )->then(
				$distX2->setTo($point2X),
				$distY2->setTo($point2Y),
			''),
			_if( $DistanceDestinationIndex->exactly(self::_Cursor) )->then(
				//$distX2->setTo($cursorx),
				//$distY2->setTo($cursory),
			''),
			
			// Load the Angle and Component's Origin and Destination
			_if( $ComponentOriginIndex->exactly(self::_Hero) )->then(
				$compX1->setTo($bsX->CP),
				$compY1->setTo($bsY->CP),
			''),
			_if( $ComponentOriginIndex->exactly(self::_Point1) )->then(
				$compX1->setTo($point1X),
				$compY1->setTo($point1Y),
			''),
			_if( $ComponentOriginIndex->exactly(self::_Point2) )->then(
				$compX1->setTo($point2X),
				$compY1->setTo($point2Y),
			''),
			_if( $ComponentOriginIndex->exactly(self::_Cursor) )->then(
				//$compX1->setTo($cursorx),
				//$compY1->setTo($cursory),
			''),
			
			_if( $ComponentDestinationIndex->exactly(self::_Hero) )->then(
				$compX2->setTo($bsX->CP),
				$compY2->setTo($bsY->CP),
			''),
			_if( $ComponentDestinationIndex->exactly(self::_Point1) )->then(
				$compX2->setTo($point1X),
				$compY2->setTo($point1Y),
			''),
			_if( $ComponentDestinationIndex->exactly(self::_Point2) )->then(
				$compX2->setTo($point2X),
				$compY2->setTo($point2Y),
			''),
			_if( $ComponentDestinationIndex->exactly(self::_Cursor) )->then(
				//$compX2->setTo($cursorx),
				//$compY2->setTo($cursory),
			''),
			
		'');

		$distance = new TempDC($xmax);
		$angle = new TempDC(1440);
		$xcomponent = new TempDC(10000);
		$ycomponent = new TempDC(10000);
		
		$tempx = new TempDC(256000);
		$tempy = new TempDC(256000);
		$success = new TempSwitch();

		$projowners->_if( $spelliscast )->then(
			
			// Calculate distance, angle, and x and y components
			$distance->distance($distX1, $distY1, $distX2, $distY2),
			$angle->getAngle($compX1, $compY1, $compX2, $compY2),
			$angle->componentsInto($xcomponent, $ycomponent),
			
			
			// TODO:Remove
			_if( $distance->atLeast(257) )->then(
				$distance->setTo(256),
			''),
			
			
			// Set Position
			_if( $PositionIndex->exactly(1) )->then(
				$positionx->setTo($distX1),
				$positiony->setTo($distY1),
			''),
			
			// Add Offsets
			$positionx->add($StaticOffsetX),
			$positiony->add($StaticOffsetY),
			
			// Set Velocity
			$velocityx->setTo(0),
			$velocityy->setTo(0),
			
			_if( $VelocityLoadIndex->exactly(1) )->then(
				Display("loading xycomponents"),
				$velocityx->roundedQuotientOf($xcomponent, 10),
				$velocityy->roundedQuotientOf($ycomponent, 10),
			''),
			
			$velocityx->Max(1000),
			$velocityy->Max(1000),
			
			_if( $VelocityMultiplyByDCIndex->exactly(1) )->then(
				Display("multiplying velocities by the distance"),
				$velocityx->multiplyBy($distance),
				$velocityy->multiplyBy($distance),
			''),
			
			$velocityx->Max(256000),
			$velocityy->Max(256000),
			
			_if( $VelocityMultiplier->atLeast(1) )->then(
				Display("multiplying velocities by static value"),
				$velocityx->multiplyBy($VelocityMultiplier),
				$velocityy->multiplyBy($VelocityMultiplier),
			''),
			
			_if( $VelocityDivisor->atLeast(1) )->then(
				Display("multiplying velocities by static value"),
				$velocityx->roundedDivideBy($VelocityDivisor),
				$velocityy->roundedDivideBy($VelocityDivisor),
			''),
			
			// Add/Subtract for signed
			_if( $VelocityAdjustForSigned->atLeast(1) )->then(
				Display("adjusting for signed velocity"),
				$tempx->setTo($velocityx),
				$tempy->setTo($velocityy),
				
				$tempx->roundedDivideBy(10),
				$tempy->roundedDivideBy(10),
				
				$tempx->Max(1600),
				$tempy->Max(1600),
				
				$velocityx->setTo(3200),
				$velocityy->setTo(3200),
				
				_if( $angle->between(361,1079) )->then(
					Display(" velocity <--"),
					$velocityx->subtract($tempx),
				e)->_else(
					Display(" velocity -->"),
					$velocityx->add($tempx),
				''),
				_if( $angle->atMost(719) )->then(
					Display(" velocity ^"),
					$velocityy->subtract($tempy),
				''),
				_if( $angle->atLeast(720) )->then(
					Display(" velocity V"),
					$velocityy->add($tempy),
				''),
				
				$velocityx->Max(6400),
				$velocityy->Max(6400),
				
			''),
			
			_if( $VelocityRawY->atLeast(1) )->then(
				$velocityy->add($VelocityRawY),
				$velocityy->subtract(3200),
			''),
			
			
			// Set Acceleration
			#$accelerationx->setTo(800), // aka zero
			#$accelerationy->setTo(800), // aka zero
			
			$this->loadIntoProjectiles($positionx, $positiony, $velocityx, $velocityy, $accelerationx, $accelerationy, $duration, $success),
			_if( $success->is_clear() )->then(
				Display("Failed to load spell (projectiles are all taken?)"),
			''),
			
			$positionx->release(),
			$positiony->release(),
			$velocityx->release(),
			$velocityy->release(),
			$accelerationx->release(),
			$accelerationy->release(),
			$duration->release(),
			
			$DistanceOriginIndex->release(),
			$DistanceDestinationIndex->release(),
			$ComponentOriginIndex->release(),
			$ComponentDestinationIndex->release(),
			$MaxCastRange->release(),
			$PositionIndex->release(),
			$StaticOffsetX->release(),
			$StaticOffsetY->release(),
			$VelocityLoadIndex->release(),
			$VelocityMultiplyByDCIndex->release(),
			$VelocityMultiplier->release(),
			$VelocityDivisor->release(),
			$VelocityRawY->release(),
			$VelocityAdjustForSigned->release(),
			
			$distX1->release(),
			$distY1->release(),
			$distX2->release(),
			$distY2->release(),
			$compX1->release(),
			$compY1->release(),
			$compX2->release(),
			$compY2->release(),
			$tempx->release(),
			$tempy->release(),
			$distance->release(),
			$angle->release(),
			$xcomponent->release(),
			$ycomponent->release(),
			
			$success->release(),
			
			$spelliscast->release(),
		'');
		
		
		
		$projowners->always(
			$this->projectileEngine(),
		'');
		
	}
	
	function loadDistanceOrigin($origin, $originX, $originY){
		
	}
	
	function loadIntoProjectiles($positionx, $positiony, $velocityx, $velocityy, $accelerationx, $accelerationy, $duration, TempSwitch $success){
		$text = '';
		
		$text .= $success->clear();
		
		foreach( $this->CPprojectiles as $projectile ){
			$text .= _if( $projectile->notInUse(), $success->is_clear() )->then(
				$projectile->setPosition($positionx, $positiony),
				$projectile->setVelocity($velocityx, $velocityy),
				$projectile->setDuration($duration),
				$projectile->setAcceleration($accelerationx, $accelerationy),
				
				$success->set(),
			'');
		}
		
		return $text;
	}
	
	function projectileEngine(){
		$text = '';
		
		foreach($this->CPprojectiles as $projectile){
			$text .= $projectile->engine();
		}
		
		return $text;
	}
	
	
}