<?php require_once("BS/BSUnit.php"); require_once("BS/Boss.php"); require_once("BS/Enemy.php"); require_once("BS/Hero.php"); require_once("BS/Roamer.php"); 


class BattleSystem {

	/* @var Hero[] */   static private $Heroes  = array();
	/* @var Enemy[] */  static private $Enemies = array();
	/* @var Roamer[] */ static private $Roamers = array();
	/* @var Boss[] */   static private $Bosses  = array();
	
	/* @var Deathcounter[] */ static $typeDCs          = array();
	/* @var Deathcounter[] */ static $attackTimeDCs    = array();
	/* @var Deathcounter[] */ static $attackTargetDCs  = array();
	/* @var Deathcounter[] */ static $healthDCs        = array();
	/* @var Deathcounter[] */ static $maxhealthDCs     = array();
	/* @var Deathcounter[] */ static $manaDCs          = array();
	/* @var Deathcounter[] */ static $damageDCs        = array();
	/* @var Deathcounter[] */ static $armorDCs         = array();
	
	/* @var Deathcounter[] */ static $xDCs             = array();
	/* @var Deathcounter[] */ static $yDCs             = array();
	
	
	/* @var BSUnit[][] */ static $dcgroups;
	
	function __construct(){
		
		
	}
	
	function Setup(){
		
		$All = new Player(P1, P2, P3, P4, P5, P6, P7, P8);
		
		$this->dcgroups = array(
			
			array(
				self::$Roamers[] = new Roamer(P2, 1),
				self::$Roamers[] = new Roamer(P3, 2),
				self::$Heroes[]  =   new Hero(P4, 3),
				self::$Heroes[]  =   new Hero(P5, 4),
				self::$Heroes[]  =   new Hero(P6, 5),
				self::$Roamers[] =  new Enemy(P7, 6),
				self::$Enemies[] =  new Enemy(P8, 7),
			),
			
			array(
				self::$Enemies[] =  new Enemy(P2, 8), 
				self::$Enemies[] =  new Enemy(P3, 9),
				self::$Enemies[] =  new Enemy(P4, 10), // formerly merc
				self::$Enemies[] =  new Enemy(P5, 11), // formerly merc
				self::$Enemies[] =  new Enemy(P6, 12), // formerly merc
				self::$Enemies[] =  new Enemy(P7, 13),
				self::$Enemies[] =  new Enemy(P8, 14),
			),
			
			array(
				self::$Enemies[] =  new Enemy(P2, 15),
				self::$Enemies[] =  new Enemy(P3, 16),
				self::$Bosses[]  =   new Boss(P4, 17),
				self::$Bosses[]  =   new Boss(P5, 18),
				self::$Bosses[]  =   new Boss(P6, 19),
				self::$Enemies[] =  new Enemy(P7, 20),
				self::$Enemies[] =  new Enemy(P8, 21),
			),
			
		);
		
		
		// Assign each BSUnit their deathcounters for the proper players
		foreach($this->dcgroups as $group){
			/* @var BSUnit[] $group */
			self::$typeDCs[] =         $type =         new Deathcounter($All, 15);
			self::$attackTimeDCs[] =   $attackTime =   new Deathcounter($All, 15);
			self::$attackTargetDCs[] = $attackTarget = new Deathcounter($All, 18);
			self::$healthDCs[] =       $health =       new Deathcounter($All, 127);
			self::$maxhealthDCs[] =    $maxhealth =    new Deathcounter($All, 127); 
			self::$manaDCs[] =         $mana =         new Deathcounter($All, 127);
			self::$damageDCs[] =       $damage =       new Deathcounter($All, 127); 
			self::$armorDCs[] =        $armor =        new Deathcounter($All, 127);
			self::$xDCs[] =            $x =            new Deathcounter($All, Map::getWidth()*32);
			self::$yDCs[] =            $y =            new Deathcounter($All, Map::getHeight()*32);
			
			foreach($group as $bsunit){
				$bsunit->type =         $type->{$bsunit->dcplayer};
				$bsunit->attackTime =   $attackTime->{$bsunit->dcplayer};
				$bsunit->attackTarget = $attackTarget->{$bsunit->dcplayer};
				$bsunit->health =       $health->{$bsunit->dcplayer};
				$bsunit->maxhealth =    $maxhealth->{$bsunit->dcplayer};
				$bsunit->mana =         $mana->{$bsunit->dcplayer};
				$bsunit->damage =       $damage->{$bsunit->dcplayer};
				$bsunit->armor =        $armor->{$bsunit->dcplayer};
				$bsunit->x =            $x->{$bsunit->dcplayer};
				$bsunit->y =            $y->{$bsunit->dcplayer};
				
			}
		}
		
		// TESTING
		$P1 = new Player(P1);
		$P2 = new Player(P2);
		$P3 = new Player(P3);
		$P4 = new Player(P4);
		$P5 = new Player(P5);
		$P6 = new Player(P6);
		$P7 = new Player(P7);
		$P8 = new Player(P8);
		
		$P1->justonce( //SetAlly(P4), SetAlly(P5), SetAlly(P6),
			Give(P12, "Protoss Zealot", 1, P9, "sandbox"),       Give(P12, "Protoss Zealot", 1, P10, "sandbox"),       Give(P12, "Protoss Zealot", 1, P11, "sandbox"),
			Give(P12, "Protoss Dragoon", 1, P9, "sandbox"),      Give(P12, "Protoss Dragoon", 1, P10, "sandbox"),      Give(P12, "Protoss Dragoon", 1, P11, "sandbox"),
			Give(P12, "Protoss High Templar", 1, P9, "sandbox"), Give(P12, "Protoss High Templar", 1, P10, "sandbox"), Give(P12, "Protoss High Templar", 1, P11, "sandbox"),
			Give(P12, "Zerg Hydralisk", 1, P9, "sandbox"),       Give(P12, "Zerg Hydralisk", 1, P10, "sandbox"),       Give(P12, "Zerg Hydralisk", 1, P11, "sandbox"),
			self::$healthDCs[0]->leaderboard(),
		'');
		
		
		$hero1 = self::$Heroes[0];
		$hero2 = self::$Heroes[1];
		$hero3 = self::$Heroes[2];
		
		//Error(var_export($hero1, true));
		//$hero1->damage;
		$P1->justonce(
			self::$healthDCs[0]->Allies->setTo(100),    self::$healthDCs[1]->Allies->setTo(100),    self::$healthDCs[2]->Allies->setTo(100),
			self::$maxhealthDCs[0]->Allies->setTo(100), self::$maxhealthDCs[1]->Allies->setTo(100), self::$maxhealthDCs[2]->Allies->setTo(100),
			self::$damageDCs[0]->Allies->setTo(33),     self::$damageDCs[1]->Allies->setTo(33),     self::$damageDCs[2]->Allies->setTo(33),
			self::$armorDCs[0]->Allies->setTo(91),      self::$armorDCs[1]->Allies->setTo(33),      self::$armorDCs[2]->Allies->setTo(0),
			
			$hero1->damage->setTo(100),         $hero2->damage->setTo(100),         $hero3->damage->setTo(100),
		'');
		
		$P4->justonce(
			 Give(P9, "Protoss Zealot", 1, P4, "sandbox"), Give(P9, Men, All, P12, "sandbox"),
		'');
		$P5->justonce(
			Give(P10, "Protoss Zealot", 1, P5, "sandbox"), Give(P10, Men, All, P12, "sandbox"),
		'');
		$P6->justonce(
			Give(P11, "Protoss Zealot", 1, P6, "sandbox"), Give(P11, Men, All, P12, "sandbox"),
		'');
		$P8->justonce(
			RemoveUnitAtLocation(P9, Men, All, "sandbox"), RemoveUnitAtLocation(P10, Men, All, "sandbox"), RemoveUnitAtLocation(P11, Men, All, "sandbox"),
			Give(P12, "Zerg Hydralisk", 3, P8, "sandbox"), Give(P12, Men, 6, P7, "sandbox"),
		'');
		
	}
	
