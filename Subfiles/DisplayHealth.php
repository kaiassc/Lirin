<?php
	

	global $P4, $P5, $P6, $P7, $P8, $heroP1, $heroP2, $heroP3, $roam1, $roam2, $roam3, $mercP1, $mercP2, $mercP3, $bossP1, $bossP2, $bossP3, $enemyUnit;
	$tempdc = new TempDC(100);
	$switch = new TempSwitch();
	$P = array($P4, $P5, $P6);
	$all = new Player(P2, P3, P4, P5, P6, P7, P8);

	//TEMPORARY SWITCH
	$killswitch = new TempSwitch();
	

	/////
	// DYNAMIC HEALTH SETUP
	///
	function mult20($unit, $P, $tempdc){
		global $heroP1, $heroP2, $heroP3;
		$maxpower = getBinaryPower($unit->maxhealth->Max);
	
		$text = '';
		$clearnums = '';
		$nums = array();

		for($i=0; $i<20; $i++){
			$nums[$i] = new TempDC();
			$text .= $nums[$i]->setTo(1);
			$clearnums .= $nums[$i]->setTo(0);
		}
		for($j=$maxpower; $j>=0; $j--){
			$k=pow(2,$j);
			$text .= _if( $unit->maxhealth->atLeast($k) )->then(
				$unit->maxhealth->subtract($k),
				$nums[0]->add($k),
				$nums[1]->add($k*2),
				$nums[2]->add($k*3),
				$nums[3]->add($k*4),
				$nums[4]->add($k*5),
				$nums[5]->add($k*6),
				$nums[6]->add($k*7),
				$nums[7]->add($k*8),
				$nums[8]->add($k*9),
				$nums[9]->add($k*10),
				$nums[10]->add($k*11),
				$nums[11]->add($k*12),
				$nums[12]->add($k*13),
				$nums[13]->add($k*14),
				$nums[14]->add($k*15),
				$nums[15]->add($k*16),
				$nums[16]->add($k*17),
				$nums[17]->add($k*18),
				$nums[18]->add($k*19),
				$nums[19]->add($k*20),
				$tempdc->add($k),
			'');
		}
		for($j=$maxpower; $j>=0; $j--){
			$k=pow(2,$j);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$unit->maxhealth->add($k),
				$tempdc->subtract($k),
			'');
		}
		for($j=$maxpower; $j>=0; $j--){
			$k=pow(2,$j);
			$text .= _if( $unit->health->atLeast($k) )->then(
				$unit->health->subtract($k),
				$nums[0]->subtract($k*20),
				$nums[1]->subtract($k*20),
				$nums[2]->subtract($k*20),
				$nums[3]->subtract($k*20),
				$nums[4]->subtract($k*20),
				$nums[5]->subtract($k*20),
				$nums[6]->subtract($k*20),
				$nums[7]->subtract($k*20),
				$nums[8]->subtract($k*20),
				$nums[9]->subtract($k*20),
				$nums[10]->subtract($k*20),
				$nums[11]->subtract($k*20),
				$nums[12]->subtract($k*20),
				$nums[13]->subtract($k*20),
				$nums[14]->subtract($k*20),
				$nums[15]->subtract($k*20),
				$nums[16]->subtract($k*20),
				$nums[17]->subtract($k*20),
				$nums[18]->subtract($k*20),
				$nums[19]->subtract($k*20),
				$tempdc->add($k),
			'');
		}
		for($j=$maxpower; $j>=0; $j--){
			$k=pow(2,$j);
			$text .= _if( $tempdc->atLeast($k) )->then(
				$unit->health->add($k),
				$tempdc->subtract($k),
			'');
		}
		
		if($unit == $heroP1 || $unit == $heroP2 || $unit == $heroP3){
			$text .= _if( $nums[19]->atMost(0) )->then( $unit->health->setTo($unit->maxhealth), ModifyHealth($P, "Protoss Zealot", 1, "Anywhere", 100),
				ModifyHealth($P, "Alexei Stukov (Ghost)", 1, "Anywhere", 100), ModifyHealth($P, "Tassadar (Templar)", 1, "Anywhere", 100), $clearnums );
			for($i=0; $i<19; $i++){
				$text .= _if( $nums[$i]->atLeast(1) )->then( ModifyHealth($P, "Protoss Zealot", 1, "Anywhere", $i*5+5), ModifyHealth($P, "Alexei Stukov (Ghost)", 1, "Anywhere", $i*5+5),
					ModifyHealth($P, "Tassadar (Templar)", 1, "Anywhere", $i*5+5), $clearnums );
			}
			$text .= _if( $nums[19]->atLeast(2) )->then( ModifyHealth($P, "Protoss Zealot", 1, "Anywhere", 99), ModifyHealth($P, "Alexei Stukov (Ghost)", 1, "Anywhere", 99),
				ModifyHealth($P, "Tassadar (Templar)", 1, "Anywhere", 99), $clearnums );
			$text .= _if( $nums[19]->exactly(1) )->then( ModifyHealth($P, "Protoss Zealot", 1, "Anywhere", 100), ModifyHealth($P, "Alexei Stukov (Ghost)", 1, "Anywhere", 100),
				ModifyHealth($P, "Tassadar (Templar)", 1, "Anywhere", 100), $clearnums );
		}
		else{
			$text .= _if( $nums[19]->atMost(0) )->then( $unit->health->setTo($unit->maxhealth), $unit->type->add(2100), $clearnums );
			for($i=0; $i<19; $i++){ $text .= _if( $nums[$i]->atLeast(1) )->then( $unit->type->add($i*100+100), $clearnums ); }
			$text .= _if( $nums[19]->atLeast(2) )->then( $unit->type->add(2000), $clearnums );
			$text .= _if( $nums[19]->exactly(1) )->then( $unit->type->add(2100), $clearnums );
		}
		
		
		foreach($nums as $num){
			$num->kill();
		}
			
		return $text;
	}


	function LoadInto(Deathcounter $dc1, Deathcounter $dc2){
		$text = $dc1->setTo(0);
		for($i=5; $i>=0; $i--){
			$k=pow(2,$i);
			$text .= _if( $dc2->atLeast($k*100) )->then(
			    $dc2->subtract($k*100),
				$dc1->add($k),
			'');
		}
		return $text;
	}

	





	/////
	// HERO HEALTH
	///
	$unit = array($heroP1, $heroP2, $heroP3);
	for($i=0; $i<3; $i++){ $P8->always( mult20($unit[$i], $P[$i], $tempdc) ); }



	/////
	// NPC HEALTH
	///
	$merc = array($mercP1, $mercP2, $mercP3);
	$roam = array($roam1, $roam2, $roam3);

	//FIND UNIT
	$P8->always(
		ModifyHealth(P7, Men, 6, "Anywhere", 99),
		ModifyHealth(P7, Men, 5, "Anywhere", 98),
		ModifyHealth(P7, Men, 4, "Anywhere", 97),
		ModifyHealth(P7, Men, 3, "Anywhere", 96),
		ModifyHealth(P7, Men, 2, "Anywhere", 95),
		ModifyHealth(P7, Men, 1, "Anywhere", 94),
	'');
	
	for($i=0; $i<3; $i++){
		for($j=0; $j<6; $j++){ $P8->_if($merc[$i]->health(Exactly, 9900-100*$j))->then($merc[$i]->type->add(60000-10000*$j)); }
	}
	for($i=0; $i<3; $i++){
		for($j=0; $j<6; $j++){ $P8->_if($roam[$i]->health(Exactly, 9900-100*$j))->then($roam[$i]->type->add(60000-10000*$j)); }
	}

	//FIND HEALTH
	for($i=0; $i<3; $i++){ $P8->_if( $merc[$i]->type->atLeast(10000) )->then( mult20($merc[$i], $P7, $tempdc) ); }
	for($i=0; $i<3; $i++){ $P8->_if( $roam[$i]->type->atLeast(10000) )->then( mult20($roam[$i], $P7, $tempdc) ); }

	//SET HEALTH
	for($i=6; $i>0; $i--){
		//GET HEALTH
		for($j=0; $j<3; $j++){
			$P8->_if( $merc[$j]->type->atLeast(10000*$i) )->then( $merc[$j]->type->subtract(10000*$i), $switch->set(), LoadInto($tempdc, $merc[$j]->type),
				/*[TEMPORARY]*/_if( $merc[$j]->health->atMost(0) )->then( $killswitch->set() ),/*[/TEMPORARY]*/'');
		}
		for($j=0; $j<3; $j++){
			$P8->_if( $roam[$j]->type->atLeast(10000*$i) )->then( $roam[$j]->type->subtract(10000*$i), $switch->set(), LoadInto($tempdc, $roam[$j]->type),
				/*[TEMPORARY]*/_if( $roam[$j]->health->atMost(0) )->then( $killswitch->set() ),/*[/TEMPORARY]*/ '');
		}
		//SET HEALTH
		for($j=1; $j<=19; $j++){ $P8->_if( $switch->is_set(), $tempdc->exactly($j) )->then( ModifyHealth(P7, Men, $i, "Anywhere", 5*$j), $switch->clear(), $tempdc->setTo(0) ); }
		$P8->_if( $switch->is_set(), $tempdc->exactly(20) )->then( ModifyHealth(P7, Men, $i, "Anywhere", 99), $switch->clear(), $tempdc->setTo(0) );
		$P8->_if( $switch->is_set() )->then( ModifyHealth(P7, Men, $i, "Anywhere", 100), $switch->clear(), $tempdc->setTo(0) );
		//[TEMPORARY]
		if($i==1){ $P8->_if( $killswitch->is_set() )->then( KillUnitAtLocation(P7, Men, 1, "sandbox"), $killswitch->clear() ); }
		else{
			$P8->_if( $killswitch->is_set() )->then(
				Give(P7, Men, $i-1, P9, "sandbox"),
				KillUnitAtLocation(P7, Men, 1, "sandbox"),
				Give(P9, Men, All, P7, "sandbox"),
				$killswitch->clear(),
			'');
		}
		//[/TEMPORARY]
	}



	/////
	// ENEMY HEALTH
	///
	$boss = array($bossP1, $bossP2, $bossP3);

	//FIND UNIT
	$P8->always(
		ModifyHealth(P8, Men, 12, "Anywhere", 99),
		ModifyHealth(P8, Men, 11, "Anywhere", 98),
		ModifyHealth(P8, Men, 10, "Anywhere", 97),
		ModifyHealth(P8, Men, 9, "Anywhere", 96),
		ModifyHealth(P8, Men, 8, "Anywhere", 95),
		ModifyHealth(P8, Men, 7, "Anywhere", 94),
		ModifyHealth(P8, Men, 6, "Anywhere", 93),
		ModifyHealth(P8, Men, 5, "Anywhere", 92),
		ModifyHealth(P8, Men, 4, "Anywhere", 91),
		ModifyHealth(P8, Men, 3, "Anywhere", 90),
		ModifyHealth(P8, Men, 2, "Anywhere", 89),
		ModifyHealth(P8, Men, 1, "Anywhere", 88),
	'');
	
	for($i=0; $i<3; $i++){
		for($j=0; $j<12; $j++){ $P8->_if($boss[$i]->health(Exactly, 9900-100*$j))->then($boss[$i]->type->add(120000-10000*$j)); }
	}
	for($i=0; $i<9; $i++){
		for($j=0; $j<12; $j++){ $P8->_if($enemyUnit[$i]->health(Exactly, 9900-100*$j))->then($enemyUnit[$i]->type->add(120000-10000*$j)); }
	}

	//FIND HEALTH
	for($i=0; $i<3; $i++){ $P8->_if( $boss[$i]->type->atLeast(10000) )->then( mult20($boss[$i], $P8, $tempdc) ); }
	for($i=0; $i<9; $i++){ $P8->_if( $enemyUnit[$i]->type->atLeast(10000) )->then( mult20($enemyUnit[$i], $P8, $tempdc) ); }

	//SET HEALTH
	for($i=12; $i>0; $i--){
		//GET HEALTH
		for($j=0; $j<3; $j++){
			$P8->_if( $boss[$j]->type->atLeast(10000*$i) )->then( $boss[$j]->type->subtract(10000*$i), $switch->set(), LoadInto($tempdc, $boss[$j]->type),
				/*[TEMPORARY]*/_if( $boss[$j]->health->atMost(0) )->then( $killswitch->set() ),/*[/TEMPORARY]*/'');
		}
		for($j=0; $j<9; $j++){
			$P8->_if( $enemyUnit[$j]->type->atLeast(10000*$i) )->then( $enemyUnit[$j]->type->subtract(10000*$i), $switch->set(), LoadInto($tempdc, $enemyUnit[$j]->type),
				/*[TEMPORARY]*/_if( $enemyUnit[$j]->health->atMost(0) )->then( $killswitch->set() ),/*[/TEMPORARY]*/ '');
		}
		//SET HEALTH
		for($j=1; $j<=19; $j++){ $P8->_if( $switch->is_set(), $tempdc->exactly($j) )->then( ModifyHealth(P8, Men, $i, "Anywhere", 5*$j), $switch->clear(), $tempdc->setTo(0) ); }
		$P8->_if( $switch->is_set(), $tempdc->exactly(20) )->then( ModifyHealth(P8, Men, $i, "Anywhere", 99), $switch->clear(), $tempdc->setTo(0) );
		$P8->_if( $switch->is_set() )->then( ModifyHealth(P8, Men, $i, "Anywhere", 100), $switch->clear(), $tempdc->setTo(0) );
		//[TEMPORARY]
		if($i==1){ $P8->_if( $killswitch->is_set() )->then( KillUnitAtLocation(P8, Men, 1, "sandbox"), $killswitch->clear() ); }
		else{
			$P8->_if( $killswitch->is_set() )->then(
				Give(P8, Men, $i-1, P9, "sandbox"),
				KillUnitAtLocation(P8, Men, 1, "sandbox"),
				Give(P9, Men, All, P8, "sandbox"),
				$killswitch->clear(),
			'');
		}
		//[/TEMPORARY]
	}

	$killswitch->kill();
	$switch->kill();
	$tempdc->kill();

?>
