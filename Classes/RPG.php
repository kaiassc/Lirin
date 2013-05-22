<?php


class RPG extends Map {
	
	function Main(){
		$humans = new Player(P4, P5, P6);
		
		$dc1 = new Deathcounter(10);
		$dc2 = new Deathcounter(11);
		$dc3 = new Deathcounter(12);
		
		$dcarray = new DCArray($dc1, $dc2, $dc3);
		
		$humans->justonce(
			$dcarray->add(1),
		'');
		
		
		
		$All = new Player(P1,P2,P3,P4,P5,P6,P7,P8);
		
		
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
		
		UnitManager::MintUnit("Start Location", $All, 4720, 752);
		UnitManager::MintMapRevealers(P4);
		
		$UnitManager->FirstTrigs();     # 04/26/13   331 triggers
		$BattleSystem->CreateEngine();  # 04/23/13 6,557 triggers
		$SpellSystem->CreateEngine();   # 04/23/13 7,833 triggers
		$FRAGS->CreateEngine();         # 04/23/13 8,322 triggers
		
		/////////////////////////
		$this->loop();
		/////////////////////////
		
		$humans->_if( IsCurrentPlayer() )->then(
			$FXManager->CreateEngine(),
		'');
		
		$GloreManager->gloreEngine();
		
		$UnitManager->LastTrigs();
		
		$LocationManager->CreateEngine();
		$UnitManager->CreateEngine();
	}
	
	function loop(){}
	
	
}