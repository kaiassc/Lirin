<?php





class GloreManager {
	
	/* @var GloreWorm[] */
	static private $glores = array();
	
	
	function __construct(){
		
	}
	
	static function registerWorm(GloreWorm $worm){
		if( !($worm instanceof GloreWorm) ){
			Error('$worm must be a GloreWorm object, you stupid fat hobbitses');
		}
		self::$glores[] = $worm;
	}
	
	
	function gloreEngine(){
		$P1 = new Player(P1);
		$P8 = new Player(P8);
		
		$spawntime = 1;
		
		$hatchery = new UnitGroup("Zerg Hatchery", P8, Loc::$larvabox);
		$larva    = new UnitGroup("Zerg Larva", P8, Loc::$larvabox);
		
		foreach(self::$glores as $glore){
			$P8->_if( Time::$elapsedLoops->exactly($spawntime) )->then(
				$hatchery->create(1),
			'');
			$P8->_if( Time::$elapsedLoops->exactly($spawntime+1) )->then(
				$hatchery->remove(),
				RunAIScriptAtLocation("Make These Units Patrol", Loc::$larvabox),
			'');
			$P8->_if( Time::$elapsedLoops->exactly($spawntime+3) )->then(
				Grid::putMain($glore->x, $glore->y),
				$larva->teleportTo(Loc::$main),
			'');
			$P8->_if( Time::$elapsedLoops->exactly($spawntime+6) )->then(
				Grid::putMain($glore->x, $glore->y),
				Loc::$main->acquire(Loc::$aoe1x1),
				$larva->teleportTo(Loc::$aoe1x1, 1, Loc::$aoe1x1),
			'');
			
			$spawntime += 5;
			
		}
		
		/**
		$vision = new UnitGroup("Zerg Zergling", P8, Loc::$spawnbox);
		$heroes = BattleSystem::getHeroes();
		foreach($heroes as $hero){
			foreach(self::$glores as $glore){
				$range = 5*32;
				$P1->_if( $hero->x->between($glore->x-$range, $glore->x+$range), $hero->x->between($glore->y-$range, $glore->y+$range) )->then(
					Grid::putMainRes($glore->x, $glore->y),
					Loc::$aoe1x1->centerOn(Loc::$main),
					_if( $vision->AllPlayers->notAt(Loc::$aoe1x1) )->then(
						Display("Bollucks"),
						$vision->create(1, Burrowed),
						$vision->teleportTo(Loc::$aoe1x1),
						$vision->giveTo($hero->visplayer, 1, Loc::$aoe1x1),
						$vision->{GetPlayerShorthand($hero->visplayer)}->teleportTo(Loc::$aoe1x1, Loc::$aoe1x1),
					''),
				'');
				
			}
		}
		/**/
		
	}
	
	
}