	function CreateEngine(){
		
		$P1 = new Player(P1);
		$P4 = new Player(P4);
		
		foreach(self::getBSUnits() as $bsunit){
			
		#	$P1->_if( $bsunit->swings() )->then(
		#		$bsunit->dealDamageToTarget(),
		#	'');
			
		}
		
		foreach(self::$Heroes as $hero){
			
			$P4->_if( $hero->swings() )->then(
				Display("Bam"),
				Display("MY GOD YOU SWANG! Target: {$hero->attackTarget}"),
				$hero->attackTarget->setTo(0),
			'');
		}
		
	}
	
	////
	// Auxillary
	//
	
	static function setAllyByTarget(Deathcounter $attackTarget, Deathcounter $dcgroupid){
		$text = repeat(1,
			SetEnemy(AllPlayers),
			$dcgroupid->setTo(0),
			
			_if( $attackTarget->atLeast(15) )->then( 
				$attackTarget->subtract(14), 
				$dcgroupid->add(2),
			''),
			_if( $attackTarget->atLeast(8) )->then( 
				$attackTarget->subtract(7), 
				$dcgroupid->add(1),
			''),
			
			_if( $attackTarget->exactly(1) )->then( SetAlly(P2) ),
			_if( $attackTarget->exactly(2) )->then( SetAlly(P3) ),
			_if( $attackTarget->exactly(3) )->then( SetAlly(P4) ),
			_if( $attackTarget->exactly(4) )->then( SetAlly(P5) ),
			_if( $attackTarget->exactly(5) )->then( SetAlly(P6) ),
			_if( $attackTarget->exactly(6) )->then( SetAlly(P7) ),
			_if( $attackTarget->exactly(7) )->then( SetAlly(P8) ),
		'');
		
		return $text;
	}
	
	static function convertToVulnerability(Deathcounter $armordc){
		$notyetfound = new TempSwitch();
		$text = $notyetfound->set();
		
		$lastvalue = null;
		
		for($i=1; $i<=100; $i++){
			$neededvalue = (int)round((100/$i-1)/0.06);
			
			// if the armor you need to get the percent is too large or the same amount of armor as the last percent, then move on 
			if( $neededvalue > 100 || $lastvalue === $neededvalue ){ continue; }
			
			$text .= _if( $armordc->atLeast($neededvalue), $notyetfound )->then(
				$armordc->setTo($i),
				$notyetfound->clear(),
			'');
			
			$lastvalue = $neededvalue;
		}
		$text .= _if( $notyetfound )->then( $armordc->setTo(100), $notyetfound->release() );
		
		return $text;
	}
	
	
	////
	// Getters
	//

	/**
	 * @return BSUnit[]
	 */
	static function getBSUnits(){
		return array_merge(self::$Heroes, self::$Roamers, self::$Enemies, self::$Bosses);
	}
	
	static function getHeroes(){
		return self::$Heroes;
	}
	
	static function getRoamers(){
		return self::$Roamers;
	}
	
	static function getEnemies(){
		return self::$Enemies;
	}
	
	static function getBosses(){
		return self::$Bosses;
	}
	
}