<?php
	
	global $P1, $heroP1, $heroP2, $heroP3, $roam1, $roam2, $roam3, $mercP1, $mercP2, $mercP3, $bossP1, $bossP2, $bossP3, $enemyUnit;
	global $healthA, $healthB, $healthC, $armorA, $armorB, $armorC;
	$all = new Player(P2, P3, P4, P5, P6, P7, P8);
	$switch = new TempSwitch();
	$armorswitch = new TempSwitch();
	$tempdc = new TempDC(100);
	$playerBlock = new TempDC();


	//armor calculation
	$armorCalc = _if( $tempdc->atLeast(91) )->then( $tempdc->setTo(15), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(85) )->then( $tempdc->setTo(16), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(79) )->then( $tempdc->setTo(17), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(74) )->then( $tempdc->setTo(18), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(69) )->then( $tempdc->setTo(19), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(65) )->then( $tempdc->setTo(20), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(61) )->then( $tempdc->setTo(21), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(58) )->then( $tempdc->setTo(22), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(55) )->then( $tempdc->setTo(23), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(52) )->then( $tempdc->setTo(24), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(49) )->then( $tempdc->setTo(25), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(47) )->then( $tempdc->setTo(26), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(44) )->then( $tempdc->setTo(27), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(42) )->then( $tempdc->setTo(28), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(40) )->then( $tempdc->setTo(29), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(38) )->then( $tempdc->setTo(30), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(37) )->then( $tempdc->setTo(31), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(35) )->then( $tempdc->setTo(32), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(34) )->then( $tempdc->setTo(33), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(32) )->then( $tempdc->setTo(34), $armorswitch->clear() ).
				_if( $armorswitch->is_set(), $tempdc->atLeast(31) )->then( $tempdc->setTo(35), $armorswitch->clear() );
				for($i=29; $i>0; $i--){
					$armorCalc .= _if( $armorswitch->is_set(), $tempdc->atLeast($i) )->then( $tempdc->setTo( round(100 - 6*$i/(1+0.06*$i)) ), $armorswitch->clear() );
				}
				$armorCalc .= _if( $armorswitch->is_set() )->then( $tempdc->setTo(100), $armorswitch->clear() );

		


	//HEROES
	$unit = array($heroP1, $heroP2, $heroP3);
	$IDs = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20);
	for($i=0; $i<3; $i++){
		//detect attack
		$P1->_if( $unit[$i]->attackCooldown(AtLeast, 1) )->then(
			$unit[$i]->attackTime->add(1),
			_if( $unit[$i]->attackTime->exactly(1) )->then( $unit[$i]->getSpecificTargetIDs($unit[$i]->attackTarget, $IDs), $switch->set() ),
			_if( $unit[$i]->attackTime->atLeast(2) )->then( $unit[$i]->checkSpecificTargetIDs($unit[$i]->attackTarget, $switch, $IDs) ),
			_if( $switch->is_clear() )->then( $unit[$i]->attackTarget->add(100), $unit[$i]->attackTarget->setTo(0) ),
			_if( $switch->is_set() )->then( $switch->clear() ),
			_if( $unit[$i]->type->Exactly(0), $unit[$i]->attackTime->exactly(2) )->then( $switch->set() ),
			_if( $unit[$i]->type->Exactly(1), $unit[$i]->attackTime->exactly(2) )->then( $switch->set() ),
			_if( $unit[$i]->type->Exactly(2), $unit[$i]->attackTime->exactly(3) )->then( $switch->set() ),
			_if( $unit[$i]->attackCooldown(AtMost, 2) )->then( $unit[$i]->attackTime->setTo(0) ),
		'');
		
		//deal damage
		$P1->_if( $switch->is_set() )->then(
			$armorswitch->set(),
			SetEnemy($all),
			//set player
			_if( $unit[$i]->attackTarget->atLeast(14) )->then( $unit[$i]->attackTarget->subtract(14), $playerBlock->add(2) ),
			_if( $unit[$i]->attackTarget->atLeast(7) )->then( $unit[$i]->attackTarget->subtract(7), $playerBlock->add(1) ),
			_if( $unit[$i]->attackTarget->exactly(0) )->then( SetAlly(P2) ),
			_if( $unit[$i]->attackTarget->exactly(1) )->then( SetAlly(P3) ),
			_if( $unit[$i]->attackTarget->exactly(2) )->then( SetAlly(P4) ),
			_if( $unit[$i]->attackTarget->exactly(3) )->then( SetAlly(P5) ),
			_if( $unit[$i]->attackTarget->exactly(4) )->then( SetAlly(P6) ),
			_if( $unit[$i]->attackTarget->exactly(5) )->then( SetAlly(P7) ),
			_if( $unit[$i]->attackTarget->exactly(6) )->then( SetAlly(P8) ),
			
			//get armor
			_if( $playerBlock->exactly(0) )->then( $tempdc->setTo($armorA->Allies) ),
			_if( $playerBlock->exactly(1) )->then( $tempdc->setTo($armorB->Allies) ),
			_if( $playerBlock->exactly(2) )->then( $tempdc->setTo($armorC->Allies) ),
			
			//armor calculation
			$armorCalc,
			$tempdc->multiplyBy($unit[$i]->damage),
			$tempdc->max(12700),
			_if( $tempdc->atMost(50) )->then( $tempdc->setTo(50) ),
		
			//deal damage
			_if( $playerBlock->exactly(0) )->then( $healthA->Allies->subDivBecome($tempdc) ),
			_if( $playerBlock->exactly(1) )->then( $healthB->Allies->subDivBecome($tempdc) ),
			_if( $playerBlock->exactly(2) )->then( $healthC->Allies->subDivBecome($tempdc) ),
			$tempdc->max(100),
			
			//restore
			SetAlly($all),
			$tempdc->setTo(0),
			$unit[$i]->attackTarget->setTo(0),
			$playerBlock->setTo(0),
			$switch->clear(),
		'');
	}
		



	//NPCS
	$unit = array($mercP1, $mercP2, $mercP3, $roam1, $roam2, $roam3);
	$IDs = array(/*TEMP*/0, 1, 2, 3, 4, 5, /*TEMP*/6, 7, 8, 12, 13, 14, 15, 16, 17, 18, 19, 20);
	for($i=0; $i<6; $i++){
		//detect attack
		$P1->_if( $unit[$i]->attackCooldown(AtLeast, 1) )->then(
			$unit[$i]->attackTime->add(1),
			_if( $unit[$i]->attackTime->exactly(1) )->then( $unit[$i]->getSpecificTargetIDs($unit[$i]->attackTarget, $IDs), $switch->set() ),
			_if( $unit[$i]->attackTime->atLeast(2) )->then( $unit[$i]->checkSpecificTargetIDs($unit[$i]->attackTarget, $switch, $IDs) ),
			_if( $switch->is_clear() )->then( $unit[$i]->attackTarget->add(100), $unit[$i]->attackTarget->setTo(0) ),
			_if( $switch->is_set() )->then( $switch->clear() ),
			_if( $unit[$i]->type->Exactly(0), $unit[$i]->attackTime->exactly(2) )->then( $switch->set() ),
			_if( $unit[$i]->type->Exactly(1), $unit[$i]->attackTime->exactly(2) )->then( $switch->set() ),
			_if( $unit[$i]->type->Exactly(2), $unit[$i]->attackTime->exactly(3) )->then( $switch->set() ),
			_if( $unit[$i]->attackCooldown(AtMost, 2) )->then( $unit[$i]->attackTime->setTo(0) ),
		'');
		
		//deal damage
		$P1->_if( $switch->is_set() )->then(
			$armorswitch->set(),
			SetEnemy($all),
			//set player
			_if( $unit[$i]->attackTarget->atLeast(14) )->then( $unit[$i]->attackTarget->subtract(14), $playerBlock->add(2) ),
			_if( $unit[$i]->attackTarget->atLeast(7) )->then( $unit[$i]->attackTarget->subtract(7), $playerBlock->add(1) ),
			_if( $unit[$i]->attackTarget->exactly(0) )->then( SetAlly(P2) ),
			_if( $unit[$i]->attackTarget->exactly(1) )->then( SetAlly(P3) ),
			_if( $unit[$i]->attackTarget->exactly(2) )->then( SetAlly(P4) ),
			_if( $unit[$i]->attackTarget->exactly(3) )->then( SetAlly(P5) ),
			_if( $unit[$i]->attackTarget->exactly(4) )->then( SetAlly(P6) ),
			_if( $unit[$i]->attackTarget->exactly(5) )->then( SetAlly(P7) ),
			_if( $unit[$i]->attackTarget->exactly(6) )->then( SetAlly(P8) ),
			
			//get armor
			_if( $playerBlock->exactly(0) )->then( $tempdc->setTo($armorA->Allies) ),
			_if( $playerBlock->exactly(1) )->then( $tempdc->setTo($armorB->Allies) ),
			_if( $playerBlock->exactly(2) )->then( $tempdc->setTo($armorC->Allies) ),
			
			//armor calculation
			$armorCalc,
			$tempdc->multiplyBy($unit[$i]->damage),
			$tempdc->max(12700),
			_if( $tempdc->atMost(50) )->then( $tempdc->setTo(50) ),
			
			//deal damage
			_if( $playerBlock->exactly(0) )->then( $healthA->Allies->subDivBecome($tempdc) ),
			_if( $playerBlock->exactly(1) )->then( $healthB->Allies->subDivBecome($tempdc) ),
			_if( $playerBlock->exactly(2) )->then( $healthC->Allies->subDivBecome($tempdc) ),
			$tempdc->max(100),
			
			//restore
			SetAlly($all),
			$tempdc->setTo(0),
			$unit[$i]->attackTarget->setTo(0),
			$playerBlock->setTo(0),
			$switch->clear(),
		'');
	}
		

	

	//ENEMIES
	$unit = array($bossP1, $bossP2, $bossP3, $enemyUnit[0], $enemyUnit[1], $enemyUnit[2], $enemyUnit[3],
					$enemyUnit[4], $enemyUnit[5], $enemyUnit[6], $enemyUnit[7], $enemyUnit[8]);
	$IDs = array(0, 1, 2, 3, 4, 5, 9, 10, 11);
	for($i=0; $i<12; $i++){
		//detect attack
		$P1->_if( $unit[$i]->attackCooldown(AtLeast, 1) )->then(
			$unit[$i]->attackTime->add(1),
			_if( $unit[$i]->attackTime->exactly(1) )->then( $unit[$i]->getSpecificTargetIDs($unit[$i]->attackTarget, $IDs), $switch->set() ),
			_if( $unit[$i]->attackTime->atLeast(2) )->then( $unit[$i]->checkSpecificTargetIDs($unit[$i]->attackTarget, $switch, $IDs) ),
			_if( $switch->is_clear() )->then( $unit[$i]->attackTarget->add(100), $unit[$i]->attackTarget->setTo(0) ),
			_if( $switch->is_set() )->then( $switch->clear() ),
			_if( $unit[$i]->type->Exactly(0), $unit[$i]->attackTime->exactly(2) )->then( $switch->set() ),
	
			_if( $unit[$i]->attackCooldown(AtMost, 2) )->then( $unit[$i]->attackTime->setTo(0) ),
		'');
		
		//deal damage
		$P1->_if( $switch->is_set() )->then(
			$armorswitch->set(),
			SetEnemy($all),
			//set player
			_if( $unit[$i]->attackTarget->atLeast(14) )->then( $unit[$i]->attackTarget->subtract(14), $playerBlock->add(2) ),
			_if( $unit[$i]->attackTarget->atLeast(7) )->then( $unit[$i]->attackTarget->subtract(7), $playerBlock->add(1) ),
			_if( $unit[$i]->attackTarget->exactly(0) )->then( SetAlly(P2) ),
			_if( $unit[$i]->attackTarget->exactly(1) )->then( SetAlly(P3) ),
			_if( $unit[$i]->attackTarget->exactly(2) )->then( SetAlly(P4) ),
			_if( $unit[$i]->attackTarget->exactly(3) )->then( SetAlly(P5) ),
			_if( $unit[$i]->attackTarget->exactly(4) )->then( SetAlly(P6) ),
			_if( $unit[$i]->attackTarget->exactly(5) )->then( SetAlly(P7) ),
			_if( $unit[$i]->attackTarget->exactly(6) )->then( SetAlly(P8) ),
			
			//get armor
			_if( $playerBlock->exactly(0) )->then( $tempdc->setTo($armorA->Allies) ),
			_if( $playerBlock->exactly(1) )->then( $tempdc->setTo($armorB->Allies) ),
			
			//armor calculation
			$armorCalc,
			$tempdc->multiplyBy($unit[$i]->damage),
			$tempdc->max(12700),
			_if( $tempdc->atMost(50) )->then( $tempdc->setTo(50) ),
		
			//deal damage
			_if( $playerBlock->exactly(0) )->then( $healthA->Allies->subDivBecome($tempdc) ),
			_if( $playerBlock->exactly(1) )->then( $healthB->Allies->subDivBecome($tempdc) ),
			$tempdc->max(100),
			
			//restore
			SetAlly($all),
			$tempdc->setTo(0),
			$unit[$i]->attackTarget->setTo(0),
			$playerBlock->setTo(0),
			$switch->clear(),
		'');
	}


	$tempdc->kill();
	$playerBlock->kill();
	$switch->kill();
	$armorswitch->kill();
	
	
?>
