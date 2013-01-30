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
		$UnitManager = new UnitManager(0);
		$BattleSystem = new BattleSystem();
		new Grid(128, 96, 8/*px*/, 32);
		$LocationManager = new LocationManager();
		Loc::populate();
		Type::populate();
		
		
		// Players
		$P1 = new Player(P1);
		$P4 = new Player(P4);
		$P5 = new Player(P5);
		$P8 = new Player(P8);
		$All = new Player(P1,P2,P3,P4,P5,P6,P7,P8);
		$humans = new Player(P4, P5, P6);
		
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
		
		$timeofday = new Deathcounter(2400);
		$minutecounter = new Deathcounter();
		$secondcounter = new Deathcounter();
		
		// Handle time
		$P1->always(
			$secondcounter->add(1),
		'');
		$P1->_if( $secondcounter->atLeast(1) )->then(
			$secondcounter->setTo(0),
			$minutecounter->add(1),
			$timeofday->add(1),
		'');
		$P1->_if( $minutecounter->atLeast(60) )->then(
			$minutecounter->setTo(0),
			$timeofday->add(40),
		'');
		$P1->_if( $timeofday->atLeast(2400) )->then(
			$timeofday->setTo(0),
		'');
		
		
		$dcx = new Deathcounter(Map::getWidth()*32-1);
		$dcy = new Deathcounter(Map::getHeight()*32-1);
		
		$heroes = BattleSystem::getHeroes();
		$enemies = BattleSystem::getEnemies();
		$roamers = BattleSystem::getRoamers();
		
		foreach($enemies as $enemy){
			$types = Type::getEnemyTypes();
			$randtype = $types[array_rand($types)];
			$randx = rand(1285, 3618);
			$randy = rand(1461, 3500);
			$P1->justonce(
				$enemy->spawnAs($randtype, $randx, $randy),
			'');
		}
		foreach($heroes as $hero){
			//$types = Type::getHeroTypes();
			//$randtype = $types[array_rand($types)];
			$x = 1417+($hero->BSid-3)*32;
			$y = 810;
			$player = new Player($hero->Player);
			$player->justonce(
				$hero->spawnAs(Type::$Melee, $x, $y),
			'');
		}
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
		
		/**
		$scurrier = new Scurrier();
		$enemies[0]->isType($scurrier);
		$enemies[0]->isType(EnemyType::Scurrier);
		$enemies[0]->isType(EnemyType::Champion);
		
		foreach($enemies as $enemy){
			$scurrier = new Scurrier();
			$P1->_if( $enemy->isType($scurrier) )->then(
				Display("You selected a Scurrier"),
				$scurrier::Armor,
			'');
		}
		/**/
		
		$humans->justonce(
			SetAlly(P7),
		'');
		
		$humans->_if( IsCurrentPlayer() )->then(
			$SFXManager->CreateEngine(),
		'');
		
		$UnitManager->lastTrigs();
		
		$LocationManager->CreateEngine();
		$UnitManager->CreateEngine();
		
	}
	
	
}

require_once("$_SERVER[DOCUMENT_ROOT]/"."Compiler/UserSpecific.php");
new Lirin();
