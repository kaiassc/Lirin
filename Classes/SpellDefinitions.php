<?php


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

	
	// Persistent DCs
	/* @var Deathcounter    $pointX                     */
	/* @var Deathcounter    $pointY                     */
	
	/* @var Deathcounter    $savedProj                  */
	/* @var Deathcounter    $spellCast                  */
	/* @var Deathcounter    $castStage                  */
	/* @var Deathcounter    $castTimer                  */
	
	// Saved DCs
	/* @var TempSwitch      $SaveAngle                  */
	/* @var Deathcounter    $StoredAngle                */
	/* @var Deathcounter    $StoredDestinationAngle     */
	
	
	// Projectile Variables
	/* @var TempDC          $positionx                  */
	/* @var TempDC          $positiony                  */
	/* @var TempDC          $velocityx                  */
	/* @var TempDC          $velocityy                  */
	/* @var TempDC          $accelerationx              */
	/* @var TempDC          $accelerationy              */
	/* @var TempDC          $duration                   */
	/* @var TempDC          $eventTime                  */
	/* @var TempDC          $spellid                    */
	/* @var TempDC          $collideProj                */
	/* @var TempDC          $collideUnit                */
	
	
	// SPELL VARIABLES
	
	// for selecting proj
	/* @var TempSwitch      $SetFirstAvailableProj      */
	/* @var TempDC          $firstAvailableProj         */
	/* @var TempSwitch      $SetSelectProj              */
	/* @var TempDC          $selectedProj               */
	/* @var TempDC          $selectedProjID             */
	
	// for distance
	/* @var TempSwitch      $FindDistance               */
	/* @var TempDC          $distance                   */
	
	/* @var TempDC          $MaxRangeIndex              */
	/* @var TempDC          $MaxCastRange               */
	
	// for coordinates and angle
	/* @var TempDC          $OriginIndex                */
	/* @var TempDC          $DestinationIndex           */
	
	/* @var TempDC          $AngleIndex                 */
	
	/* @var TempDC          $AngleAlterationsIndex      */
	/* @var TempDC          $AngleAlterationsValue      */
	
	/* @var TempDC          $angle                      */
	
	// for components
	/* @var TempSwitch      $FindComponents             */
	/* @var TempDC          $xcomponent                 */
	/* @var TempDC          $ycomponent                 */
	
	// multiplication/division
	/* @var TempDC          $Mult1Value                 */
	/* @var TempDC          $Mult1ResultX               */
	/* @var TempDC          $Mult1ResultY               */
	/* @var TempDC          $DivValue                   */
	/* @var TempDC          $Mult2Value                 */
	/* @var TempDC          $Mult2ResultX               */
	/* @var TempDC          $Mult2ResultY               */
	
	/* @var TempDC          $tempx = $temp1             */
	/* @var TempDC          $tempy = $temp2             */
	
	// position
	/* @var TempSwitch      $SetPosition                */
	/* @var TempDC          $PositionIndex              */
	/* @var TempDC          $PositionDirection          */
	
	// velocity
	/* @var TempSwitch      $ClearVelocity              */
	/* @var TempSwitch      $AddVelocity                */
	/* @var TempDC          $VelocityIndex              */
	/* @var TempDC          $VelocityDirection          */
	
	// acceleration
	/* @var TempSwitch      $ClearAcceleration          */
	/* @var TempSwitch      $AddAcceleration            */
	/* @var TempDC          $AccelerationIndex          */
	/* @var TempDC          $AccelerationDirection      */
	
	// miscellaneous
	/* @var TempSwitch      $frags                      */
	/* @var TempSwitch      $enableSpellSystem          */
	/* @var Deathcounter    $invokedslot                */
	/* @var TempDC          $invokedspell               */
	/* @var TempSwitch      $success = $sign            */
	/* @var TempDC          $projCount                  */
	
	


	// player slots
	$humans = new Player(P4, P5, P6);
	$projowners = new Player(P4, P5, P6, P7, P8);


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
	




	//////
	// SPELL CONTROLLER
	///
	

	// 01 -- FIREBALL
		// cast
		$humans->_if( $invokedspell->exactly(_Fireball), $projCount->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Fireball"),
			$spellCast->setTo(_Fireball),
			$castStage->setTo(0),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(16),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(24),
			$eventTime->setTo(0),
			$spellid->setTo(_Fireball),
			$collideProj->setTo(2),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
	
	
	
	// 02 -- LOB
		// cast
		$humans->_if( $invokedspell->exactly(_Lob), $projCount->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Lob"),
			$spellCast->setTo(_Lob),
			$castStage->setTo(0),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->set(),
			$MaxRangeIndex->setTo(_DistResize),
				$MaxCastRange->setTo(256),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(_MultDist),
			$DivValue->setTo(16),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			$AddAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(16),
			$eventTime->setTo(0),
			$spellid->setTo(_Lob),
			
			// Cast
			$enableSpellSystem->set(),
				
		'');
	

	// 03 -- LUNGE



	// 04 -- TELEPORT



	// 05 -- METEOR
		// cast
		$humans->_if( $invokedspell->exactly(_Meteor), $projCount->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Meteor"),
			$spellCast->setTo(_Meteor),
			$castStage->setTo(0),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(0),
			$angle->setTo(400),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(140),
			$DivValue->setTo(0),
			$Mult2Value->setTo(24),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Ahead),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult2),
				$VelocityDirection->setTo(_Behind),
			
			// Acceleration
			$ClearAcceleration->set(),
			$AddAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(5),
			$eventTime->setTo(0),
			$spellid->setTo(_Meteor),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');



	// 06 -- BLOCK
		// continue
		$humans->_if( $spellCast->exactly(_Block), $castTimer->atLeast(1) )->then(
		
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
			$AngleAlterationsIndex->setTo(_shiftTo),
				$AngleAlterationsValue->setTo(60),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(64),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Ahead),
			
			// Velocity
			$ClearVelocity->set(),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(1),
			$eventTime->setTo(0),
			$spellid->setTo(_Block),
			
			// Cast
			$enableSpellSystem->set(),
				
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_Block), $castTimer->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirected Block"),
			
			// Coordinate
			$DestinationIndex->setTo(_Point),
			
			// Angle
			$AngleIndex->setTo(_CalcSaveLoad),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Block), $castTimer->exactly(0), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Block"),
			$spellCast->setTo(_Block),
			$castTimer->setTo(12),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			
			// Angle
			$AngleIndex->setTo(_CalcSave),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(64),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Ahead),
			
			// Velocity
			$ClearVelocity->set(),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(1),
			$eventTime->setTo(0),
			$spellid->setTo(_Block),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// end
		$humans->_if( $spellCast->exactly(_Block), $castTimer->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Ended Block"),
			$spellCast->setTo(0),
		
		'');


	// 07 -- DISRUPTION



	// 08 -- FIREWALL
		// direct
		$humans->_if( $invokedspell->exactly(_Firewall), $spellCast->exactly(_Firewall), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Direct Firewall"),
			$invokedspell->setTo(0),
			$spellCast->setTo(0),
			$castStage->setTo(0),
			
			// Coordinate
			$OriginIndex->setTo(_Proj),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->set(),
			$MaxRangeIndex->setTo(128),
				$MaxCastRange->setTo(_DistResize),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(4),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Durations and ID
			$duration->setTo(1000+32),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Firewall), $projCount->atLeast(1), $castStage->exactly(0) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Firewall"),
			$spellCast->setTo(_Firewall),
			$castStage->setTo(1),
			$castTimer->setTo(24),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Point),
			$DestinationIndex->setTo(_Hero),
			
			// Distance
			$FindDistance->set(),
			$MaxRangeIndex->setTo(_DistCancel),
				$MaxCastRange->setTo(256),
			
			// Angle
			$AngleIndex->setTo(0),
			
			// Mult Div Mult
			$Mult1Value->setTo(0),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(24),
			$eventTime->setTo(24),
			$spellid->setTo(_Firewall),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// cancelled
		$humans->_if( $spellCast->exactly(_Firewall), $castStage->exactly(1), $castTimer->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Firewall Cancelled"),
			$spellCast->setTo(0),
			$castStage->setTo(0),
			
		'');
		



	// 09 -- BARRIER
		


	// 10 -- ZAP
		// stage4
		$humans->_if( $spellCast->exactly(_Zap), $castTimer->atLeast(1) )->then(
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			
			// Angle
			$AngleIndex->setTo(_Load),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(48),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(12),
			$spellid->setTo(_Zap),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Zap), $castTimer->exactly(0), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Zap"),
			$spellCast->setTo(_Zap),
			$castTimer->setTo(4),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(48),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(12),
			$spellid->setTo(_Zap),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');


	// 11 -- BLAZE



	// 12 -- DANCE OF FLAMES
		// initial cast
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $castTimer->atMost(0), not($selectedProjID->exactly(_DanceOfFlames)) )->then(
			Display("Cast Dance of Flames"),
			$spellCast->setTo(_DanceOfFlames),
			$castTimer->setTo(120),
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $spellCast->exactly(_DanceOfFlames), $castTimer->atLeast(1), $castStage->atMost(0), not($selectedProjID->exactly(_DanceOfFlames)) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Dance of Flames"),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(12),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(72),
			$eventTime->setTo(24),
			$spellid->setTo(_DanceOfFlames),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $spellCast->exactly(_DanceOfFlames), $castTimer->atLeast(1), $castStage->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirect Dance of Flames"),
			$spellCast->setTo(_DanceOfFlames),
			$castStage->setTo(0),
			
			// Coordinate
			$OriginIndex->setTo(_Proj),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(12),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Durations and ID
			$duration->setTo(72),
			$eventTime->setTo(24),
			$spellid->setTo(_DanceOfFlames),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// select
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $spellCast->exactly(_DanceOfFlames), $castTimer->atLeast(1), $castStage->atMost(0), $selectedProjID->exactly(_DanceOfFlames) )->then(
			
			Display("Select Dance of Flames"),
			$castStage->setTo(1),
			$SetSelectProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// end
		$humans->_if( $spellCast->exactly(_DanceOfFlames), $castTimer->exactly(1) )->then(
			
			Display("Ended Dance of Flames"),
			$castStage->setTo(0),
			$spellCast->setTo(0),
			
		'');


	// 13 -- HOLOCAUST
		// stage4
		$humans->_if( $spellCast->exactly(_Holocaust), $castStage->exactly(3) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(0),
			
			//Projectile
			$savedProj->setTo(4),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(144),
			$DivValue->setTo(0),
			$Mult2Value->setTo(6),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Right),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult2),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(60),
			$eventTime->setTo(0),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// stage3
		$humans->_if( $spellCast->exactly(_Holocaust), $castStage->exactly(2) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(3),
			
			//Projectile
			$savedProj->setTo(3),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(144),
			$DivValue->setTo(0),
			$Mult2Value->setTo(6),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Left),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult2),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(60+1),
			$eventTime->setTo(1),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// stage2
		$humans->_if( $spellCast->exactly(_Holocaust), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(2),
			
			//Projectile
			$savedProj->setTo(2),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(48),
			$DivValue->setTo(0),
			$Mult2Value->setTo(6),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Right),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult2),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(60+2),
			$eventTime->setTo(2),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// cast, stage1
		$humans->_if( $invokedspell->exactly(_Holocaust), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Holocaust"),
			$spellCast->setTo(_Holocaust),
			$castStage->setTo(1),
			
			//Projectile
			$savedProj->setTo(1),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(48),
			$DivValue->setTo(0),
			$Mult2Value->setTo(6),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult1),
				$PositionDirection->setTo(_Left),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult2),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(60+3),
			$eventTime->setTo(3),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');



	// 14 -- EXPLOSION



	// 15 -- CLAIM



	// 16 -- RAIN OF FIRE



	// 17 -- FIREBREATH
		// continue
		$humans->_if( $spellCast->exactly(_Firebreath), $castTimer->atLeast(1) )->then(
		
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
			$AngleAlterationsIndex->setTo(_shiftTo),
				$AngleAlterationsValue->setTo(30),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(48),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(4),
			$eventTime->setTo(0),
			$spellid->setTo(_Firebreath),
			
			// Cast
			$enableSpellSystem->set(),
				
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_Firebreath), $castTimer->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirected Firebreath"),
			
			// Coordinate
			$DestinationIndex->setTo(_Point),
			
			// Angle
			$AngleIndex->setTo(_CalcSaveLoad),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Firebreath), $castTimer->exactly(0), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Firebreath"),
			$spellCast->setTo(_Firebreath),
			$castTimer->setTo(60),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_CalcSave),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(48),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(4),
			$eventTime->setTo(0),
			$spellid->setTo(_Firebreath),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// end
		$humans->_if( $spellCast->exactly(_Firebreath), $castTimer->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Ended firebreath"),
			$spellCast->setTo(0),
		
		'');


	// 18 -- GUIDED
		// continue
		$humans->_if( $spellCast->exactly(_Guided), $castTimer->atLeast(1) )->then(
		
			// Coordinate
			$OriginIndex->setTo(_Proj),
			$DestinationIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(_CalcSaveLoad),
			$AngleAlterationsIndex->setTo(_shiftTo),
				$AngleAlterationsValue->setTo(90),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(16),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Durations and ID
			$duration->setTo(12),
			$eventTime->setTo(0),
			$spellid->setTo(_Guided),
			
			// Cast
			$enableSpellSystem->set(),
				
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_Guided), $castTimer->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirected Guided Fireball"),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Guided), $castTimer->exactly(0), $projCount->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Guided Fireball"),
			$spellCast->setTo(_Guided),
			$castTimer->setTo(84),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(_CalcSave),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(16),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(120),
			$eventTime->setTo(0),
			$spellid->setTo(_Guided),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// end
		$humans->_if( $spellCast->exactly(_Guided), $castTimer->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Ended Guided Fireball"),
			$spellCast->setTo(0),
			$castTimer->setTo(0),
		
		'');


	// 19 -- SMITE
		// stage4
		$humans->_if( $spellCast->exactly(_Smite), $castStage->exactly(4) )->then(
			
			// Stage prep (for persistent spells)
			$castStage->setTo(0),
			
			//Projectile
			$savedProj->setTo(4),
			
			// Coordinate
			$OriginIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(5),
			$eventTime->setTo(0),
			$spellid->setTo(_Smite2),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// stage3
		$humans->_if( $spellCast->exactly(_Smite), $castStage->exactly(3) )->then(
			
			// Stage prep (for persistent spells)
			$castStage->setTo(4),
			
			//Projectile
			$savedProj->setTo(3),
			
			// Coordinate
			$OriginIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(0),
			$angle->setTo(400),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(64),
			$DivValue->setTo(0),
			$Mult2Value->setTo(32),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult2),
				$PositionDirection->setTo(_Ahead),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(6),
			$eventTime->setTo(1),
			$spellid->setTo(_Smite),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// stage2
		$humans->_if( $spellCast->exactly(_Smite), $castStage->exactly(2) )->then(
			
			// Stage prep (for persistent spells)
			$castStage->setTo(3),
			
			//Projectile
			$savedProj->setTo(2),
			
			// Coordinate
			$OriginIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(0),
			$angle->setTo(400),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(64),
			$DivValue->setTo(0),
			$Mult2Value->setTo(32),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult2),
				$PositionDirection->setTo(_Ahead),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(7),
			$eventTime->setTo(2),
			$spellid->setTo(_Smite),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// stage1
		$humans->_if( $spellCast->exactly(_Smite), $castStage->exactly(1), $castTimer->atMost(0) )->then(
			
			// Stage prep (for persistent spells)
			$castStage->setTo(2),
			
			//Projectile
			$savedProj->setTo(1),
			
			// Coordinate
			$OriginIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(0),
			$angle->setTo(400),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(64),
			$DivValue->setTo(0),
			$Mult2Value->setTo(32),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(_LoadMult2),
				$PositionDirection->setTo(_Ahead),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(8),
			$eventTime->setTo(3),
			$spellid->setTo(_Smite),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Smite), $castStage->exactly(0), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Smite"),
			$spellCast->setTo(_Smite),
			$castStage->setTo(1),
			$castTimer->setTo(20),
			
			$enableSpellSystem->set(),
		'');


	// 01 -- SPIRAL
		// cast
		$humans->_if( $spellCast->exactly(_Spiral), $castStage->exactly(1) )->then(
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Proj),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(16),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Left),
			
			// Durations and ID
			$duration->setTo(2),
			$eventTime->setTo(0),
			$spellid->setTo(_Spiral),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Spiral), $projCount->atLeast(1), $castStage->exactly(0) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Fireball"),
			$spellCast->setTo(_Spiral),
			$castTimer->setTo(60),
			$castStage->setTo(1),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Coordinate
			$OriginIndex->setTo(_Hero),
			$DestinationIndex->setTo(_Point),
			
			// Distance
			$FindDistance->clear(),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
			
			// Component
			$FindComponents->set(),
			
			// Mult Div Mult
			$Mult1Value->setTo(16),
			$DivValue->setTo(0),
			$Mult2Value->setTo(0),
			
			// Position
			$SetPosition->set(),
			
			// Velocity
			$ClearVelocity->set(),
			$AddVelocity->set(),
				$VelocityIndex->setTo(_LoadMult1),
				$VelocityDirection->setTo(_Ahead),
			
			// Acceleration
			$ClearAcceleration->set(),
			
			// Durations and ID
			$duration->setTo(60),
			$eventTime->setTo(0),
			$spellid->setTo(_Spiral),
			
			// Cast
			$enableSpellSystem->set(),
			
		'');
		// end
		$humans->_if( $spellCast->exactly(_Spiral), $castStage->exactly(1), $castTimer->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Ended Spiral"),
			$spellCast->setTo(0),
			$castTimer->setTo(0),
			$castStage->setTo(0),
			
		'');
	
	
