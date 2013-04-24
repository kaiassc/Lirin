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
		
		$FXManager = new FXManager("$_SERVER[DOCUMENT_ROOT]/Lirin/Wavs");
		$UnitManager = new UnitManager(2);
		$BattleSystem = new BattleSystem();
		$SpellSystem = new SpellSystem(4);
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
		
		/**/
		$UnitManager->firstTrigs();
		/**/
		$BattleSystem->CreateEngine();
		/**/
		$SpellSystem->CreateEngine();
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
			#$types = Type::getHeroTypes();
			#$randtype = $types[array_rand($types)];
			#$x = 1417+($hero->BSid-3)*32;
			#$y = 810;
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
		
		
		$fireball = new Spell("Fireball");
		
		/**
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
		
		
		/**
		// Projectile Testing
		
		$ProjectileManager = new ProjectileManager(3);
		
		$hero = $heroes[0];
		$currentspell = new Deathcounter();
		
		$Q = new KeyStroke("Q");
		$W = new KeyStroke("W");
		$E = new KeyStroke("E");
		
		$P4->_if( $Q->pressed() )->then( $currentspell->setTo(0), Display("Spell 1") );
		$P4->_if( $W->pressed() )->then( $currentspell->setTo(1), Display("Spell 2") );
		$P4->_if( $E->pressed() )->then( $currentspell->setTo(2), Display("Spell 3") );
		
		$tempx  = new TempDC(630000);
		$tempy  = new TempDC(630000);
		$angle  = new TempDC();
		$xvel   = new TempDC();
		$yvel   = new TempDC();
		$dur    = new TempDC();
		
		$bam = new Sound("bam");
		
		$P4->always(
			$ProjectileManager->engine(),
		'');
		
		$proj = ProjectileManager::$projectiles[0];
		
		$success = new TempSwitch();
		$P4->_if( FRAGS::$P4Fragged )->then(
			FRAGS::$P4Fragged->clear(),
			
			_if( $currentspell->exactly(0) )->then(
				Display("Spell 0 Cast"),
				
				$proj->setPosition($hero->x, $hero->y),
				
				$angle->getAngle($hero->x, $hero->y, FRAGS::$x->P4, FRAGS::$y->P4),
				$angle->componentsInto($tempx, $tempy),
				
				$tempx->multiplyBy(32),
				$tempy->multiplyBy(32),
				$tempx->roundedDivideBy(100),
				$tempy->roundedDivideBy(100),
				
				$xvel->setTo(3200),
				$yvel->setTo(3200),
				
				
				_if( $angle->between(361,1079) )->then(
					$xvel->subtract($tempx),
				e)->_else(
					$xvel->add($tempx),
				''),
				_if( $angle->atMost(719) )->then(
					$yvel->subtract($tempy),
				''),
				_if( $angle->atLeast(720) )->then(
					$yvel->add($tempy),
				''),
				
				$proj->setVelocity($xvel, $yvel),
				$proj->setAcceleration(0,-32),
				
				$proj->duration->setTo(12),
				FX::rumbleAt(10, $hero->x, $hero->y),
				FX::playWavAt($bam, $hero->x, $hero->y),
			''),
			
			$tempx->release(),
			$tempy->release(),
			$xvel->release(),
			$yvel->release(),
			$angle->release(),
			$dur->release(),
			
			$success->release(),
		'');
		
		/**
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
		
		/**
		// WASD Movement
		$A = new KeyStroke("A");
		$S = new KeyStroke("S");
		$D = new KeyStroke("D");
		$W = new KeyStroke("W");
		
		$P4->always(
			$hero->Location->acquire(Loc::$main),
			ClearText(),
		'');
		
		$movepixels = 16;
		
		$P4->_if( $W->isDown() )->then(
			Loc::$main->slideUp($movepixels),
			Display("W"),
		'');
		$P4->_if( $S->isDown() )->then(
			Loc::$main->slideDown($movepixels),
			Display("S"),
		'');
		$P4->_if( $A->isDown() )->then(
			Loc::$main->slideLeft($movepixels),
			Display("A"),
		'');
		$P4->_if( $D->isDown() )->then(
			Loc::$main->slideRight($movepixels),
			Display("D"),
		'');
		
		$P4->always(
			#Loc::$main->explode(),
			Order($hero->Player, Type::$Melee->BaseUnit, Loc::$sandbox, Move, Loc::$main),
		'');
		/**/
		
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
			$FXManager->CreateEngine(),
		'');
		
		$GloreManager->gloreEngine();
		
		$UnitManager->lastTrigs();
		
		$LocationManager->CreateEngine();
		$UnitManager->CreateEngine();
		
		
		
	}
	
	
}

require_once("$_SERVER[DOCUMENT_ROOT]/"."Compiler/UserSpecific.php");
new Lirin();
