<?php require_once("Classes/"."Map.php"); require_once("Custom/Actions.php"); require_once("Custom/Conditions.php");

class Lirin extends Map {
	
	protected $MapTitle = "Lirin";
	protected $MapDescription = "";
	protected $Force1 = array( "Name" => "Visioners",    "Players" => array(P1 => Computer, P2 => Computer, P3 => Computer) );
	protected $Force2 = array( "Name" => "Humans",       "Players" => array(P4 => Human, P5 => Human, P6 => Human) );
	protected $Force3 = array( "Name" => "Allied Comp",  "Players" => array(P7 => Computer) );
	protected $Force4 = array( "Name" => "Enemy Comp",   "Players" => array(P8 => Computer) );
	
	static $xdim = 192;
	static $ydim = 128;
	
	protected $SuppressOutput = TRUE;
	
	function Main(){
		
		$SFXManager = new SFXManager("$_SERVER[DOCUMENT_ROOT]/Lirin/Wavs");
		$UnitManager = new UnitManager(2);
		$BattleSystem = new BattleSystem();
		new Grid(128, 96, 8/*px*/, 32);
		new Time(10/*min*/);
		$GloreManager = new GloreManager();
		$FRAGS = new FRAGS();
		$LocationManager = new LocationManager();
		Loc::populate();
		Type::populate();
		
		
		// Players
		$P1 = new Player(P1);
		$P4 = new Player(P4);
		$P5 = new Player(P5);
		$P6 = new Player(P6);
		$P8 = new Player(P8);
		$All = new Player(P1,P2,P3,P4,P5,P6,P7,P8);
		$humans = new Player(P4, P5, P6);
		$visioners = new Player(P1, P2, P3);
		
		UnitManager::MintUnit("Start Location", $All, 250, 1400);
		UnitManager::MintMapRevealers(P4);
		
		$UnitManager->firstTrigs();
		
		/**/
		$BattleSystem->CreateEngine();
		/**/
		
		
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
		// FRAGS
		
		$P4->justonce( $FRAGS->giveFrags(P4, P9) );
		$P5->justonce( $FRAGS->giveFrags(P5, P10) );
		$P6->justonce( $FRAGS->giveFrags(P6, P11) );
		
		$P1->always(
			$FRAGS->getCoordinate(),
		'');
		
		$success = new TempSwitch();
		$P4->_if( FRAGS::$P4Fragged )->then(
			FRAGS::$P4Fragged->clear(),
			Grid::putMain(FRAGS::$x->P4, FRAGS::$y->P4, $success),
			_if( $success )->then(
				Grid::$main->explode(),
				$success->release(),
			''),
		'');
		/**/
		
		$dcx = new Deathcounter(Map::getWidth()*32-1);
		$dcy = new Deathcounter(Map::getHeight()*32-1);
		
		$heroes = BattleSystem::getHeroes();
		$enemies = BattleSystem::getEnemies();
		$roamers = BattleSystem::getRoamers();
		
		/**
		// Spawn Enemies 
		foreach($enemies as $enemy){
			$types = Type::getEnemyTypes();
			$randtype = $types[array_rand($types)];
			$randx = rand(1285, 3618);
			$randy = rand(1461, 3500);
			$P1->justonce(
				$enemy->spawnAs($randtype, $randx, $randy),
			'');
		}
		/**/
		
		/**/
		// Spawn Heroes
		foreach($heroes as $hero){
			//$types = Type::getHeroTypes();
			//$randtype = $types[array_rand($types)];
			$x = 1417+($hero->BSid-3)*32;
			$y = 810;
			$player = new Player($hero->Player);
			$player->justonce(
				$hero->spawnAs(Type::$Melee, 2555, 2444),
			'');
		}
		/**/
		
		/**
		// Spawn Roamers
		$typeindex = 0;
		foreach($roamers as $roamer){
			$types = Type::getRoamerTypes();
			$randtype = $types[$typeindex];
			$typeindex++;
			$x = 3800+($typeindex-3)*64;
			$y = 1000;
			$P1->justonce(
				$roamer->spawnAs($randtype, $x, $y),
			'');
		}
		/**/
		
		/** 
		// Chat style test
		$A = new KeyStroke("A");
		$D = new KeyStroke("D");
		
		$display = new Deathcounter();
		
		$P4->_if( $A->pressed() )->then(
			$display->subtract(1),
		'');
		$P4->_if( $D->pressed() )->then(
			$display->add(1),
		'');
		
		$P4->always(
			ClearText(),
		'');
		
		$P4->_if( $display->exactly(1) )->then(
			Display("\\x013\\x00f.\\x004 Tagan \\x00f."),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01119 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(2) )->then(
			Display("\\x013\\x01c.\\x004 Tagan \\x01c."),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01919 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(3) )->then(
			Display("\\x013\\x010.\\x004 Tagan \\x010."),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01719 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(4) )->then(
			Display("\\x013\\x006.\\x004 Tagan \\x006."),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01119 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(5) )->then(
			Display("\\x013\\x006.\\x00f:\\x004 Tagan \\x00f:\\x006."),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01b19 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(6) )->then(
			DisplayAlt("\\x013\\x006.\\x010:\\x004 Tagan \\x010:\\x006."),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01119 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(7) )->then(
			Display("\\x013\\x018:\\x004 TAGAN \\x018:"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01119 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(8) )->then(
			DisplayAlt("\\x013\\x004Tagan"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room?"),
			Display("\\x013\\x004I have 2 beds open, for only \\x01119 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(9) )->then(
			DisplayAlt("\\x013\\x004Tagan"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x004Are you looking for a room? I have"),
			Display("\\x013\\x0042 beds open, for only 19 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(10) )->then(
			DisplayAlt("\\x013\\x004Tagan"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x00fAre you looking for a room? I have"),
			Display("\\x013\\x00f2 beds open, for only 19 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(11) )->then(
			DisplayAlt("\\x013\\x004Tagan"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x01cAre you looking for a room? I have"),
			Display("\\x013\\x01c2 beds open, for only 19 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(12) )->then(
			DisplayAlt("\\x013\\x004Tagan"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x01eAre you looking for a room? I have"),
			Display("\\x013\\x01e2 beds open, for only 19 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		$P4->_if( $display->exactly(13) )->then(
			DisplayAlt("\\x013\\x002Tagan"),
			Display(" "),
			Display(" "),
			Display("\\x013\\x01eAre you looking for a room? I have"),
			Display("\\x013\\x01e2 beds open, for only 19 gold"),
			Display(" "),
			Display(" "),
			Display(" "),
		'');
		/**/
		
		/** 
		// Show BSID
		$ctrl = new KeyStroke("Ctrl");
		
		foreach($BattleSystem::getBSUnits() as $bsunit){
			$P4->_if( $ctrl->isDown(), $bsunit->isSelectedByP4() )->then(
				ClearText(),
				$bsunit->display(),
			'');
			$P5->_if( $ctrl->isDown(), $bsunit->isSelectedByP5() )->then(
				ClearText(),
				$bsunit->display(),
			'');
			
		}
		$humans->_if( $ctrl->released() )->then(
			ClearText(),
		'');
		/**/
		
		
		/**
		// Drunk and Rumble testing
		$Q = new KeyStroke("Q");

		$P1->_if( Elapsed(AtLeast, 5) )->then_justonce(
			$SFXManager->DrunkTimer->add(120),
		'');
		
		$P1->_if( $Q->pressed() )->then(
			$SFXManager->getRumbleAtCommand(50, 2555, 2444),
		'');
		/**/
		
		$humarths = new VirtualLocation(1400, 1500, 2350, 2450);
		
		
		new GloreWorm(2145, 2311);
		new GloreWorm(1760, 2260);
		new GloreWorm(1895, 2395);
		new GloreWorm(2597, 2403);
		
		
		
		$ProjectileManager = new ProjectileManager(1);
		
		$P4->always(
			$ProjectileManager->engine(),
		'');
		
		$proj = ProjectileManager::$projectiles[0];
		
		$P4->_if( Elapsed(AtLeast, 10) )->then_justonce(
			Display("Start Projectile"),
			$proj->setPosition(3072*100, 2048*100),
			$proj->setVelocity(3200*6400+3200),
			$proj->setDuration(5),
			$proj->setAcceleration(3200*6400+3200),
		'');
		
		$P4->_if( $proj->duration->atLeast(1) )->then(
			Display("duration: {$proj->duration}"),
		'');
		
		
		/**
		// Time testing
		$P4->_if( Time::$Clock->atLeast(2359) )->then(
			Display("GRAH HAHAH ITS A NEW DAY"),
		'');
		
		$P4->_if( Elapsed(AtLeast, 10*60) )->then_justonce(
			Display("Elapsed 10 min"),
		'');
		
		$P4->_if( Time::realMinuteStrokes() )->then(
			Display("new minute"),
		'');
		
		$P4->always(
			$P4->setGas(Time::$Clock),
		'');
		/**/
		
		
		$humans->_if( IsCurrentPlayer() )->then(
			$SFXManager->CreateEngine(),
		'');
		
		$GloreManager->gloreEngine();
		
		$UnitManager->lastTrigs();
		
		$LocationManager->CreateEngine();
		$UnitManager->CreateEngine();
	}
	
	
}

require_once("$_SERVER[DOCUMENT_ROOT]/"."Compiler/UserSpecific.php");
new Lirin();
