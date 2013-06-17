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
	
	
	
	// coordinate controller
	const _Hero         = 1;
	const _Point1       = 2;
	const _Proj         = 3;
	const _Saved        = 4;
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
	// angle specifier
	const _Ahead        = 1;
	const _Behind       = 2;
	const _Left         = 3;
	const _Right        = 4;
	
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
			$this->eventTime[]      = $eventTime = new Deathcounter($projowners, 720);
			$this->spellidDCs[]     = $spellid   = new Deathcounter($projowners, 100);
			
			$this->CPprojectiles[] = new Projectile(array($xpos->CP, $ypos->CP, $xpospart->CP, $ypospart->CP, $xvel->CP, $yvel->CP, $xacc->CP, $yacc->CP, $duration->CP, $eventTime->CP, $spellid->CP));
			$this->Projectiles[] = $this->P4projectiles[] = new Projectile(array($xpos->P4, $ypos->P4, $xpospart->P4, $ypospart->P4, $xvel->P4, $yvel->P4, $xacc->P4, $yacc->P4, $duration->P4, $eventTime->P4, $spellid->P4));
			$this->Projectiles[] = $this->P5projectiles[] = new Projectile(array($xpos->P5, $ypos->P5, $xpospart->P5, $ypospart->P5, $xvel->P5, $yvel->P5, $xacc->P5, $yacc->P5, $duration->P5, $eventTime->P5, $spellid->P5));
			$this->Projectiles[] = $this->P6projectiles[] = new Projectile(array($xpos->P6, $ypos->P6, $xpospart->P6, $ypospart->P6, $xvel->P6, $yvel->P6, $xacc->P6, $yacc->P6, $duration->P6, $eventTime->P6, $spellid->P6));
			$this->Projectiles[] = $this->P7projectiles[] = new Projectile(array($xpos->P7, $ypos->P7, $xpospart->P7, $ypospart->P7, $xvel->P7, $yvel->P7, $xacc->P7, $yacc->P7, $duration->P7, $eventTime->P7, $spellid->P7));
			$this->Projectiles[] = $this->P8projectiles[] = new Projectile(array($xpos->P8, $ypos->P8, $xpospart->P8, $ypospart->P8, $xvel->P8, $yvel->P8, $xacc->P8, $yacc->P8, $duration->P8, $eventTime->P8, $spellid->P8));
			
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
			$this->SpellSlotDCs[1]->setTo(self::_Zap), // fireball
			$this->SpellSlotDCs[2]->setTo(self::_Lob), // lob
			$this->SpellSlotDCs[3]->setTo(self::_Holocaust), // holocaust
			$this->SpellSlotDCs[4]->setTo(self::_DanceOfFlames), // dance of flames
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
		
		$savedProj =                    new Deathcounter($humans, 4);
		$spellCast =                    new Deathcounter($humans);
		$castStage =                    new Deathcounter($humans, 15);
		
		// Saved DCs
		$SaveAngle =                    new TempSwitch();
		$StoredCurrentAngle =           new Deathcounter($humans, 1440);
		$StoredDestinationAngle =       new Deathcounter($humans, 1440);
		$StoredX =                      new Deathcounter($humans, FRAGS::$x->Max);
		$StoredY =                      new Deathcounter($humans, FRAGS::$y->Max);
		
		
		// Projectile Variables
		$positionx =                    new TempDC(Map::getWidth()*32-1);
		$positiony =                    new TempDC(Map::getHeight()*32-1);
		$velocityx =                    new TempDC(12800);
		$velocityy =                    new TempDC(12800);
		$accelerationx =                new TempDC(1600);
		$accelerationy =                new TempDC(1600);
		$duration =                     new TempDC(720);
		$eventTime =                    new TempDC(720);
		$spellid =                      new TempDC(127);
		
		
		// SPELL VARIABLES
		
		// for selecting proj
		$SetFirstAvailableProj =        new TempSwitch();
		$SetSelectProj =                new TempSwitch();
		$selectedProj =                 new TempDC(4);
		$selectedProjID =               new TempDC(100);
		$firstAvailableProj =           new TempDC(4);
		
		// for distance
		$DistanceOriginIndex =          new TempDC();
		$DistanceDestinationIndex =     new TempDC();
		
		$FindDistance =                 new TempSwitch();
		$distance =                     new TempDC(256);
		
		$MaxRangeIndex =                new TempDC();
		$MaxCastRange =                 new TempDC(512);
		
		// for coordinates and angle
		$SaveCoordinates =              new TempSwitch();
		
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
		$SetPosition =                  new TempSwitch();
		$PositionIndex =                new TempDC();
		$PositionLoadIndex =            new TempDC();
		$PositionMultiplier =           new TempDC(144);
		$StaticOffsetX =                new TempDC(12800);
		$StaticOffsetY =                new TempDC(12800);
		
		$tempx = $temp1 =               new TempDC(256000);
		$tempy = $temp2 =               new TempDC(256000);
		
		// for velocity
		$SetVelocity =                  new TempSwitch();
		$VelocityLoadIndex =            new TempDC();
		$VelocityMultiplyByDCIndex =    new TempDC();
		$VelocityMultiplier =           new TempDC(256);
		$VelocityDivisor =              new TempDC(100);
		$VelocityRawY =                 new TempDC(12800);
		
		// for acceleration
		$SetAcceleration =              new TempSwitch();
		$AccelerationLoadIndex =        new TempDC();
		$AccelerationMultiplier =       new TempDC(8);
		$AccelerationRawY =             new TempDC(1600);
		
		// miscellaneous
		$frags =                        new TempSwitch();
		$enableSpellSystem =            new TempSwitch();
		$invokedslot =                  new Deathcounter($humans, 104);
		$invokedspell =                 new TempDC(50);
		$success = $sign =              new TempSwitch();
		$projCount =                    new TempDC(4);
		$loadIntoProj =                 new TempSwitch();
		
		// 
		$humans->_if( $invokedslot->atLeast(1) )->then(
			$invokedslot->subtract(100),
		'');
		
		// Set invokedslot
		foreach($this->P4casterunits as $key=>$casterunit){
			$P4->_if( $casterunit->orderCoordinate(AtLeast, 1) )->then(
				$invokedslot->setTo($key+100),
				Loc::$aoe1x1->placeAt($casterunit->x, $casterunit->y),
				$casterunit->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
		}
		foreach($this->P5casterunits as $key=>$casterunit){
			$P5->_if( $casterunit->orderCoordinate(AtLeast, 1) )->then(
				$invokedslot->setTo($key+100),
				Loc::$aoe1x1->placeAt($casterunit->x, $casterunit->y),
				$casterunit->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
		}
		foreach($this->P6casterunits as $key=>$casterunit){
			$P6->_if( $casterunit->orderCoordinate(AtLeast, 1) )->then(
				$invokedslot->setTo($key+100),
				Loc::$aoe1x1->placeAt($casterunit->x, $casterunit->y),
				$casterunit->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
		}
		
		
		// Set $frags
		$humans->_if( FRAGS::$Fragged->atLeast(1), $invokedslot->between(1, 99) )->then(
			FRAGS::$Fragged->setTo(0),
			$frags->set(),
		'');
		
		// Set invokedspell
		foreach($this->SpellSlotDCs as $key=>$spellslotdc){
			$humans->_if( $frags, $invokedslot->exactly($key) )->then(
				$invokedspell->setTo($spellslotdc->CP),
				$invokedslot->setTo(0),
			'');
		}
		
		$humans->_if( $frags )->then(
			//$point2X->setTo($point1X),
			//$point2Y->setTo($point1Y),
			$point1X->setTo(FRAGS::$x->CP),
			$point1Y->setTo(FRAGS::$y->CP),
		'');
		
		
		
		
		// select projectile
		$humans->_if( $frags )->then(
			//  enable
			_if( $invokedspell->exactly(12) )->then( $success->set() ),
			
			// select projectile
			_if( $success->is_set() )->then(
				$this->findSelected($selectedProj, $selectedProjID),
			''),
		'');
		// first available proj
		$humans->_if( $frags )->then(
			// get first available projectile slot
			$this->firstAvailableProj($projCount, $firstAvailableProj),
		'');
		
		
		
		
		
		//////
		// SPELL CONTROLLER
		///
		
		include("SpellDefinitions.php");
		
		
		
		
		//Hello?
		
		
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
		
		

		$humans->_if( $enableSpellSystem )->then(
			
			// PROJECTILE
			_if( $SetFirstAvailableProj->is_set() )->then(
				$savedProj->setTo($firstAvailableProj),
			''),
			_if( $SetSelectProj->is_set() )->then(
				$savedProj->setTo($selectedProj),
			''),
			
			
			
			// save point
			_if( $SaveCoordinates->is_set() )->then(
				$StoredX->setTo($point1X),
				$StoredY->setTo($point1Y),
			''),

			
			// DISTANCE
			_if( $FindDistance )->then(
				
				// Load the Distance Calculation's Origin and Destination
				_if( $DistanceOriginIndex->exactly(self::_Hero) )->then(
					$distX1->setTo($bsX->CP),
					$distY1->setTo($bsY->CP),
				''),
				_if( $DistanceOriginIndex->exactly(self::_Point1) )->then(
					$distX1->setTo($point1X),
					$distY1->setTo($point1Y),
				''),
				_if( $DistanceOriginIndex->exactly(self::_Proj) )->then(
					$this->loadProjCoordinates($savedProj, $distX1, $distY1),
				''),
				_if( $DistanceOriginIndex->exactly(self::_Saved) )->then(
					$distX1->setTo($StoredX),
					$distY1->setTo($StoredY),
				''),
				
				
				_if( $DistanceDestinationIndex->exactly(self::_Hero) )->then(
					$distX2->setTo($bsX->CP),
					$distY2->setTo($bsY->CP),
				''),
				_if( $DistanceDestinationIndex->exactly(self::_Point1) )->then(
					$distX2->setTo($point1X),
					$distY2->setTo($point1Y),
				''),
				_if( $DistanceDestinationIndex->exactly(self::_Proj) )->then(
					$this->loadProjCoordinates($savedProj, $distX2, $distY2),
				''),
				_if( $DistanceOriginIndex->exactly(self::_Saved) )->then(
					$distX2->setTo($StoredX),
					$distY2->setTo($StoredY),
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
						$SetPosition->clear(), $SetVelocity->clear(), $SetAcceleration->clear(), $loadIntoProj->clear(),
					''),
				''),
				
			''),
			
			
			
			// COORDINATES
			
			// Set Origin and Destination
			_if( $ComponentOriginIndex->exactly(self::_Hero) )->then(
				$compX1->setTo($bsX->CP),
				$compY1->setTo($bsY->CP),
			''),
			_if( $ComponentOriginIndex->exactly(self::_Point1) )->then(
				$compX1->setTo($point1X),
				$compY1->setTo($point1Y),
			''),
			_if( $ComponentOriginIndex->exactly(self::_Proj) )->then(
				$this->loadProjCoordinates($savedProj, $compX1, $compY1),
			''),
			_if( $ComponentOriginIndex->exactly(self::_Saved) )->then(
				$compX1->setTo($StoredX),
				$compY1->setTo($StoredY),
			''),
			
			_if( $ComponentDestinationIndex->exactly(self::_Hero) )->then(
				$compX2->setTo($bsX->CP),
				$compY2->setTo($bsY->CP),
			''),
			_if( $ComponentDestinationIndex->exactly(self::_Point1) )->then(
				$compX2->setTo($point1X),
				$compY2->setTo($point1Y),
			''),
			_if( $ComponentDestinationIndex->exactly(self::_Proj) )->then(
				$this->loadProjCoordinates($savedProj, $compX2, $compY2),
			''),
			_if( $ComponentDestinationIndex->exactly(self::_Saved) )->then(
				$compX2->setTo($StoredX),
				$compY2->setTo($StoredY),
			''),
	
			
			
			// ANGLE
			
			// Calculate angle
			_if( $AngleIndex->between(self::_Calc, self::_CalcSaveLoad) )->then(
			    $angle->getAngle($compX1, $compY1, $compX2, $compY2),
			''),
			
			// Save destination angle
			_if( $AngleIndex->between(self::_CalcSave, self::_CalcSaveLoad) )->then(
				$StoredDestinationAngle->setTo($angle),
			''),
			
			// Load angle
			_if( $AngleIndex->between(self::_CalcSaveLoad, self::_Load) )->then(
			    $angle->setTo($StoredCurrentAngle),
			''),
			
			// Alter angle
			_if( $AngleAlterationsIndex->exactly(self::_addConst) )->then(
			    $angle->add($AngleAlterationsValue),
				_if( $angle->atLeast(1440) )->then(
					$angle->subtract(1440),
				''),
			''),
			_if( $AngleAlterationsIndex->exactly(self::_shiftTo) )->then(
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
			''),
			
			// Save angle
			_if( $SaveAngle )->then(
				$StoredCurrentAngle->setTo($angle),
			''),
			
			
			
			// COMPONENTS
			_if( $FindComponents )->then(
			    $angle->componentsInto($xcomponent, $ycomponent),
			''),
			
			
			
			// POSITION
			
			// dynamic offset
			_if( $SetPosition->is_set() )->then(
				_if( $PositionLoadIndex->atLeast(1) )->then(
					_if( $PositionLoadIndex->between(self::_Ahead, self::_Behind) )->then(
						$positionx->roundedQuotientOf($xcomponent, 10),
						$positiony->roundedQuotientOf($ycomponent, 10),
					''),
					_if( $PositionLoadIndex->between(self::_Left, self::_Right) )->then(
						$positionx->roundedQuotientOf($ycomponent, 10),
						$positiony->roundedQuotientOf($xcomponent, 10),
					''),
					
					$positionx->Max(1000),
					$positiony->Max(1000),
					
					_if( $PositionMultiplier->atLeast(1) )->then(
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
					
					// alter angles
					_if( $PositionLoadIndex->exactly(self::_Left) )->then( //perp left
						$angle->add(360*4-90*4),
						_if($angle->atLeast(1440))->then(
							$angle->subtract(1440),
						''),
					''),
					_if( $PositionLoadIndex->exactly(self::_Right) )->then( //perp right
						$angle->add(90*4),
						_if($angle->atLeast(1440))->then(
							$angle->subtract(1440),
						''),
					''),
					
					// account for signs
					_if( $angle->between(361,1079) )->then(
						$positionx->subtract($tempx),
					e)->_else(
						$positionx->add($tempx),
					''),
					_if( $angle->atMost(719) )->then(
						$positiony->subtract($tempy),
					''),
					_if( $angle->atLeast(720) )->then(
						$positiony->add($tempy),
					''),
					
					// restore angles
					_if( $PositionLoadIndex->exactly(self::_Left) )->then( //perp right
						$angle->add(90*4),
						_if($angle->atLeast(1440))->then(
							$angle->subtract(1440),
						''),
					''),
					_if( $PositionLoadIndex->exactly(self::_Right) )->then( //perp left
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
			''),
			
			
			
			// VELOCITY
			_if( $SetVelocity->is_set() )->then(
				_if( $VelocityLoadIndex->atLeast(1) )->then(
					
					_if( $VelocityLoadIndex->exactly(self::_Ahead) )->then(
						$velocityx->roundedQuotientOf($xcomponent, 10),
						$velocityy->roundedQuotientOf($ycomponent, 10),
					''),
					
					$velocityx->Max(1000),
					$velocityy->Max(1000),
					
					_if( $VelocityMultiplyByDCIndex->exactly(1) )->then(
						$VelocityMultiplier->setTo($distance),
					''),
					
					_if( $VelocityMultiplier->atLeast(1) )->then(
						$velocityx->multiplyBy($VelocityMultiplier),
						$velocityy->multiplyBy($VelocityMultiplier),
					''),
					
					$velocityx->Max(256000),
					$velocityy->Max(256000),
					
					_if( $VelocityDivisor->atLeast(1) )->then(
						$velocityx->roundedDivideBy($VelocityDivisor),
						$velocityy->roundedDivideBy($VelocityDivisor),
					''),
					
					$velocityx->Max(64000),
					$velocityy->Max(64000),
					
					// Add/Subtract for signed
					$tempx->roundedQuotientOf($velocityx, 10),
					$tempy->roundedQuotientOf($velocityy, 10),
					
					$tempx->Max(6400),
					$tempy->Max(6400),
					
				''),
								
				$velocityx->setTo(6400),
				$velocityy->setTo(6400),
					
				_if( $VelocityLoadIndex->atLeast(1) )->then(
					
					_if( $angle->between(361,1079) )->then(
						$velocityx->subtract($tempx),
					e)->_else(
						$velocityx->add($tempx),
					''),
					_if( $angle->atMost(719) )->then(
						$velocityy->subtract($tempy),
					''),
					_if( $angle->atLeast(720) )->then(
						$velocityy->add($tempy),
					''),
					
					$velocityx->Max(12800),
					$velocityy->Max(12800),
					
				''),
				
				_if( $VelocityRawY->atLeast(1) )->then(
					$velocityy->add($VelocityRawY),
					$velocityy->subtract(6400),
				''),
			''),
				
				
			
			// ACCELERATION
			_if( $SetAcceleration->is_set() )->then(
				_if( $AccelerationLoadIndex->atLeast(1) )->then(
					
					_if( $AccelerationLoadIndex->exactly(self::_Ahead) )->then(
						$accelerationx->roundedQuotientOf($xcomponent, 10),
						$accelerationy->roundedQuotientOf($ycomponent, 10),
					''),
					
					$accelerationx->Max(1000),
					$accelerationy->Max(1000),
					
					_if( $AccelerationMultiplier->atLeast(1) )->then(
						$accelerationx->multiplyBy($AccelerationMultiplier),
						$accelerationy->multiplyBy($AccelerationMultiplier),
					''),
					
					$accelerationx->Max(8000),
					$accelerationy->Max(8000),
					
					// Add/Subtract for signed
					$tempx->roundedQuotientOf($accelerationx, 10),
					$tempy->roundedQuotientOf($accelerationy, 10),
					
					$tempx->Max(800),
					$tempy->Max(800),
					
				''),
						
				$accelerationx->setTo(800),
				$accelerationy->setTo(800),
						
				_if( $AccelerationLoadIndex->atLeast(1) )->then(
					
					_if( $angle->between(361,1079) )->then(
						$accelerationx->subtract($tempx),
					e)->_else(
						$accelerationx->add($tempx),
					''),
					_if( $angle->atMost(719) )->then(
						$accelerationy->subtract($tempy),
					''),
					_if( $angle->atLeast(720) )->then(
						$accelerationy->add($tempy),
					''),
					
					$accelerationx->Max(1600),
					$accelerationy->Max(1600),
					
				''),
				
				_if( $AccelerationRawY->atLeast(1) )->then(
					$accelerationy->add($AccelerationRawY),
					$accelerationy->subtract(800),
				''),
			''),
			
			
			// LOAD PROJECTILE
			_if( $loadIntoProj->is_set() )->then(
			    $this->loadIntoProjectiles($savedProj, $SetPosition, $positionx, $positiony, $SetVelocity, $velocityx, $velocityy, $SetAcceleration, $accelerationx, $accelerationy, $duration, $eventTime, $spellid),
			''),
			
		'');
		
		
		$humans->always(
			// Cleanup
			$frags->release(),
			$invokedspell->release(),
			$SetPosition->release(),
			$SetVelocity->release(),
			$SetAcceleration->release(),
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
			$AccelerationLoadIndex->release(),
			$AccelerationMultiplier->release(),
			$AccelerationRawY->release(),
		'');
		$humans->always(
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
			$projCount->release(),
			$duration->release(),
			$eventTime->release(),
			$spellid->release(),
			$enableSpellSystem->release(),
			$SetFirstAvailableProj->release(),
			$SetSelectProj->release(),
			$firstAvailableProj->release(),
			$selectedProj->release(),
			$selectedProjID->release(),
			$SaveCoordinates->release(),
		
		'');
			
		
		
		
		// SPECIAL EFFECTS, YO
		$bam = new Sound("bam");
		
		foreach($this->Projectiles as $projectile){
			$P1->_if( $projectile->spellid->exactly(self::_Fireball), $projectile->duration->exactly(1) )->then(
				FX::rumbleAt(2, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
			$P1->_if( $projectile->spellid->exactly(self::_Lob), $projectile->duration->exactly(1) )->then(
				FX::rumbleAt(10, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
			$P1->_if( $projectile->spellid->exactly(self::_Zap), $projectile->duration->atLeast(1) )->then(
				FX::rumbleAt(2, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
			$P1->_if( $projectile->spellid->exactly(self::_Holocaust), $projectile->duration->atLeast(1), $projectile->eventTime->exactly(0) )->then(
				FX::rumbleAt(2, $projectile->xpos, $projectile->ypos),
			'');
		}
		
		
		
		// Engine
		$projowners->always(
			$this->projectileEngine(),
		'');
		

		
	}
	
	
	
	
	private function loadProjCoordinates($projectile, $X, $Y){
		$text = '';
		
		foreach($this->CPprojectiles as $key=>$proj){
			$text .= _if( $projectile->exactly($key+1) )->then(
					$X->setTo($proj->xpos),
					$Y->setTo($proj->ypos),
				'');
		}
		
		return $text;
	}
	
	
	
	private function findSelected($selected, $selectedID){
		$text = '';
				
		$xdcs = new DCArray($this->xposDCs);
		$ydcs = new DCArray($this->yposDCs);
		
		$tempx = new TempDC();
		$tempy = new TempDC();
		
		$origin = 32000;
		$text .= repeat(1,						
			// count down dcs for comparison
			$xdcs->add($origin),
			$ydcs->add($origin),
			
			$xdcs->countOff(FRAGS::$x->CP, $tempx),
			$ydcs->countOff(FRAGS::$y->CP, $tempy),
			
			// compare if proj is within 24 pixels of the frag coordinate
			$this->compareProjCoordinates($selected, $selectedID, $origin),
			
			// count up to restore
			$xdcs->countUp($tempx, FRAGS::$x->CP),
			$ydcs->countUp($tempy, FRAGS::$y->CP),
			
			$xdcs->subtract($origin),
			$ydcs->subtract($origin),
			
			$tempx->release(),
			$tempy->release(),
		'');
		
		return $text;
	}
	
	private function compareProjCoordinates($selected, $selectedID, $origin){
		$range = 24/*px*/;
		$text = '';
		
		foreach($this->CPprojectiles as $key=>$proj){
			$text .= _if( 
				$proj->xpos->between($origin-$range, $origin+$range), 
				$proj->ypos->between($origin-$range, $origin+$range), 
				$selected->exactly(0),
				$proj->inUse()
			)->then(
				$selected->setTo($key+1),
				$selectedID->setTo($proj->spellid),
			'');
		}
		return $text;
	}
	

	
	function loadIntoProjectiles($savedProj, $SetPosition, $positionx, $positiony, $SetVelocity, $velocityx, $velocityy, $SetAcceleration, $accelerationx, $accelerationy, $duration, $eventTime, $spellid){
		$text = '';
				
		foreach( $this->CPprojectiles as $key=>$projectile ){
			$text .= _if( $savedProj->exactly($key+1) )->then(
				_if( $SetPosition->is_set() )->then(
					$projectile->setPosition($positionx, $positiony),
				''),
				_if( $SetVelocity->is_set() )->then(
					$projectile->setVelocity($velocityx, $velocityy),
				''),
				_if( $SetAcceleration->is_set() )->then(
					$projectile->setAcceleration($accelerationx, $accelerationy),
				''),
				$projectile->setEventTime($eventTime),
				$projectile->setDuration($duration),
				$projectile->setSpellID($spellid),
			'');
		}
		
		return $text;
	}
	
	function firstAvailableProj($count, $proj){
		$text = $count->setTo(0);
		
		foreach ($this->CPprojectiles as $key=>$projectile ){
			$text .= _if( $projectile->notInUse() )->then(
				$proj->setTo($key+1),
				$count->add(1),
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