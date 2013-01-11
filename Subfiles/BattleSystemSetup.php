<?php
	
	/*
	$players = new Player(P4, P5, P6);
	$all = new Player(P1, P2, P3, P4, P5, P6, P7, P8);
	global $heroP1, $heroP2, $heroP3, $roam1, $roam2, $roam3, $mercP1, $mercP2, $mercP3, $bossP1, $bossP2, $bossP3, $enemyUnit;
	global $healthA, $healthB, $healthC, $armorA, $armorB, $armorC;
	
	
	//BLOCK 1: IDs 0-6
	$type = new Deathcounter($all, 15);
	$heroP1->type = $type->P4; 
	$heroP2->type = $type->P5; 
	$heroP3->type = $type->P6;
	$roam1->type = $type->P2; 
	$roam2->type = $type->P3; 
	$roam3->type = $type->P7; 
	$enemyUnit[0]->type = $type->P8;
	
	$attackTime = new Deathcounter($all, 15);
	$heroP1->attackTime = $attackTime->P4; 
	$heroP2->attackTime = $attackTime->P5; 
	$heroP3->attackTime = $attackTime->P6; 
	$roam1->attackTime = $attackTime->P2; 
	$roam2->attackTime = $attackTime->P3; 
	$roam3->attackTime = $attackTime->P7; 
	$enemyUnit[0]->attackTime = $attackTime->P8; 
	
	$attackTarget = new Deathcounter($all, 18); 
	$heroP1->attackTarget = $attackTarget->P4; 
	$heroP2->attackTarget = $attackTarget->P5; 
	$heroP3->attackTarget = $attackTarget->P6; 
	$roam1->attackTarget = $attackTarget->P2; 
	$roam2->attackTarget = $attackTarget->P3; 
	$roam3->attackTarget = $attackTarget->P7; 
	$enemyUnit[0]->attackTarget = $attackTarget->P8; 
	
	$healthA = $health = new Deathcounter($all, 127); 
	$heroP1->health = $health->P4; 
	$heroP2->health = $health->P5; 
	$heroP3->health = $health->P6;
	$roam1->health = $health->P2; 
	$roam2->health = $health->P3; 
	$roam3->health = $health->P7; 
	$enemyUnit[0]->health = $health->P8;
	
	$maxhealth = new Deathcounter($all, 127); 
	$heroP1->maxhealth = $maxhealth->P4; 
	$heroP2->maxhealth = $maxhealth->P5; 
	$heroP3->maxhealth = $maxhealth->P6;
	$roam1->maxhealth = $maxhealth->P2; 
	$roam2->maxhealth = $maxhealth->P3; 
	$roam3->maxhealth = $maxhealth->P7; 
	$enemyUnit[0]->maxhealth = $maxhealth->P8;
	
	$mana = new Deathcounter($all, 127); 
	$heroP1->mana = $mana->P4; 
	$heroP2->mana = $mana->P5; 
	$heroP3->mana = $mana->P6;
	$roam1->mana = $mana->P2; 
	$roam2->mana = $mana->P3; 
	$roam3->mana = $mana->P7; 
	$enemyUnit[0]->mana = $mana->P8;
	
	$damage = new Deathcounter($all, 127); 
	$heroP1->damage = $damage->P4; 
	$heroP2->damage = $damage->P5; 
	$heroP3->damage = $damage->P6;
	$roam1->damage = $damage->P2; 
	$roam2->damage = $damage->P3; 
	$roam3->damage = $damage->P7; 
	$enemyUnit[0]->damage = $damage->P8;
	
	$armorA = $armor = new Deathcounter($all, 127);
	$heroP1->armor = $armor->P4; 
	$heroP2->armor = $armor->P5; 
	$heroP3->armor = $armor->P6;
	$roam1->armor = $armor->P2; 
	$roam2->armor = $armor->P3; 
	$roam3->armor = $armor->P7; 
	$enemyUnit[0]->armor = $armor->P8;

	//BLOCK 2: IDs 7-13
	$type = new Deathcounter($all, 15); 
	$mercP1->type = $type->P4; 
	$mercP2->type = $type->P5; 
	$mercP3->type = $type->P6;
	$enemyUnit[1]->type = $type->P2; 
	$enemyUnit[2]->type = $type->P3;
	$enemyUnit[3]->type = $type->P7; 
	$enemyUnit[4]->type = $type->P8;
	
	$attackTime = new Deathcounter($all, 15); 
	$mercP1->attackTime = $attackTime->P4; 
	$mercP2->attackTime = $attackTime->P5; 
	$mercP3->attackTime = $attackTime->P6;
	$enemyUnit[1]->attackTime = $attackTime->P2; 
	$enemyUnit[2]->attackTime = $attackTime->P3; 
	$enemyUnit[3]->attackTime = $attackTime->P7; 
	$enemyUnit[4]->attackTime = $attackTime->P8;
	
	$attackTarget = new Deathcounter($all, 18); $mercP1->attackTarget = $attackTarget->P4; $mercP2->attackTarget = $attackTarget->P5; $mercP3->attackTarget = $attackTarget->P6;
		$enemyUnit[1]->attackTarget = $attackTarget->P2; $enemyUnit[2]->attackTarget = $attackTarget->P3; $enemyUnit[3]->attackTarget = $attackTarget->P7; $enemyUnit[4]->attackTarget = $attackTarget->P8;
	$healthB = $health = new Deathcounter($all, 127); $mercP1->health = $health->P4; $mercP2->health = $health->P5; $mercP3->health = $health->P6;
		$enemyUnit[1]->health = $health->P2; $enemyUnit[2]->health = $health->P3; $enemyUnit[3]->health = $health->P7; $enemyUnit[4]->health = $health->P8;
	$maxhealth = new Deathcounter($all, 127); $mercP1->maxhealth = $maxhealth->P4; $mercP2->maxhealth = $maxhealth->P5; $mercP3->maxhealth = $maxhealth->P6;
		$enemyUnit[1]->maxhealth = $maxhealth->P2; $enemyUnit[2]->maxhealth = $maxhealth->P3; $enemyUnit[3]->maxhealth = $maxhealth->P7; $enemyUnit[4]->maxhealth = $maxhealth->P8;
	$mana = new Deathcounter($all, 127); $mercP1->mana = $mana->P4; $mercP2->mana = $mana->P5; $mercP3->mana = $mana->P6;
		$enemyUnit[1]->mana = $mana->P2; $enemyUnit[2]->mana = $mana->P3; $enemyUnit[3]->mana = $mana->P7; $enemyUnit[4]->mana = $mana->P8;
	$damage = new Deathcounter($all, 127); $mercP1->damage = $damage->P4; $mercP2->damage = $damage->P5; $mercP3->damage = $damage->P6;
		$enemyUnit[1]->damage = $damage->P2; $enemyUnit[2]->damage = $damage->P3; $enemyUnit[3]->damage = $damage->P7; $enemyUnit[4]->damage = $damage->P8;
	$armorB = $armor = new Deathcounter($all, 127); $mercP1->armor = $armor->P4; $mercP2->armor = $armor->P5; $mercP3->armor = $armor->P6;
		$enemyUnit[1]->armor = $armor->P2; $enemyUnit[2]->armor = $armor->P3; $enemyUnit[3]->armor = $armor->P7; $enemyUnit[4]->armor = $armor->P8;

	//BLOCK 3: IDs 14-20
	$type = new Deathcounter($all, 15); $enemyUnit[5]->type = $type->P2; $enemyUnit[6]->type = $type->P3; $bossP1->type = $type->P4;
		$bossP2->type = $type->P5; $bossP3->type = $type->P6; $enemyUnit[7]->type = $type->P7; $enemyUnit[8]->type = $type->P8;
	$attackTime = new Deathcounter($all, 15); $enemyUnit[5]->attackTime = $attackTime->P2; $enemyUnit[6]->attackTime = $attackTime->P3; $bossP1->attackTime = $attackTime->P4;
		$bossP2->attackTime = $attackTime->P5; $bossP3->attackTime = $attackTime->P6; $enemyUnit[7]->attackTime = $attackTime->P7; $enemyUnit[8]->attackTime = $attackTime->P8;
	$attackTarget = new Deathcounter($all, 18); $enemyUnit[5]->attackTarget = $attackTarget->P2; $enemyUnit[6]->attackTarget = $attackTarget->P3; $bossP1->attackTarget = $attackTarget->P4;
		$bossP2->attackTarget = $attackTarget->P5; $bossP3->attackTarget = $attackTarget->P6; $enemyUnit[7]->attackTarget = $attackTarget->P7; $enemyUnit[8]->attackTarget = $attackTarget->P8;
	$healthC = $health = new Deathcounter($all, 127); $enemyUnit[5]->health = $health->P2; $enemyUnit[6]->health = $health->P3; $bossP1->health = $health->P4;
		$bossP2->health = $health->P5; $bossP3->health = $health->P6; $enemyUnit[7]->health = $health->P7; $enemyUnit[8]->health = $health->P8;
	$maxhealth = new Deathcounter($all, 127); $enemyUnit[5]->maxhealth = $maxhealth->P2; $enemyUnit[6]->maxhealth = $maxhealth->P3; $bossP1->maxhealth = $maxhealth->P4;
		$bossP2->maxhealth = $maxhealth->P5; $bossP3->maxhealth = $maxhealth->P6; $enemyUnit[7]->maxhealth = $maxhealth->P7; $enemyUnit[8]->maxhealth = $maxhealth->P8;
	$mana = new Deathcounter($all, 127); $enemyUnit[5]->mana = $mana->P2; $enemyUnit[6]->mana = $mana->P3; $bossP1->mana = $mana->P4;
		$bossP2->mana = $mana->P5; $bossP3->mana = $mana->P6; $enemyUnit[7]->mana = $mana->P7; $enemyUnit[8]->mana = $mana->P8;
	$damage = new Deathcounter($all, 127); $enemyUnit[5]->damage = $damage->P2; $enemyUnit[6]->damage = $damage->P3; $bossP1->damage = $damage->P4;
		$bossP2->damage = $damage->P5; $bossP3->damage = $damage->P6; $enemyUnit[7]->damage = $damage->P7; $enemyUnit[8]->damage = $damage->P8;
	$armorC = $armor = new Deathcounter($all, 127); $enemyUnit[5]->armor = $armor->P2; $enemyUnit[6]->armor = $armor->P3; $bossP1->armor = $armor->P4;
		$bossP2->armor = $armor->P5; $bossP3->armor = $armor->P6; $enemyUnit[7]->armor = $armor->P7; $enemyUnit[8]->armor = $armor->P8;

	//MISC. HERO/MERC STATS
	$x = new Deathcounter($players, 2047); $heroP1->x = $x->P4; $heroP2->x = $x->P5; $heroP3->x = $x->P6;
	$y = new Deathcounter($players, 511);  $heroP1->y = $y->P4; $heroP2->y = $y->P5; $heroP3->y = $y->P6;
	
	
	
	//GIVE UNITS
	$P1->justonce( //SetAlly(P4), SetAlly(P5), SetAlly(P6),
		Give(P12, "Protoss Zealot", 1, P9, "sandbox"),       Give(P12, "Protoss Zealot", 1, P10, "sandbox"),       Give(P12, "Protoss Zealot", 1, P11, "sandbox"),
		Give(P12, "Protoss Dragoon", 1, P9, "sandbox"),      Give(P12, "Protoss Dragoon", 1, P10, "sandbox"),      Give(P12, "Protoss Dragoon", 1, P11, "sandbox"),
		Give(P12, "Protoss High Templar", 1, P9, "sandbox"), Give(P12, "Protoss High Templar", 1, P10, "sandbox"), Give(P12, "Protoss High Templar", 1, P11, "sandbox"),
		Give(P12, "Zerg Hydralisk", 1, P9, "sandbox"),       Give(P12, "Zerg Hydralisk", 1, P10, "sandbox"),       Give(P12, "Zerg Hydralisk", 1, P11, "sandbox"),
	'');
	$P4->justonce(
		$heroP1->type->setTo(0), $heroP1->health->setTo(100), $heroP1->maxhealth->setTo(100), $heroP1->mana->setTo(0), $heroP1->damage->setTo(100), $heroP1->armor->setTo(91),
		$mercP1->type->setTo(0), $mercP1->health->setTo(100), $mercP1->maxhealth->setTo(100), $mercP1->mana->setTo(0), $mercP1->damage->setTo(33), $mercP1->armor->setTo(91),
		$bossP1->type->setTo(0), $bossP1->health->setTo(100), $bossP1->maxhealth->setTo(100), $bossP1->mana->setTo(0), $bossP1->damage->setTo(33), $bossP1->armor->setTo(91),
		 Give(P9, "Protoss Zealot", 1, P4, "sandbox"), Give(P9, Men, All, P12, "sandbox"),
	'');
	$P5->justonce(
		$heroP2->type->setTo(0), $heroP2->health->setTo(100), $heroP2->maxhealth->setTo(100), $heroP2->mana->setTo(0), $heroP2->damage->setTo(100), $heroP2->armor->setTo(91),
		$mercP2->type->setTo(0), $mercP2->health->setTo(100), $mercP2->maxhealth->setTo(100), $mercP2->mana->setTo(0), $mercP2->damage->setTo(33), $mercP2->armor->setTo(91),
		$bossP2->type->setTo(0), $bossP2->health->setTo(100), $bossP2->maxhealth->setTo(100), $bossP2->mana->setTo(0), $bossP2->damage->setTo(33), $bossP2->armor->setTo(91),
		Give(P10, "Protoss Zealot", 1, P5, "sandbox"), Give(P10, Men, All, P12, "sandbox"),
	'');
	$P6->justonce(
		$heroP3->type->setTo(0), $heroP3->health->setTo(100), $heroP3->maxhealth->setTo(100), $heroP3->mana->setTo(0), $heroP3->damage->setTo(100), $heroP3->armor->setTo(91),
		$mercP3->type->setTo(0), $mercP3->health->setTo(100), $mercP3->maxhealth->setTo(100), $mercP3->mana->setTo(0), $mercP3->damage->setTo(33), $mercP3->armor->setTo(91),
		$bossP3->type->setTo(0), $bossP3->health->setTo(100), $bossP3->maxhealth->setTo(100), $bossP3->mana->setTo(0), $bossP3->damage->setTo(33), $bossP3->armor->setTo(91),
		Give(P11, "Protoss Zealot", 1, P6, "sandbox"), Give(P11, Men, All, P12, "sandbox"),
	'');
	$P8->justonce(
		$roam1->type->setTo(0), $roam1->health->setTo(100), $roam1->maxhealth->setTo(100), $roam1->mana->setTo(0), $roam1->damage->setTo(33), $roam1->armor->setTo(91),
		$roam2->type->setTo(0), $roam2->health->setTo(100), $roam2->maxhealth->setTo(100), $roam2->mana->setTo(0), $roam2->damage->setTo(33), $roam2->armor->setTo(91),
		$roam3->type->setTo(0), $roam3->health->setTo(100), $roam3->maxhealth->setTo(100), $roam3->mana->setTo(0), $roam3->damage->setTo(33), $roam3->armor->setTo(91),
		RemoveUnitAtLocation(P9, Men, All, "sandbox"), RemoveUnitAtLocation(P10, Men, All, "sandbox"), RemoveUnitAtLocation(P11, Men, All, "sandbox"),
		Give(P12, "Zerg Hydralisk", 3, P8, "sandbox"), Give(P12, Men, 6, P7, "sandbox"),
	'');
	
	//SET STATS
	$P1->justonce( 
		$enemyUnit[0]->type->setTo(0), $enemyUnit[0]->health->setTo(100), $enemyUnit[0]->maxhealth->setTo(100), $enemyUnit[0]->mana->setTo(0), $enemyUnit[0]->damage->setTo(33), $enemyUnit[0]->armor->setTo(91),
		$enemyUnit[1]->type->setTo(0), $enemyUnit[1]->health->setTo(100), $enemyUnit[1]->maxhealth->setTo(100), $enemyUnit[1]->mana->setTo(0), $enemyUnit[1]->damage->setTo(33), $enemyUnit[1]->armor->setTo(66),
		$enemyUnit[2]->type->setTo(0), $enemyUnit[2]->health->setTo(100), $enemyUnit[2]->maxhealth->setTo(100), $enemyUnit[2]->mana->setTo(0), $enemyUnit[2]->damage->setTo(33), $enemyUnit[2]->armor->setTo(49),
		$enemyUnit[3]->type->setTo(0), $enemyUnit[3]->health->setTo(100), $enemyUnit[3]->maxhealth->setTo(100), $enemyUnit[3]->mana->setTo(0), $enemyUnit[3]->damage->setTo(33), $enemyUnit[3]->armor->setTo(17),
		$enemyUnit[4]->type->setTo(0), $enemyUnit[4]->health->setTo(100), $enemyUnit[4]->maxhealth->setTo(100), $enemyUnit[4]->mana->setTo(0), $enemyUnit[4]->damage->setTo(33), $enemyUnit[4]->armor->setTo(0), 
		$enemyUnit[5]->type->setTo(0), $enemyUnit[5]->health->setTo(100), $enemyUnit[5]->maxhealth->setTo(100), $enemyUnit[5]->mana->setTo(0), $enemyUnit[5]->damage->setTo(33), $enemyUnit[5]->armor->setTo(0), 
		$enemyUnit[6]->type->setTo(0), $enemyUnit[6]->health->setTo(100), $enemyUnit[6]->maxhealth->setTo(100), $enemyUnit[6]->mana->setTo(0), $enemyUnit[6]->damage->setTo(33), $enemyUnit[6]->armor->setTo(0), 
		$enemyUnit[7]->type->setTo(0), $enemyUnit[7]->health->setTo(100), $enemyUnit[7]->maxhealth->setTo(100), $enemyUnit[7]->mana->setTo(0), $enemyUnit[7]->damage->setTo(33), $enemyUnit[7]->armor->setTo(0), 
		$enemyUnit[8]->type->setTo(0), $enemyUnit[8]->health->setTo(100), $enemyUnit[8]->maxhealth->setTo(100), $enemyUnit[8]->mana->setTo(0), $enemyUnit[8]->damage->setTo(33), $enemyUnit[8]->armor->setTo(0), 
	'');

	*/
	
?>
