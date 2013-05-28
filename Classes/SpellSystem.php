<?php



class SpellSystem {
	
	/* @var Projectile[] */ private $Projectiles = array();
	/* @var Projectile[] */ private $CPprojectiles = array();
	/* @var Projectile[] */ private $P4projectiles = array();
	/* @var Projectile[] */ private $P5projectiles = array();
	/* @var Projectile[] */ private $P6projectiles = array();
	/* @var Projectile[] */ private $P7projectiles = array();
	/* @var Projectile[] */ private $P8projectiles = array();
	
	
	/* @var CoordinateUnit[] */ private $P4casterunits = array();
	/* @var CoordinateUnit[] */ private $P5casterunits = array();
	/* @var CoordinateUnit[] */ private $P6casterunits = array();
	/* @var Deathcounter[] */   private $SpellSlotDCs = array();
	
	
	/* @var Deathcounter[] */ private $xposDCs = array();
	/* @var Deathcounter[] */ private $yposDCs = array();
	/* @var Deathcounter[] */ private $xpospartDCs = array();
	/* @var Deathcounter[] */ private $ypospartDCs = array();
	/* @var Deathcounter[] */ private $xvelDCs = array();
	/* @var Deathcounter[] */ private $yvelDCs = array();
	/* @var Deathcounter[] */ private $xaccDCs = array();
	/* @var Deathcounter[] */ private $yaccDCs = array();
	/* @var Deathcounter[] */ private $durationDCs = array();
	/* @var Deathcounter[] */ private $spellidDCs = array();
	
	// coordinate controller
	const _Hero         = 1;
	const _Point1       = 2;
	const _Point2       = 3;
	const _Cursor       = 4;
	// distance controller
	const _DistResize   = 1;
	const _DistCancel   = 2;
	// angle controller
	const _Calc         = 1;
	const _CalcSave     = 2;
	const _CalcSaveLoad = 3;
	const _Load         = 4;
	const _addConst     = 1;
	const _shiftTo      = 2;
	
	function __construct($projPerPlayer = 4){
		
		$humans = new Player(P4, P5, P6);
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
			$this->spellidDCs[]     = $spellid   = new Deathcounter($projowners, 100);
			
			$this->CPprojectiles[] = new Projectile(array($xpos->CP, $ypos->CP, $xpospart->CP, $ypospart->CP, $xvel->CP, $yvel->CP, $xacc->CP, $yacc->CP, $duration->CP));
			$this->Projectiles[] = $this->P4projectiles[] = new Projectile(array($xpos->P4, $ypos->P4, $xpospart->P4, $ypospart->P4, $xvel->P4, $yvel->P4, $xacc->P4, $yacc->P4, $duration->P4, $spellid->P4));
			$this->Projectiles[] = $this->P5projectiles[] = new Projectile(array($xpos->P5, $ypos->P5, $xpospart->P5, $ypospart->P5, $xvel->P5, $yvel->P5, $xacc->P5, $yacc->P5, $duration->P5, $spellid->P5));
			$this->Projectiles[] = $this->P6projectiles[] = new Projectile(array($xpos->P6, $ypos->P6, $xpospart->P6, $ypospart->P6, $xvel->P6, $yvel->P6, $xacc->P6, $yacc->P6, $duration->P6, $spellid->P6));
			$this->Projectiles[] = $this->P7projectiles[] = new Projectile(array($xpos->P7, $ypos->P7, $xpospart->P7, $ypospart->P7, $xvel->P7, $yvel->P7, $xacc->P7, $yacc->P7, $duration->P7, $spellid->P7));
			$this->Projectiles[] = $this->P8projectiles[] = new Projectile(array($xpos->P8, $ypos->P8, $xpospart->P8, $ypospart->P8, $xvel->P8, $yvel->P8, $xacc->P8, $yacc->P8, $duration->P8, $spellid->P8));
			
		}
		
		
		// Create Spell Slot Caster Units
		
