<?php require_once("Classes/"."Map.php"); require_once("Custom/Actions.php"); require_once("Custom/Conditions.php");

class Lirin extends RPG {
	
	protected $MapTitle = "Lirin";
	protected $MapDescription = "";
	protected $Force1 = array( "Name" => "Visioners",    "Players" => array(P1 => Computer, P2 => Computer, P3 => Computer) );
	protected $Force2 = array( "Name" => "Humans",       "Players" => array(P4 => Human, P5 => Human, P6 => Human) );
	protected $Force3 = array( "Name" => "Allied Comp",  "Players" => array(P7 => Computer) );
	protected $Force4 = array( "Name" => "Enemy Comp",   "Players" => array(P8 => Computer) );
	
	static $xdim = 192, $ydim = 128;
	
	protected $SuppressOutput = TRUE;
	
	function loop(){
		
		// Players
		$P1 = new Player(P1);
		$P4 = new Player(P4);
		$P5 = new Player(P5);
		$P6 = new Player(P6);
		$P8 = new Player(P8);
		$All = new Player(P1,P2,P3,P4,P5,P6,P7,P8);
		$humans = new Player(P4, P5, P6);
		$visioners = new Player(P1, P2, P3);
		
		$humans->justonce(
			SetAlly(AllPlayers),
			Mute(),
		'');
		
		$P4->justonce( GetVisionOf(P1) );
		$P5->justonce( GetVisionOf(P2) );
		$P6->justonce( GetVisionOf(P3) );
		
		$visioners->justonce(
			SetAlly(AllPlayers),
		'');
		
		
		/**/
		// Spawn Heroes
		$heroes = BattleSystem::getHeroes();
		foreach($heroes as $hero){
			$player = new Player($hero->Player);
			$player->justonce(
				$hero->spawnAs(Type::$Melee, 2555, 2444),
			'');
		}
		/**/
		
		new GloreWorm(2145, 2311);
		new GloreWorm(1760, 2260);
		new GloreWorm(1895, 2395);
		new GloreWorm(2597, 2403);
		
		
		
		
	}
	
	
}

require_once("$_SERVER[DOCUMENT_ROOT]/"."Compiler/UserSpecific.php");
new Lirin();


	/*
	
	// $area = new VirtualLocation(1400, 1500, 2350, 2450);
	
	// Drunk and Rumble testing
	$Q = new KeyStroke("Q");

		$P1->_if( Elapsed(AtLeast, 5) )->then_justonce(
			$SFXManager->DrunkTimer->add(120),
		'');
		
		$P1->_if( $Q->pressed() )->then(
			$SFXManager->getRumbleAtCommand(50, 2555, 2444),
		'');
		/**/
		
		/**
		// DEE testing
		$shadow = new UnitGroup("Zerg Devourer", P8, Loc::$aoe1x1);
		
		$P1->_if( Time::$elapsedLoops->exactly(1) )->then(
			Loc::$aoe1x1->placeAtRes(4912, 560),
			$shadow->create(1, Invincible),
			$shadow->disableDoodadState(),
		'');
		$P1->_if( Time::$elapsedLoops->exactly(2) )->then(
			Loc::$aoe1x1->placeAtRes(4912, 560),
			$shadow->enableDoodadState(),
			$shadow->enableDoodadState(),
		'');
		$P1->_if( Time::$elapsedLoops->exactly(3) )->then(
			Loc::$aoe1x1->placeAtRes(4904, 560),
			$shadow->moveTo(Loc::$aoe1x1),
		'');
		
		$P1->_if( Time::$elapsedLoops->exactly(4) )->then(
			Loc::$aoe1x1->placeAtRes(4912, 560),
			$shadow->giveTo(P4),
		'');
		$P1->_if( Time::$elapsedLoops->exactly(80) )->then(
			Loc::$aoe1x1->placeAtRes(4912, 560),
			$shadow->P4->giveTo(P8),
		'');
		$P1->_if( Time::$elapsedLoops->exactly(81) )->then(
			Loc::$aoe1x1->placeAtRes(4912, 560),
			CreateUnit(P4, "Protoss Observer", 1, Anywhere),
		'');
		
		$P1->_if( Time::$elapsedLoops->atLeast(82) )->then(
			Loc::$main->centerOn(P4, "Protoss Observer", Anywhere),
			$shadow->teleportTo(Loc::$main, All, Anywhere),
		'');
		/**/
