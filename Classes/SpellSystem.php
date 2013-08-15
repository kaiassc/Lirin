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
	const _Smite            = 19;
	const _Smite2           = 119;
	const _Spiral           = 20;
	
	
	
	// coordinate controller
	const _Hero         = 1;
	const _Point        = 2;
	const _Saved        = 3;
	const _Proj         = 4;
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
	// multiply/divide index
	const _MultDist     = 1000;
	// load value
	const _LoadMult1    = 1;
	const _LoadMult2    = 2;
	
	
	
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
			$this->SpellSlotDCs[1]->setTo(self::_Fireball),
			$this->SpellSlotDCs[2]->setTo(self::_Lob),
			$this->SpellSlotDCs[3]->setTo(self::_Meteor),
			$this->SpellSlotDCs[4]->setTo(self::_Spiral),
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
		$pointX =                       new Deathcounter($humans, FRAGS::$x->Max);
		$pointY =                       new Deathcounter($humans, FRAGS::$y->Max);
		
		$savedProj =                    new Deathcounter($humans, 4);
		$spellCast =                    new Deathcounter($humans);
		$castStage =                    new Deathcounter($humans, 15);
		$castTimer =                    new Deathcounter($humans, 120);
		
		// Saved DCs
		$SaveAngle =                    new TempSwitch();
		$StoredAngle =                  new Deathcounter($humans, 1440);
		$StoredDestinationAngle =       new Deathcounter($humans, 1440);
		
		
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
		$firstAvailableProj =           new TempDC(4);
		$SetSelectProj =                new TempSwitch();
		$selectedProj =                 new TempDC(4);
		$selectedProjID =               new TempDC(100);
		
		// for distance
		$FindDistance =                 new TempSwitch();
		$distance =                     new TempDC(256);
		
		$MaxRangeIndex =                new TempDC();
		$MaxCastRange =                 new TempDC(512);
		
		// for coordinates and angle
		$OriginIndex =                  new TempDC();
		$DestinationIndex =             new TempDC();
		
		$AngleIndex =                   new TempDC();
		
		$AngleAlterationsIndex =        new TempDC();
		$AngleAlterationsValue =        new TempDC(1440);
		
		$angle =                        new TempDC(1440);
		
		// for components
		$FindComponents =               new TempSwitch();
		$xcomponent =                   new TempDC(10000);
		$ycomponent =                   new TempDC(10000);
		
		// multiplication/division
		$Mult1Value =                   new TempDC(256);
		$Mult1ResultX =                 new TempDC(256000);
		$Mult1ResultY =                 new TempDC(256000);
		$DivValue =                     new TempDC(256);
		$Mult2Value =                   new TempDC(64);
		$Mult2ResultX =                 new TempDC(64000);
		$Mult2ResultY =                 new TempDC(64000);
		
		$tempx = $temp1 =               new TempDC(6400);
		$tempy = $temp2 =               new TempDC(6400);
		
		// position
		$SetPosition =                  new TempSwitch();
		$PositionIndex =                new TempDC();
		$PositionDirection =            new TempDC();
		
		// velocity
		$ClearVelocity =                new TempSwitch();
		$AddVelocity =                  new TempSwitch();
		$VelocityIndex =                new TempDC();
		$VelocityDirection =            new TempDC();
		
		// acceleration
		$ClearAcceleration =            new TempSwitch();
		$AddAcceleration =              new TempSwitch();
		$AccelerationIndex =            new TempDC();
		$AccelerationDirection =        new TempDC();
		
		// miscellaneous
		$frags =                        new TempSwitch();
		$enableSpellSystem =            new TempSwitch();
		$invokedslot =                  new Deathcounter($humans, 104);
		$invokedspell =                 new TempDC(50);
		$success = $sign =              new TempSwitch();
		$projCount =                    new TempDC(4);
		
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
			$pointX->setTo(FRAGS::$x->CP),
			$pointY->setTo(FRAGS::$y->CP),
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
		$humans->_if( $frags, _OR($castTimer->atLeast(1)) )->then(
			// get first available projectile slot
			$this->firstAvailableProj($projCount, $firstAvailableProj),
		'');
		
		
		
		
		
		//////
		// SPELL CONTROLLER
		///
		
		include("SpellDefinitions.php");
		
		

		
		
		//////
		// SPELL CONSTRUCTOR
		///
		
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


			
			// COORDINATES
			
			// Set Origin and Destination
			_if( $OriginIndex->exactly(self::_Hero) )->then(
				$compX1->setTo($bsX->CP),
				$compY1->setTo($bsY->CP),
			''),
			_if( $OriginIndex->between(self::_Point, self::_Saved) )->then(
				$compX1->setTo($pointX),
				$compY1->setTo($pointY),
			''),
			_if( $OriginIndex->exactly(self::_Proj) )->then(
				$this->loadProjCoordinates($savedProj, $compX1, $compY1),
			''),
			
			_if( $DestinationIndex->exactly(self::_Hero) )->then(
				$compX2->setTo($bsX->CP),
				$compY2->setTo($bsY->CP),
			''),
			_if( $DestinationIndex->between(self::_Point, self::_Saved) )->then(
				$compX2->setTo($pointX),
				$compY2->setTo($pointY),
			''),
			_if( $DestinationIndex->exactly(self::_Proj) )->then(
				$this->loadProjCoordinates($savedProj, $compX2, $compY2),
			''),
			
			
			
			// DISTANCE
			_if( $FindDistance )->then(
				
				// calc distance
			    $distance->distance($compX1, $compY1, $compX2, $compY2),
				
				// if distance exceeds, do specified
				_if( $distance->greaterThan($MaxCastRange) )->then(
					_if( $MaxRangeIndex->exactly(self::_DistResize) )->then(
						$distance->setTo($MaxCastRange),
					''),
					_if( $MaxRangeIndex->exactly(self::_DistCancel) )->then(
						Display("Out of range!"),
						$SetPosition->clear(), $ClearVelocity->clear(), $AddVelocity->clear(), $ClearAcceleration->clear(), $AddAcceleration->clear(), $castStage->setTo(0), $castTimer->setTo(0),
					''),
				''),
				
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
			    $angle->setTo($StoredAngle),
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
				$temp1->max(6400),
				$temp2->max(6400),
			''),
			
			// Save angle
			_if( $SaveAngle )->then(
				$StoredAngle->setTo($angle),
			''),
			
			
			
			// COMPONENTS
			_if( $FindComponents )->then(
			    $angle->componentsInto($xcomponent, $ycomponent),
				$xcomponent->roundedDivideBy(10),
				$ycomponent->roundedDivideBy(10),
				$xcomponent->Max(1000),
				$ycomponent->Max(1000),
			''),
			
			
			
			// MULTIPLICATION 1
			_if( $Mult1Value->atLeast(1) )->then(
				
				// load variable (if applicable)
				_if( $Mult1Value->exactly(self::_MultDist))->then(
					$Mult1Value->setTo($distance),
				''),
				
				// multiply
				$Mult1ResultX->productOf($xcomponent, $Mult1Value),
				$Mult1ResultY->productOf($ycomponent, $Mult1Value),
			''),
			
			
			// DIVISION 1
			_if( $DivValue->atLeast(1) )->then(
				$Mult1ResultX->roundedDivideBy($DivValue),
				$Mult1ResultY->roundedDivideBy($DivValue),
			''),
			
			
			// MULTIPLICATION 2
			_if( $Mult2Value->atLeast(1) )->then(
				
				// load variable (if applicable)
				_if( $Mult2Value->exactly(self::_MultDist))->then(
					$Mult2Value->setTo($distance),
				''),
				
				// multiply
				$Mult2ResultX->productOf($xcomponent, $Mult2Value),
				$Mult2ResultY->productOf($ycomponent, $Mult2Value),
			''),
			
			
			
			// POSITION
			_if( $SetPosition->is_set() )->then(
				
				// initialize
				$positionx->setTo($compX1),
				$positiony->setTo($compY1),
				
				$tempx->Max(144),
				$tempy->Max(144),
				$Mult1ResultX->Max(144000),
				$Mult1ResultY->Max(144000),
				
				
				// get appropriate value
				_if( $PositionIndex->exactly(self::_LoadMult1), $PositionDirection->between(self::_Ahead, self::_Behind) )->then(
					$tempx->roundedQuotientOf($Mult1ResultX, 1000),
					$tempy->roundedQuotientOf($Mult1ResultY, 1000),
				''),
				_if( $PositionIndex->exactly(self::_LoadMult1), $PositionDirection->between(self::_Left, self::_Right) )->then(
					$tempx->roundedQuotientOf($Mult1ResultY, 1000),
					$tempy->roundedQuotientOf($Mult1ResultX, 1000),
				''),
				
				
				// Add/Subtract for signed
				_if( $PositionIndex->atLeast(1) )->then(
					
					// alter angles
					_if( $PositionDirection->exactly(self::_Left) )->then( $angle->add(360*4-90*4) ),
					_if( $PositionDirection->exactly(self::_Right) )->then( $angle->add(90*4) ),
					_if( $PositionDirection->exactly(self::_Behind) )->then( $angle->add(180*4) ),
					_if( $angle->atLeast(1440) )->then( $angle->subtract(1440) ),
					
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
					_if( $PositionDirection->exactly(self::_Left) )->then( $angle->add(90*4) ),
					_if( $PositionDirection->exactly(self::_Right) )->then( $angle->add(360*4-90*4) ),
					_if( $PositionDirection->exactly(self::_Behind) )->then( $angle->add(180*4) ),
					_if( $angle->atLeast(1440) )->then( $angle->subtract(1440) ),
					
				''),

			''),
			
			
			
			// VELOCITY
			_if( $AddVelocity->is_set() )->then(
				
				// initialize
				$velocityx->setTo(6400),
				$velocityy->setTo(6400),
				
				$tempx->Max(6400),
				$tempy->Max(6400),
				$Mult1ResultX->max(64000),
				$Mult1ResultY->max(64000),
				
				
				// get appropriate value
				_if( $VelocityIndex->exactly(self::_LoadMult1), $VelocityDirection->between(self::_Ahead, self::_Behind) )->then(
					$tempx->roundedQuotientOf($Mult1ResultX, 10),
					$tempy->roundedQuotientOf($Mult1ResultY, 10),
				''),
				_if( $VelocityIndex->exactly(self::_LoadMult1), $VelocityDirection->between(self::_Left, self::_Right) )->then(
					$tempx->roundedQuotientOf($Mult1ResultY, 10),
					$tempy->roundedQuotientOf($Mult1ResultX, 10),
				''),
				_if( $VelocityIndex->exactly(self::_LoadMult2), $VelocityDirection->between(self::_Ahead, self::_Behind) )->then(
					$tempx->roundedQuotientOf($Mult2ResultX, 10),
					$tempy->roundedQuotientOf($Mult2ResultY, 10),
				''),
				_if( $VelocityIndex->exactly(self::_LoadMult2), $VelocityDirection->between(self::_Left, self::_Right) )->then(
					$tempx->roundedQuotientOf($Mult2ResultY, 10),
					$tempy->roundedQuotientOf($Mult2ResultX, 10),
				''),
				
				
				// Add/Subtract for signed
				_if( $VelocityIndex->atLeast(1) )->then(
					
					// alter angles
					_if( $VelocityDirection->exactly(self::_Left) )->then( $angle->add(360*4-90*4) ),
					_if( $VelocityDirection->exactly(self::_Right) )->then( $angle->add(90*4) ),
					_if( $VelocityDirection->exactly(self::_Behind) )->then( $angle->add(180*4) ),
					_if( $angle->atLeast(1440) )->then( $angle->subtract(1440) ),
					
					// account for signs
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
					
					// restore angles
					_if( $VelocityDirection->exactly(self::_Left) )->then( $angle->add(90*4) ),
					_if( $VelocityDirection->exactly(self::_Right) )->then( $angle->add(360*4-90*4) ),
					_if( $VelocityDirection->exactly(self::_Behind) )->then( $angle->add(180*4) ),
					_if( $angle->atLeast(1440) )->then( $angle->subtract(1440) ),
					
				''),
				

				// add const value
				_if( $spellCast->exactly(self::_Lob) )->then( $velocityy->subtract(2625) ),
				
			''),
				
				
			
			// ACCELERATION
			_if( $AddAcceleration->is_set() )->then(
				
				// initialize
				$accelerationx->setTo(800),
				$accelerationy->setTo(800),
				
				$tempx->Max(800),
				$tempy->Max(800),
				$Mult1ResultX->max(8000),
				$Mult1ResultY->max(8000),
				
				
				// get appropriate value
				_if( $AccelerationIndex->exactly(self::_LoadMult2), $AccelerationDirection->between(self::_Ahead, self::_Behind) )->then(
					$tempx->roundedQuotientOf($Mult2ResultX, 10),
					$tempy->roundedQuotientOf($Mult2ResultY, 10),
				''),
				_if( $AccelerationIndex->exactly(self::_LoadMult2), $AccelerationDirection->between(self::_Left, self::_Right) )->then(
					$tempx->roundedQuotientOf($Mult2ResultY, 10),
					$tempy->roundedQuotientOf($Mult2ResultX, 10),
				''),
				
				
				// Add/Subtract for signed
				_if( $AccelerationIndex->atLeast(1) )->then(
					
					// alter angles
					_if( $AccelerationDirection->exactly(self::_Left) )->then( $angle->add(360*4-90*4) ),
					_if( $AccelerationDirection->exactly(self::_Right) )->then( $angle->add(90*4) ),
					_if( $AccelerationDirection->exactly(self::_Behind) )->then( $angle->add(180*4) ),
					_if( $angle->atLeast(1440) )->then( $angle->subtract(1440) ),
					
					// account for signs
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
					
					// restore angles
					_if( $AccelerationDirection->exactly(self::_Left) )->then( $angle->add(90*4) ),
					_if( $AccelerationDirection->exactly(self::_Right) )->then( $angle->add(360*4-90*4) ),
					_if( $AccelerationDirection->exactly(self::_Behind) )->then( $angle->add(180*4) ),
					_if( $angle->atLeast(1440))->then( $angle->subtract(1440) ),
					
				''),
				

				// add const value
				_if( $spellCast->exactly(self::_Lob) )->then( $accelerationy->add(350) ),
				_if( $spellCast->exactly(self::_Meteor) )->then( $accelerationx->add(35), $accelerationy->add(197) ),
				
			''),
			
			
			
			// LOAD PROJECTILE
			$this->loadIntoProjectiles($savedProj, $SetPosition, $positionx, $positiony, $ClearVelocity, $AddVelocity, $velocityx, $velocityy, $ClearAcceleration, $AddAcceleration, $accelerationx, $accelerationy, $duration, $eventTime, $spellid),
			
		'');
		
		
		$humans->always(
			// Cleanup
			$frags->release(),
			$invokedspell->release(),
			$SetPosition->release(),
			$ClearVelocity->release(),
			$AddVelocity->release(),
			$ClearAcceleration->release(),
			$AddAcceleration->release(),
			$FindDistance->release(),
			$MaxRangeIndex->release(),
			$MaxCastRange->release(),
			$OriginIndex->release(),
			$DestinationIndex->release(),
			$AngleIndex->release(),
			$AngleAlterationsIndex->release(),
			$AngleAlterationsValue->release(),
			$SaveAngle->release(),
			$FindComponents->release(),
			$Mult1Value->release(),
			$Mult1ResultX->release(),
			$Mult1ResultY->release(),
			$DivValue->release(),
			$Mult2Value->release(),
			$Mult2ResultX->release(),
			$Mult2ResultY->release(),
			$PositionIndex->release(),
			$PositionDirection->release(),
			$VelocityIndex->release(),
			$VelocityDirection->release(),
			$AccelerationIndex->release(),
			$AccelerationDirection->release(),
		'');
		$humans->always(
			$tempx->release(),
			$tempy->release(),
			$compX1->release(),
			$compY1->release(),
			$compX2->release(),
			$compY2->release(),
			$distance->release(),
			$angle->release(),
			$xcomponent->release(),
			$ycomponent->release(),
			$success->release(),
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
			$castTimer->subtract(1),
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
			$P1->_if( $projectile->spellid->exactly(self::_Meteor), $projectile->duration->exactly(1) )->then(
				FX::rumbleAt(50, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
			$P1->_if( $projectile->spellid->exactly(self::_Zap), $projectile->duration->atLeast(1) )->then(
				FX::rumbleAt(2, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
			$P1->_if( $projectile->spellid->exactly(self::_Holocaust), $projectile->duration->atLeast(1), $projectile->eventTime->exactly(0) )->then(
				FX::rumbleAt(2, $projectile->xpos, $projectile->ypos),
			'');
			$P1->_if( $projectile->spellid->exactly(self::_Smite2), $projectile->duration->atLeast(1) )->then(
				FX::rumbleAt(30, $projectile->xpos, $projectile->ypos),
				FX::playWavAt($bam, $projectile->xpos, $projectile->ypos),
			'');
		}
		
		
		
		// Engine
		$projowners->always(
			$this->projectileEngine(),
		'');
		

		
	}
	
	
	
	
	private function loadProjCoordinates(Deathcounter $projectile, TempDC $X, TempDC $Y){
		$text = '';
		
		foreach($this->CPprojectiles as $key=>$proj){
			$text .= _if( $projectile->exactly($key+1) )->then(
					$X->setTo($proj->xpos),
					$Y->setTo($proj->ypos),
				'');
		}
		
		return $text;
	}
	
	
	
	private function findSelected(TempDC $selected, TempDC $selectedID){
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
	
	private function compareProjCoordinates(TempDC $selected, TempDC $selectedID, $origin){
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
	

	
	function loadIntoProjectiles($savedProj, $SetPosition, $positionx, $positiony, $ClearVelocity, $AddVelocity, $velocityx, $velocityy, $ClearAcceleration, $AddAcceleration, $accelerationx, $accelerationy, $duration, $eventTime, $spellid){
		
		/* @var Deathcounter    $savedProj          */
		/* @var TempSwitch      $SetPosition        */
		/* @var TempDC          $positionx          */
		/* @var TempDC          $positiony          */
		/* @var TempSwitch      $ClearVelocity      */
		/* @var TempSwitch      $AddVelocity        */
		/* @var TempDC          $velocityx          */
		/* @var TempDC          $velocityy          */
		/* @var TempSwitch      $ClearAcceleration  */
		/* @var TempSwitch      $AddAcceleration    */
		/* @var TempDC          $accelerationx      */
		/* @var TempDC          $accelerationy      */
		/* @var TempDC          $duration           */
		/* @var TempDC          $eventTime          */
		/* @var TempDC          $spellid            */
				
		$text = '';
				
		foreach( $this->CPprojectiles as $key=>$projectile ){
			$text .= _if( $savedProj->exactly($key+1) )->then(
				_if( $SetPosition->is_set() )->then( $projectile->setPosition($positionx, $positiony) ),
				_if( $ClearVelocity->is_set() )->then( $projectile->setVelocity(0, 0) ),
				_if( $AddVelocity->is_set() )->then( $projectile->addVelocity($velocityx, $velocityy) ),
				_if( $ClearAcceleration->is_set() )->then( $projectile->setAcceleration(0, 0) ),
				_if( $AddAcceleration->is_set() )->then( $projectile->addAcceleration($accelerationx, $accelerationy) ),
				_if( $eventTime->atLeast(1) )->then( $projectile->setEventTime($eventTime) ),
				_if( $duration->atLeast(1) )->then(
					_if( $duration->atMost(999) )->then($projectile->setDuration(0) ),
					_if( $duration->atLeast(1000) )->then( $duration->subtract(1000) ),
					$projectile->addDuration($duration),
				''),
				_if( $spellid->atLeast(1) )->then( $projectile->setSpellID($spellid) ),
			'');
		}
		
		return $text;
	}
	
	function firstAvailableProj(TempDC $count, TempDC $proj){
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