		for($i=1;$i<=4; $i++){
			$casterunit = "Zerg Mutalisk";
			$x = 4208+64*($i-1);
			$y = 624;
			$this->P4casterunits[$i] = new CoordinateUnit(UnitManager::MintUnitWithAnyIndex($casterunit, P12, $x, $y+64*0, Invincible), $x, $y+64*0, $casterunit, P4);
			$this->P5casterunits[$i] = new CoordinateUnit(UnitManager::MintUnitWithAnyIndex($casterunit, P12, $x, $y+64*1, Invincible), $x, $y+64*1, $casterunit, P5);
			$this->P6casterunits[$i] = new CoordinateUnit(UnitManager::MintUnitWithAnyIndex($casterunit, P12, $x, $y+64*2, Invincible), $x, $y+64*2, $casterunit, P6);
		}
		
		
		for($i=1; $i<=4; $i++){
			$this->SpellSlotDCs[$i] = new Deathcounter($humans, 50);
		}
		
		
		// TODO: testing trigger, remove later
		$humans->justonce(
			$this->SpellSlotDCs[1]->setTo(1), // fireball
			$this->SpellSlotDCs[2]->setTo(2), // lob
			$this->SpellSlotDCs[3]->setTo(3), // firewall
			$this->SpellSlotDCs[4]->setTo(4), //
		'');
		
	}
	
	
	function CreateEngine(){
		
		$P1 =           new Player(P1);
		$P4 =           new Player(P4);
		$P5 =           new Player(P5);
		$P6 =           new Player(P6);
		$comps =        new Player(P7, P8);
		$humans =       new Player(P4, P5, P6);
		$projowners =   new Player(P4, P5, P6, P7, P8);
		
		
		// Give caster units
		foreach($this->P4casterunits as $casterunit){
			$P4->justonce(
				Loc::$main->placeAt($casterunit->x, $casterunit->y),
				$casterunit->P12->giveTo(P4, 1, Loc::$main),
			'');
		}
		foreach($this->P5casterunits as $casterunit){
			$P5->justonce(
				Loc::$main->placeAt($casterunit->x, $casterunit->y),
				$casterunit->P12->giveTo(P5, 1, Loc::$main),
			'');
		}
		foreach($this->P6casterunits as $casterunit){
			$P6->justonce(
				Loc::$main->placeAt($casterunit->x, $casterunit->y),
				$casterunit->P12->giveTo(P6, 1, Loc::$main),
			'');
		}
		
		
		
		// Pre-existing DCs
		$xmax = Map::getWidth()*32-1;
		$ymax = Map::getHeight()*32-1;

		$bsX = BattleSystem::$xDCs[0];
		$bsY = BattleSystem::$yDCs[0];
		
		// Persistent DCs
		$point1X =                      new Deathcounter($humans, FRAGS::$x->Max);
		$point1Y =                      new Deathcounter($humans, FRAGS::$y->Max);
		$point2X =                      new Deathcounter($humans, FRAGS::$x->Max);
		$point2Y =                      new Deathcounter($humans, FRAGS::$y->Max);
		
		$selectedSpell =                new Deathcounter($humans, 4);
		$spellCast =                    new Deathcounter($humans);
		$spellStage =                   new Deathcounter($humans, 15);
		
		// Saved DCs
		$SaveAngle =                    new TempSwitch();
		$StoredCurrentAngle =           new Deathcounter($humans, 1440);
		$StoredDestinationAngle =       new Deathcounter($humans, 1440);
		
		
		// Projectile Variables
		$positionx =                    new TempDC(Map::getWidth()*32-1);
		$positiony =                    new TempDC(Map::getHeight()*32-1);
		$velocityx =                    new TempDC(12800);
		$velocityy =                    new TempDC(12800);
		$accelerationx =                new TempDC(1600);
		$accelerationy =                new TempDC(1600);
		$duration =                     new TempDC(720);
		
		
		// SPELL VARIABLES
		
		// for distance
		$DistanceOriginIndex =          new TempDC();
		$DistanceDestinationIndex =     new TempDC();
		
		$FindDistance =                 new TempSwitch();
		$distance =                     new TempDC(256);
		
		$MaxRangeIndex =                new TempDC();
		$MaxCastRange =                 new TempDC(512);
		
		// for coordinates and angle
		$ComponentOriginIndex =         new TempDC();
		$ComponentDestinationIndex =    new TempDC();
		
		$AngleIndex =                   new TempDC();
		
		$AngleAlterationsIndex =        new TempDC();
		$AngleAlterationsValue =        new TempDC(1440);
		
		$angle =                        new TempDC(1440);
		
		// for components
		$FindComponents =               new TempSwitch();
		$xcomponent =                   new TempDC(10000);
		$ycomponent =                   new TempDC(10000);
		
		// for position
		$PositionIndex =                new TempDC();
		$PositionLoadIndex =            new TempDC();
		$PositionMultiplier =           new TempDC(128);
		$StaticOffsetX =                new TempDC(12800);
		$StaticOffsetY =                new TempDC(12800);
		
		// for velocity
		$VelocityLoadIndex =            new TempDC();
		$VelocityMultiplyByDCIndex =    new TempDC();
		$VelocityMultiplier =           new TempDC(256);
		$VelocityDivisor =              new TempDC(100);
		$VelocityRawY =                 new TempDC(12800);
		$VelocityAdjustForSigned =      new TempDC();
		
		$tempx = $temp1 =               new TempDC(256000);
		$tempy = $temp2 =               new TempDC(256000);
		
		// miscellaneous
		$frags =                        new TempSwitch();
		$invokedslot =                  new Deathcounter($humans, 104);
		$invokedspell =                 new TempDC(50);
		$success = $sign =              new TempSwitch();
		$loadIntoProj =                 new TempSwitch();
		
		// 
		$humans->_if( $invokedslot->atLeast(1) )->then(
			$invokedslot->subtract(100),
		'');
		
		// Set invokedslot
		foreach($this->P4casterunits as $key=>$casterunit){
			$P4->_if( $casterunit->orderCoordinate(AtLeast, 1) )->then(
				//Display("invokedslot set to $key"),
				$invokedslot->setTo($key+100),
				Loc::$aoe1x1->placeAt($casterunit->x, $casterunit->y),
				$casterunit->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
		}
		foreach($this->P5casterunits as $key=>$casterunit){
			$P5->_if( $casterunit->orderCoordinate(AtLeast, 1) )->then(
				//Display("invokedslot set to $key"),
				$invokedslot->setTo($key+100),
				Loc::$aoe1x1->placeAt($casterunit->x, $casterunit->y),
				$casterunit->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
		}
		foreach($this->P6casterunits as $key=>$casterunit){
			$P6->_if( $casterunit->orderCoordinate(AtLeast, 1) )->then(
				//Display("invokedslot set to $key"),
				$invokedslot->setTo($key+100),
				Loc::$aoe1x1->placeAt($casterunit->x, $casterunit->y),
				$casterunit->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
		}
		
		
		// Set $frags
		$P4->_if( FRAGS::$P4Fragged, $invokedslot->between(1, 99) )->then(
			FRAGS::$P4Fragged->clear(),
			$frags->set(),
		'');
		$P5->_if( FRAGS::$P5Fragged, $invokedslot->between(1, 99) )->then(
			FRAGS::$P5Fragged->clear(),
			$frags->set(),
		'');
		$P6->_if( FRAGS::$P6Fragged, $invokedslot->between(1, 99) )->then(
			FRAGS::$P6Fragged->clear(),
			$frags->set(),
		'');		
		
		
		// Set invokedspell
		foreach($this->SpellSlotDCs as $key=>$spellslotdc){
			$humans->_if( $frags, $invokedslot->exactly($key) )->then(
				//Display("invokedslot is $key, setting invokedspell based on proper SpellSlotDC"),
				$invokedspell->setTo($spellslotdc->CP),
				$invokedslot->setTo(0),
			'');
		}
		
		$humans->_if( $frags )->then(
			$point2X->setTo($point1X),
			$point2Y->setTo($point1Y),
			$point1X->setTo(FRAGS::$x->CP),
			$point1Y->setTo(FRAGS::$y->CP),
			
			//Display("Spell is cast"),
			//Display("invoked spell: $invokedspell"),
			
			$frags->clear(),
		'');
		
		
		
		
		
		//////
		// SPELL CONTROLLER
		///
		
		// Fireball
		$humans->_if( $invokedspell->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke fireball"),
			
			// Distance
			
			// Angle
			$AngleIndex->setTo(self::_Calc),
				$ComponentOriginIndex       ->setTo(self::_Hero),
				$ComponentDestinationIndex  ->setTo(self::_Point1),
			$FindComponents->set(),
			
			// Position
			$PositionIndex->setTo(1),                       // Load Angle's Origin
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Velocity
			$VelocityLoadIndex->setTo(1),                   // Load components
			$VelocityMultiplyByDCIndex->setTo(0),           // none
			$VelocityMultiplier->setTo(16),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1),             // Add/subtracts for signed
			
			// Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Duration
			$duration->setTo(24),
			
			// Cast
			$loadIntoProj->set(),
			
		'');
		
		
		
		// Lob 
		$humans->_if( $invokedspell->exactly(2) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke lob"),
			
			// Distance
			$FindDistance->set(),
				$DistanceOriginIndex        ->setTo(self::_Hero),
				$DistanceDestinationIndex   ->setTo(self::_Point1),
			$MaxRangeIndex->setTo(self::_DistResize),
				$MaxCastRange->setTo(256),
			
			// Angle
			$AngleIndex->setTo(self::_Calc),
				$ComponentOriginIndex       ->setTo(self::_Hero),
				$ComponentDestinationIndex  ->setTo(self::_Point1),
			$FindComponents->set(),
			
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(1),   // vel *= distance
			$VelocityMultiplier->setTo(0),
			$VelocityDivisor->setTo(16),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			$VelocityRawY->setTo(6400-2625),    	// 6400 is zero
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800+350),
			
			// Set Duration
			$duration->setTo(16),
			
			// Cast
			$loadIntoProj->set(),
				
		'');
		
		
		// Firebreath
		#$humans->_if( $invokedspell->exactly(3), $spellStage->exactly(1) )->then(
		#	
		#	// Stage prep (for persistent spells)
		#	Display("Ended firebreath"),
		#	$spellCast->setTo(0),
		#	$spellStage->setTo(0),
		#
		#'');
		$humans->_if( $spellCast->exactly(3), $spellStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			
			// Distance
			
			// Angle
			$AngleIndex->setTo(self::_Load),
				$ComponentOriginIndex       ->setTo(self::_Hero),
			$AngleAlterationsIndex->setTo(self::_shiftTo),
				$AngleAlterationsValue->setTo(30),
			$SaveAngle->set(),
			$FindComponents->set(),
			
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(0),   // none
			$VelocityMultiplier->setTo(48),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(4),
			
			// Cast
			$loadIntoProj->set(),
				
		'');
		$humans->_if( $invokedspell->exactly(3), $spellStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirected firebreath"),
			
			// Angle
			$AngleIndex->setTo(self::_CalcSaveLoad),
				$ComponentDestinationIndex  ->setTo(self::_Point1),
			
		'');
		$humans->_if( $invokedspell->exactly(3), $spellStage->exactly(0) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke firebreath"),
			$spellCast->setTo(3),
			$spellStage->setTo(1),
			
			// Angle
			$AngleIndex->setTo(self::_CalcSave),
				$ComponentOriginIndex       ->setTo(self::_Hero),
				$ComponentDestinationIndex  ->setTo(self::_Point1),
			$SaveAngle->set(),
			$FindComponents->set(),
				
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(0),   // none
			$VelocityMultiplier->setTo(48),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(4),
			
			// Cast
			$loadIntoProj->set(),
			
		'');
		
		
		// Holocaust
		$humans->_if( $spellCast->exactly(4), $spellStage->exactly(3) )->then(
			
			// Stage prep (for persistent spells)
			$spellCast->setTo(0),
			$spellStage->setTo(0),
			
			// Distance
			
			// Angle
			$AngleIndex->setTo(self::_Load),
				$ComponentOriginIndex       ->setTo(self::_Hero),
			$FindComponents->set(),
			
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$PositionLoadIndex->setTo(3),           // Perp. right
				$PositionMultiplier->setTo(96),
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(0),   // none
			$VelocityMultiplier->setTo(4),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(32),
			
			// Cast
			$loadIntoProj->set(),
				
		'');
		$humans->_if( $spellCast->exactly(4), $spellStage->exactly(2) )->then(
			
			// Stage prep (for persistent spells)
			$spellStage->setTo(3),
			
			// Distance
			
			// Angle
			$AngleIndex->setTo(self::_Load),
				$ComponentOriginIndex       ->setTo(self::_Hero),
			$FindComponents->set(),
			
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$PositionLoadIndex->setTo(2),           // Perp. left
				$PositionMultiplier->setTo(96),
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(0),   // none
			$VelocityMultiplier->setTo(4),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(32),
			
			// Cast
			$loadIntoProj->set(),
				
		'');
		$humans->_if( $spellCast->exactly(4), $spellStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			$spellStage->setTo(2),
			
			// Distance
			
			// Angle
			$AngleIndex->setTo(self::_Load),
				$ComponentOriginIndex       ->setTo(self::_Hero),
			$FindComponents->set(),
			
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$PositionLoadIndex->setTo(3),           // Perp. right
				$PositionMultiplier->setTo(32),
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(0),   // none
			$VelocityMultiplier->setTo(4),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(32),
			
			// Cast
			$loadIntoProj->set(),
				
		'');
		$humans->_if( $invokedspell->exactly(4), $spellStage->exactly(0) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Holocaust"),
			$spellCast->setTo(4),
			$spellStage->setTo(1),
			
			// Angle
			$AngleIndex->setTo(self::_CalcSave),
				$ComponentOriginIndex       ->setTo(self::_Hero),
				$ComponentDestinationIndex  ->setTo(self::_Point1),
			$SaveAngle->set(),
			$FindComponents->set(),
				
			// Set Position
			$PositionIndex->setTo(1),               // Load Angle's Origin
			$PositionLoadIndex->setTo(2),           // Perp. left
				$PositionMultiplier->setTo(32),
			$StaticOffsetX->setTo(0),
			$StaticOffsetY->setTo(0),
			
			// Set Velocity
			$VelocityLoadIndex->setTo(1),           // Load components
			$VelocityMultiplyByDCIndex->setTo(0),   // none
			$VelocityMultiplier->setTo(4),
			$VelocityDivisor->setTo(0),
			$VelocityAdjustForSigned->setTo(1), 	// Add/subtracts for signed
			
			// Set Acceleration
			$accelerationx->setTo(800),
			$accelerationy->setTo(800),
			
			// Set Duration
			$duration->setTo(32),
			
			// Cast
			$loadIntoProj->set(),
			
		'');
		
		
		
		
		
		
		
		//////
		// SPELL CONSTRUCTOR
		///
		
		$distX1 = new TempDC($xmax);
		$distY1 = new TempDC($ymax);
		$distX2 = new TempDC($xmax);
		$distY2 = new TempDC($ymax);
		
		$compX1 = new TempDC($xmax);
		$compY1 = new TempDC($ymax);
		$compX2 = new TempDC($xmax);
		$compY2 = new TempDC($ymax);
		
		
		
		// Check for available projectile slots
		// TODO: TEMPORARY
		$humans->_if( $loadIntoProj )->then(
			$this->firstAvailableProj($selectedSpell, $success),
			_if( $success->is_clear() )->then(
				Display("All available projectiles are in use"),
				$accelerationx->setTo(0), $accelerationy->setTo(0), $angle->setTo(0), $AngleAlterationsIndex->setTo(0), $AngleAlterationsValue->setTo(0),
				$AngleIndex->setTo(0), $ComponentDestinationIndex->setTo(0), $ComponentOriginIndex->setTo(0), $distance->setTo(0), $DistanceDestinationIndex->setTo(0),
				$DistanceOriginIndex->setTo(0), $distX1->setTo(0), $distX2->setTo(0), $distY1->setTo(0), $distY2->setTo(0), $duration->setTo(0), $MaxCastRange->setTo(0),
				$MaxRangeIndex->setTo(0), $point1X->setTo(0), $point1Y->setTo(0), $point2X->setTo(0), $point2Y->setTo(0), $PositionIndex->setTo(0), $positionx->setTo(0),
				$positiony->setTo(0), $selectedSpell->setTo(0), $StaticOffsetX->setTo(0), $StaticOffsetY->setTo(0), $VelocityAdjustForSigned->setTo(0),
				$VelocityDivisor->setTo(0), $VelocityLoadIndex->setTo(0), $VelocityMultiplier->setTo(0), $VelocityMultiplyByDCIndex->setTo(0), $VelocityRawY->setTo(0),
				$velocityx->setTo(0), $velocityy->setTo(0),
				$FindComponents->clear(), $FindDistance->clear(), $loadIntoProj->clear(), $SaveAngle->clear(),
			''),
			$success->clear(),
		'');	
		
		
		// DISTANCE
		
		$humans->_if( $FindDistance )->then(
			
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
			
			// calc distance
		    $distance->distance($distX1, $distY1, $distX2, $distY2),
			
			// if distance exceeds, do specified
			_if( $distance->greaterThan($MaxCastRange) )->then(
				_if( $MaxRangeIndex->exactly(self::_DistResize) )->then(
					$distance->setTo($MaxCastRange),
				''),
				_if( $MaxRangeIndex->exactly(self::_DistCancel) )->then(
					Display("Out of range!"),
					$accelerationx->setTo(0), $accelerationy->setTo(0), $angle->setTo(0), $AngleAlterationsIndex->setTo(0), $AngleAlterationsValue->setTo(0),
					$AngleIndex->setTo(0), $ComponentDestinationIndex->setTo(0), $ComponentOriginIndex->setTo(0), $distance->setTo(0), $DistanceDestinationIndex->setTo(0),
					$DistanceOriginIndex->setTo(0), $distX1->setTo(0), $distX2->setTo(0), $distY1->setTo(0), $distY2->setTo(0), $duration->setTo(0), $MaxCastRange->setTo(0),
					$MaxRangeIndex->setTo(0), $point1X->setTo(0), $point1Y->setTo(0), $point2X->setTo(0), $point2Y->setTo(0), $PositionIndex->setTo(0), $positionx->setTo(0),
					$positiony->setTo(0), $selectedSpell->setTo(0), $StaticOffsetX->setTo(0), $StaticOffsetY->setTo(0), $VelocityAdjustForSigned->setTo(0),
					$VelocityDivisor->setTo(0), $VelocityLoadIndex->setTo(0), $VelocityMultiplier->setTo(0), $VelocityMultiplyByDCIndex->setTo(0), $VelocityRawY->setTo(0),
					$velocityx->setTo(0), $velocityy->setTo(0),
					$FindComponents->clear(), $FindDistance->clear(), $loadIntoProj->clear(), $SaveAngle->clear(),
				''),
			''),
			
		'');
		
		
		
		// ANGLE
		
		// Set Origin and Destination
		$humans->_if( $ComponentOriginIndex->exactly(self::_Hero) )->then(
			$compX1->setTo($bsX->CP),
			$compY1->setTo($bsY->CP),
		'');
		$humans->_if( $ComponentOriginIndex->exactly(self::_Point1) )->then(
			$compX1->setTo($point1X),
			$compY1->setTo($point1Y),
		'');
		$humans->_if( $ComponentOriginIndex->exactly(self::_Point2) )->then(
			$compX1->setTo($point2X),
			$compY1->setTo($point2Y),
		'');
		
		$humans->_if( $ComponentDestinationIndex->exactly(self::_Hero) )->then(
			$compX2->setTo($bsX->CP),
			$compY2->setTo($bsY->CP),
		'');
		$humans->_if( $ComponentDestinationIndex->exactly(self::_Point1) )->then(
			$compX2->setTo($point1X),
			$compY2->setTo($point1Y),
		'');
		$humans->_if( $ComponentDestinationIndex->exactly(self::_Point2) )->then(
			$compX2->setTo($point2X),
			$compY2->setTo($point2Y),
		'');
		
		// Calculate angle
		$humans->_if( $AngleIndex->between(self::_Calc, self::_CalcSaveLoad) )->then(
		    $angle->getAngle($compX1, $compY1, $compX2, $compY2),
		'');
		
		// Save destination angle
		$humans->_if( $AngleIndex->between(self::_CalcSave, self::_CalcSaveLoad) )->then(
			$StoredDestinationAngle->setTo($angle),
		'');
		
		// Load angle
		$humans->_if( $AngleIndex->between(self::_CalcSaveLoad, self::_Load) )->then(
		    $angle->setTo($StoredCurrentAngle),
		'');
		
		// Alter angle
		$humans->_if( $AngleAlterationsIndex->exactly(self::_addConst) )->then(
		    $angle->add($AngleAlterationsValue),
			_if( $angle->atLeast(1440) )->then(
				$angle->subtract(1440),
			''),
		'');
		$humans->_if( $AngleAlterationsIndex->exactly(self::_shiftTo) )->then(
			$temp1->max(1440),
			$temp2->max(1440),
			$temp1->absDifference($angle, $StoredDestinationAngle, $sign),
			_if( $temp1->atLeast(721) )->then(
				$temp2->setTo($temp1),
				$temp1->setTo(1440),
				$temp1->subtract($temp2),
				$sign->toggle(),
			''),
			
			_if( $temp1->greaterThan($AngleAlterationsValue) )->then(
				$temp1->setTo($AngleAlterationsValue),
			''),
			
			_if( $sign->is_clear() )->then(
		        $angle->add($temp1),
			''),
			_if( $sign->is_set() )->then(
				$sign->clear(),
				$angle->add(1440),
		        $angle->subtract($temp1),
			''),
			_if( $angle->atLeast(1440) )->then(
				$angle->subtract(1440),
			''),
			$temp1->max(256000),
			$temp2->max(256000),
		'');
		
		// Save angle
		$humans->_if( $SaveAngle )->then(
			$StoredCurrentAngle->setTo($angle),
		'');
		
		
		
		// COMPONENTS
		$humans->_if( $FindComponents )->then(
		    $angle->componentsInto($xcomponent, $ycomponent),
		'');
		
		
		// POSITION
		$projowners->_if( $PositionIndex->atLeast(1) )->then(
			
			$positionx->setTo(0),
			$positiony->setTo(0),
			
			// dynamic offset
			_if( $PositionLoadIndex->atLeast(1) )->then(
				_if( $PositionLoadIndex->exactly(1) )->then(
					//Display("loading xycomponents"),
					$positionx->roundedQuotientOf($xcomponent, 10),
					$positiony->roundedQuotientOf($ycomponent, 10),
				''),
				_if( $PositionLoadIndex->atLeast(2) )->then(
					//Display("loading xycomponents"),
					$positionx->roundedQuotientOf($ycomponent, 10),
					$positiony->roundedQuotientOf($xcomponent, 10),
				''),
				
				$positionx->Max(1000),
				$positiony->Max(1000),
				
				_if( $PositionMultiplier->atLeast(1) )->then(
					//Display("multiplying velocities by static value"),
					$positionx->multiplyBy($PositionMultiplier),
					$positiony->multiplyBy($PositionMultiplier),
				''),
				
				$positionx->Max(128000),
				$positiony->Max(128000),
				
				$tempx->roundedQuotientOf($positionx, 1000),
				$tempy->roundedQuotientOf($positiony, 1000),
				
				$tempx->Max(128),
				$tempy->Max(128),
				
				$positionx->Max(Map::getWidth()*32-1),
				$positiony->Max(Map::getHeight()*32-1),
			''),
			
			// add origin coordinate
			$positionx->setTo($compX1),
			$positiony->setTo($compY1),
			
			// Add/Subtract for signed
			_if( $PositionLoadIndex->atLeast(1) )->then(
				
				_if( $PositionLoadIndex->exactly(2) )->then( //perp left
					$angle->add(360*4-90*4),
					_if($angle->atLeast(1440))->then(
						$angle->subtract(1440),
					''),
				''),
				_if( $PositionLoadIndex->exactly(3) )->then( //perp right
					$angle->add(90*4),
					_if($angle->atLeast(1440))->then(
						$angle->subtract(1440),
					''),
				''),
				
				_if( $angle->between(361,1079) )->then(
					//Display(" velocity <--"),
					$positionx->subtract($tempx),
				e)->_else(
					//Display(" velocity -->"),
					$positionx->add($tempx),
				''),
				_if( $angle->atMost(719) )->then(
					//Display(" velocity ^"),
					$positiony->subtract($tempy),
				''),
				_if( $angle->atLeast(720) )->then(
					//Display(" velocity V"),
					$positiony->add($tempy),
				''),
				
				_if( $PositionLoadIndex->exactly(2) )->then( //perp right
					$angle->add(90*4),
					_if($angle->atLeast(1440))->then(
						$angle->subtract(1440),
					''),
				''),
				_if( $PositionLoadIndex->exactly(3) )->then( //perp left
					$angle->add(360*4-90*4),
					_if($angle->atLeast(1440))->then(
						$angle->subtract(1440),
					''),
				''),
				
				$tempx->Max(256000),
				$tempy->Max(256000),
				
			''),
			
			// Add Offsets
			_if( $StaticOffsetX->atLeast(1) )->then(
				$positionx->add($StaticOffsetX),
				$positionx->subtract(6400),
			''),
			_if( $StaticOffsetY->atLeast(1) )->then(
				$positiony->add($StaticOffsetY),
				$positiony->subtract(6400),
			''),
			
			// TODO: more position stuff
		'');
		
		
		// VELOCITY
		$projowners->_if( $VelocityLoadIndex->atLeast(1) )->then(
			
			$velocityx->setTo(0),
			$velocityy->setTo(0),
			
			_if( $VelocityLoadIndex->exactly(1) )->then(
				//Display("loading xycomponents"),
				$velocityx->roundedQuotientOf($xcomponent, 10),
				$velocityy->roundedQuotientOf($ycomponent, 10),
			''),
			
			$velocityx->Max(1000),
			$velocityy->Max(1000),
			
			_if( $VelocityMultiplyByDCIndex->exactly(1) )->then(
				//Display("multiplying velocities by the distance"),
				$VelocityMultiplier->setTo($distance),
			''),
			
			_if( $VelocityMultiplier->atLeast(1) )->then(
				//Display("multiplying velocities by static value"),
				$velocityx->multiplyBy($VelocityMultiplier),
				$velocityy->multiplyBy($VelocityMultiplier),
			''),
			
			$velocityx->Max(256000),
			$velocityy->Max(256000),
			
			_if( $VelocityDivisor->atLeast(1) )->then(
				//Display("dividing velocities by static value"),
				$velocityx->roundedDivideBy($VelocityDivisor),
				$velocityy->roundedDivideBy($VelocityDivisor),
			''),
			
			$velocityx->Max(64000),
			$velocityy->Max(64000),
			
			// Add/Subtract for signed
			_if( $VelocityAdjustForSigned->atLeast(1) )->then(
				//Display("adjusting for signed velocity"),
				$tempx->roundedQuotientOf($velocityx, 10),
				$tempy->roundedQuotientOf($velocityy, 10),
				
				$tempx->Max(6400),
				$tempy->Max(6400),
				
				$velocityx->setTo(6400),
				$velocityy->setTo(6400),
				
				_if( $angle->between(361,1079) )->then(
					//Display(" velocity <--"),
					$velocityx->subtract($tempx),
				e)->_else(
					//Display(" velocity -->"),
					$velocityx->add($tempx),
				''),
				_if( $angle->atMost(719) )->then(
					//Display(" velocity ^"),
					$velocityy->subtract($tempy),
				''),
				_if( $angle->atLeast(720) )->then(
					//Display(" velocity V"),
					$velocityy->add($tempy),
				''),
				
				$velocityx->Max(12800),
				$velocityy->Max(12800),
				
			''),
			
			_if( $VelocityRawY->atLeast(1) )->then(
				$velocityy->add($VelocityRawY),
				$velocityy->subtract(6400),
			''),
			
		'');
			
			
		// ACCELERATION
		#$projowners->_if( )->then(
			#$accelerationx->setTo(800), // aka zero
			#$accelerationy->setTo(800), // aka zero
		#)
		
		
		// LOAD PROJECTILE
		$projowners->_if( $loadIntoProj )->then(
		    $this->loadIntoProjectiles($positionx, $positiony, $velocityx, $velocityy, $accelerationx, $accelerationy, $duration, $success),
			_if( $success->is_clear() )->then(
				//Display("Failed to load spell (projectiles are all taken?)"),
			''),
		'');
		
		
		
		// Cleanup
		$projowners->always(
			$frags->release(),
			$invokedspell->release(),
			$DistanceOriginIndex->release(),
			$DistanceDestinationIndex->release(),
			$FindDistance->release(),
			$MaxRangeIndex->release(),
			$MaxCastRange->release(),
			$ComponentOriginIndex->release(),
			$ComponentDestinationIndex->release(),
			$AngleIndex->release(),
			$AngleAlterationsIndex->release(),
			$AngleAlterationsValue->release(),
			$SaveAngle->release(),
			$FindComponents->release(),
			$PositionIndex->release(),
			$PositionLoadIndex->release(),
			$PositionMultiplier->release(),
			$VelocityLoadIndex->release(),
			$StaticOffsetX->release(),
			$StaticOffsetY->release(),
			$VelocityMultiplyByDCIndex->release(),
			$VelocityMultiplier->release(),
			$VelocityDivisor->release(),
			$VelocityRawY->release(),
			$VelocityAdjustForSigned->release(),
			$tempx->release(),
			$tempy->release(),
			$distX1->release(),
			$distY1->release(),
			$distX2->release(),
			$distY2->release(),
			$compX1->release(),
			$compY1->release(),
			$compX2->release(),
			$compY2->release(),
			$distance->release(),
			$angle->release(),
			$xcomponent->release(),
			$ycomponent->release(),
			$success->release(),
			$loadIntoProj->release(),
			$positionx->release(),
			$positiony->release(),
			$velocityx->release(),
			$velocityy->release(),
			$accelerationx->release(),
			$accelerationy->release(),
			$duration->release(),
		'');
			
		
		
		/**
		$bam = new Sound("bam");
		
		foreach($this->Projectiles as $projectile){
			$P1->_if( $projectile->duration->exactly(1) )->then(
				FX::rumbleAt(10, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
		}
		/**/
		$projowners->always(
			$this->projectileEngine(),
		'');
		

		
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
	
	function firstAvailableProj($proj, TempSwitch $success){
		$text = '';
		
		foreach ($this->CPprojectiles as $key=>$projectile ){
			$text .= _if( $projectile->notInUse(), $success->is_clear() )->then(
				$proj->setTo($key+1),
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