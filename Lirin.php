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
	
	function Main(){
		
		$SFXManager = new SFXManager("$_SERVER[DOCUMENT_ROOT]/Lirin/Wavs");
		$BattleSystem = new BattleSystem();
		$Grid = new Grid(128, 96, 8/*px*/, 32);
		$UnitManager = new UnitManager(0);
		$LocationManager = new LocationManager();
		$Loc = new Loc();
		$Loc->populate();
		
		// Players
		$P1 = new Player(P1);
		$P4 = new Player(P4);
		$All = new Player(P1,P2,P3,P4,P5,P6,P7,P8);
		$humans = new Player(P4, P5, P6);
		
		
		UnitManager::MintUnit("Start Location", $All, 250, 1400);
		UnitManager::MintMapRevealers(P4);
		
		/**/
		$BattleSystem->Setup();
		$BattleSystem->CreateEngine();
		/**/
		
		
		/**/
		$K1 = new KeyStroke("1"); $K2 = new KeyStroke("2"); $K3 = new KeyStroke("3"); $K4 = new KeyStroke("4"); $K5 = new KeyStroke("5");
		$P4->_if( $K1->pressed() )->then( BattleSystem::$healthDCs[0]->leaderboard("Health Set 1") );
		$P4->_if( $K2->pressed() )->then( BattleSystem::$healthDCs[1]->leaderboard("Health Set 2") );
		$P4->_if( $K3->pressed() )->then( BattleSystem::$healthDCs[2]->leaderboard("Health Set 3") );
		$P4->_if( $K4->pressed() )->then( BattleSystem::$healthDCs[3]->leaderboard("Health Set 4") );
		$P4->_if( $K5->pressed() )->then( BattleSystem::$healthDCs[4]->leaderboard("Health Set 5") );
		/**/
		
		
		$dcx = new Deathcounter(Map::getWidth()*32-1);
		$dcy = new Deathcounter(Map::getHeight()*32-1);
		
		$heroes = BattleSystem::getHeroes();
		$enemies = BattleSystem::getEnemies();
		
		$humans->justonce(
			SetAlly(P7),
		'');
		
		/**/
		$P1->always(
			//$heroes[0]->scanUnit(),
			//$enemies[0]->scanUnit(),
			$enemies[0]->showHealth(),
		'');		
		/**/
		
		$LocationManager->CreateEngine();
		$UnitManager->CreateEngine();
		
	}
	
	
}

require_once("$_SERVER[DOCUMENT_ROOT]/"."Compiler/UserSpecific.php");
new Lirin();
