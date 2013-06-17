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

	
	// Persistent DCs
	/* @var Deathcounter    $savedProj                  */
	/* @var Deathcounter    $spellCast                  */
	/* @var Deathcounter    $castStage                  */
	
	// Saved DCs
	/* @var TempSwitch      $SaveAngle                  */
	/* @var Deathcounter    $StoredCurrentAngle         */
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
	

	// SPELL VARIABLES

	// for projectile
	/* @var TempSwitch      $SetFirstAvailableProj =    */
	/* @var TempSwitch      $SetSelectProj =            */
	/* @var TempDC          $selectedProj =             */
	/* @var TempDC          $selectedProjID =           */
	/* @var TempDC          $firstAvailableProj =       */
	
	// for distance
	/* @var TempDC          $DistanceOriginIndex        */
	/* @var TempDC          $DistanceDestinationIndex   */
	
	/* @var TempSwitch      $FindDistance               */
	/* @var TempDC          $distance                   */
	
	/* @var TempDC          $MaxRangeIndex              */
	/* @var TempDC          $MaxCastRange               */
	
	// for coordinates and angle
	/* @var TempSwitch      $SaveCoordinates            */

	/* @var TempDC          $ComponentOriginIndex       */
	/* @var TempDC          $ComponentDestinationIndex  */
	
	/* @var TempDC          $AngleIndex                 */
	
	/* @var TempDC          $AngleAlterationsIndex      */
	/* @var TempDC          $AngleAlterationsValue      */
	
	/* @var TempDC          $angle                      */
	
	// for components
	/* @var TempSwitch      $FindComponents             */
	/* @var TempDC          $xcomponent                 */
	/* @var TempDC          $ycomponent                 */
	
	// for position
	/* @var TempSwitch      $SetPosition                */
	/* @var TempDC          $PositionIndex              */
	/* @var TempDC          $PositionLoadIndex          */
	/* @var TempDC          $PositionMultiplier         */
	/* @var TempDC          $StaticOffsetX              */
	/* @var TempDC          $StaticOffsetY              */
	
	/* @var TempDC          $temp1                      */
	/* @var TempDC          $temp2                      */
	
	// for velocity
	/* @var TempSwitch      $SetVelocity                */
	/* @var TempDC          $VelocityLoadIndex          */
	/* @var TempDC          $VelocityMultiplyByDCIndex  */
	/* @var TempDC          $VelocityMultiplier         */
	/* @var TempDC          $VelocityDivisor            */
	/* @var TempDC          $VelocityRawY               */
	
	// for acceleration
	/* @var TempSwitch      $SetAcceleration            */
	/* @var TempDC          $AccelerationLoadIndex      */
	/* @var TempDC          $AccelerationMultiplier     */
	/* @var TempDC          $AccelerationRawY           */
	
	// miscellaneous
	/* @var TempSwitch      $frags                      */
	/* @var TempSwitch      $enableSpellSystem          */
	/* @var Deathcounter    $invokedslot                */
	/* @var TempDC          $invokedspell               */
	/* @var TempSwitch      $success                    */
	/* @var TempSwitch      $sign                       */
	/* @var TempDC          $projCount                  */
	/* @var TempSwitch      $loadIntoProj               */


	// player slots
	$humans = new Player(P4, P5, P6);
	$projowners = new Player(P4, P5, P6, P7, P8);


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
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(16),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(24),
			$eventTime->setTo(0),
			$spellid->setTo(_Fireball),
			
			// Cast
			$loadIntoProj->set(),
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
			
			// Distance
			$FindDistance->set(),
				$DistanceOriginIndex->setTo(_Hero),
				$DistanceDestinationIndex->setTo(_Point1),
			$MaxRangeIndex->setTo(_DistResize),
				$MaxCastRange->setTo(256),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(1),   // vel *= dist
					$VelocityMultiplier->setTo(0),
					$VelocityDivisor->setTo(16),
				$VelocityRawY->setTo(6400-2625),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(800+350),
			
			// Durations and ID
			$duration->setTo(16),
			$eventTime->setTo(0),
			$spellid->setTo(_Lob),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
				
		'');
	

	// 03 -- LUNGE



	// 04 -- TELEPORT



	// 05 -- METEOR



	// 06 -- BLOCK



	// 07 -- DISRUPTION



	// 08 -- FIREWALL



	// 09 -- BARRIER



	// 10 -- ZAP
		// stage4
		$humans->_if( $spellCast->exactly(_Zap), $castStage->exactly(3) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(0),
			
			//Projectile
			$savedProj->setTo(4),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(48),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(12),
			$spellid->setTo(_Zap),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// stage3
		$humans->_if( $spellCast->exactly(_Zap), $castStage->exactly(2) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(3),
			
			//Projectile
			$savedProj->setTo(3),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(48),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(12),
			$spellid->setTo(_Zap),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// stage2
		$humans->_if( $spellCast->exactly(_Zap), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(2),
			
			//Projectile
			$savedProj->setTo(2),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(48),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(12),
			$spellid->setTo(_Zap),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// cast, stage1
		$humans->_if( $invokedspell->exactly(_Zap), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Zap"),
			$spellCast->setTo(_Zap),
			$castStage->setTo(1),
			
			//Projectile
			$savedProj->setTo(1),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(48),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(12),
			$spellid->setTo(_Zap),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');


	// 11 -- BLAZE



	// 12 -- DANCE OF FLAMES
		// cast
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $castStage->atMost(0), not($selectedProjID->exactly(_DanceOfFlames)) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Dance of Flames"),
			$spellCast->setTo(_DanceOfFlames),
			$castStage->setTo(0),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(12),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(72),
			$eventTime->setTo(24),
			$spellid->setTo(_DanceOfFlames),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $castStage->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirect Dance of Flames"),
			$spellCast->setTo(_DanceOfFlames),
			$castStage->setTo(0),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Proj),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(12),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(72),
			$eventTime->setTo(24),
			$spellid->setTo(_DanceOfFlames),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// select
		$humans->_if( $invokedspell->exactly(_DanceOfFlames), $castStage->atMost(0), $selectedProjID->exactly(_DanceOfFlames) )->then(
			
			Display("Select Dance of Flames"),
			$castStage->setTo(1),
			$SetSelectProj->set(),
			$enableSpellSystem->set(),
			
		'');


	// 13 -- HOLOCAUST
		// stage4
		$humans->_if( $spellCast->exactly(_Holocaust), $castStage->exactly(3) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(0),
			
			//Projectile
			$savedProj->setTo(4),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
				$ComponentOriginIndex->setTo(_Hero),
				#$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(_Right),
					$PositionMultiplier->setTo(144),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(6),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(60),
			$eventTime->setTo(0),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// stage3
		$humans->_if( $spellCast->exactly(_Holocaust), $castStage->exactly(2) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(3),
			
			//Projectile
			$savedProj->setTo(3),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
				$ComponentOriginIndex->setTo(_Hero),
				#$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(_Left),
					$PositionMultiplier->setTo(144),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(6),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(60+1),
			$eventTime->setTo(1),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');
		// stage2
		$humans->_if( $spellCast->exactly(_Holocaust), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			#$spellCast->setTo(12),
			$castStage->setTo(2),
			
			//Projectile
			$savedProj->setTo(2),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
				$ComponentOriginIndex->setTo(_Hero),
				#$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->clear(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(_Right),
					$PositionMultiplier->setTo(48),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(6),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(60+2),
			$eventTime->setTo(2),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$loadIntoProj->set(),
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
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Calc),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(_Left),
					$PositionMultiplier->setTo(48),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(_Ahead),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(6),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(60+3),
			$eventTime->setTo(3),
			$spellid->setTo(_Holocaust),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');



	// 14 -- EXPLOSION



	// 15 -- CLAIM



	// 16 -- RAIN OF FIRE



	// 17 -- FIREBREATH
		// end
		#$humans->_if( $invokedspell->exactly(3), $spellStage->exactly(1) )->then(
		#	
		#	// Stage prep (for persistent spells)
		#	Display("Ended firebreath"),
		#	$spellCast->setTo(0),
		#	$spellStage->setTo(0),
		#
		#'');
		// continue
		$humans->_if( $spellCast->exactly(_Firebreath), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			#Display("Invoke fireball"),
			#$spellCast->setTo(16),
			#$castStage->setTo(0),
		
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_Load),
				$ComponentOriginIndex->setTo(_Hero),
				#$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(_shiftTo),
				$AngleAlterationsValue->setTo(30),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(48),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(4),
			$eventTime->setTo(0),
			$spellid->setTo(_Firebreath),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
				
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_Firebreath), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirected Firebreath"),
			
			// Angle
			$AngleIndex->setTo(_CalcSaveLoad),
				$ComponentDestinationIndex  ->setTo(_Point1),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Firebreath), $castStage->exactly(0), $projCount->atLeast(4) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Firebreath"),
			$spellCast->setTo(_Firebreath),
			$castStage->setTo(1),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			// Distance
			$FindDistance->clear(),
				$DistanceOriginIndex->setTo(0),
				$DistanceDestinationIndex->setTo(0),
			$MaxRangeIndex->setTo(0),
				$MaxCastRange->setTo(0),
			
			// Angle
			$AngleIndex->setTo(_CalcSave),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Point1),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(48),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(4),
			$eventTime->setTo(0),
			$spellid->setTo(_Firebreath),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');


		// 18 -- GUIDED
		// end
		#$humans->_if( $invokedspell->exactly(3), $spellStage->exactly(1) )->then(
		#	
		#	// Stage prep (for persistent spells)
		#	Display("Ended firebreath"),
		#	$spellCast->setTo(0),
		#	$spellStage->setTo(0),
		#
		#'');
		// continue
		$humans->_if( $spellCast->exactly(_Guided), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			#Display("Invoke fireball"),
			#$spellCast->setTo(16),
			#$castStage->setTo(0),
		
			// Coordinates
			$ComponentOriginIndex->setTo(_Proj),
			$ComponentDestinationIndex->setTo(_Saved),
			
			// Angle
			$AngleIndex->setTo(_CalcSaveLoad),
			$AngleAlterationsIndex->setTo(_shiftTo),
				$AngleAlterationsValue->setTo(90),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(16),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(120),
			$eventTime->setTo(0),
			$spellid->setTo(_Guided),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
				
		'');
		// redirect
		$humans->_if( $invokedspell->exactly(_Guided), $castStage->exactly(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Redirected Guided Fireball"),
			$SaveCoordinates->set(),
			
		'');
		// cast
		$humans->_if( $invokedspell->exactly(_Guided), $castStage->exactly(0), $projCount->atLeast(1) )->then(
			
			// Stage prep (for persistent spells)
			Display("Invoke Guided Fireball"),
			$spellCast->setTo(_Guided),
			$castStage->setTo(1),
			
			//Projectile
			$SetFirstAvailableProj->set(),
			
			$SaveCoordinates->set(),
			
			// Angle
			$AngleIndex->setTo(_CalcSave),
				$ComponentOriginIndex->setTo(_Hero),
				$ComponentDestinationIndex->setTo(_Saved),
			$AngleAlterationsIndex->setTo(0),
				$AngleAlterationsValue->setTo(0),
			$SaveAngle->set(),
			
			// Component
			$FindComponents->set(),
			
			// Position
			$SetPosition->set(),
				$PositionIndex->setTo(1),
					$PositionLoadIndex->setTo(0),
					$PositionMultiplier->setTo(0),
				$StaticOffsetX->setTo(0),
				$StaticOffsetY->setTo(0),
			
			// Velocity
			$SetVelocity->set(),
				$VelocityLoadIndex->setTo(1),
					$VelocityMultiplyByDCIndex->setTo(0),
					$VelocityMultiplier->setTo(16),
					$VelocityDivisor->setTo(0),
				$VelocityRawY->setTo(0),
			
			// Acceleration
			$SetAcceleration->set(),
				$AccelerationLoadIndex->setTo(0),
					$AccelerationMultiplier->setTo(0),
				$AccelerationRawY->setTo(0),
			
			// Durations and ID
			$duration->setTo(120),
			$eventTime->setTo(0),
			$spellid->setTo(_Guided),
			
			// Cast
			$loadIntoProj->set(),
			$enableSpellSystem->set(),
			
		'');


		
	
	
