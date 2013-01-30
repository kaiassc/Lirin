<?php require_once("BS/BSUnit.php"); require_once("BS/Boss.php"); require_once("BS/Enemy.php"); require_once("BS/Hero.php"); require_once("BS/Roamer.php"); 
	  require_once("BS/Types/Type.php"); require_once("BS/Types/BSType.php"); require_once("BS/Types/EnemyType.php"); require_once("BS/Types/HeroType.php"); require_once("BS/Types/RoamerType.php");


class BattleSystem {

	/* @var Hero[] */   static private $Heroes  = array();
	/* @var Enemy[] */  static private $Enemies = array();
	/* @var Roamer[] */ static private $Roamers = array();
	/* @var Boss[] */   static private $Bosses  = array();
	
	/* @var Deathcounter[] */ static $typeDCs          = array();
	/* @var Deathcounter[] */ static $baseidDCs        = array();
	/* @var Deathcounter[] */ static $apparentidDCs    = array();
	/* @var Deathcounter[] */ static $attackTimeDCs    = array();
	/* @var Deathcounter[] */ static $attackTargetDCs  = array();
	/* @var Deathcounter[] */ static $healthDCs        = array();
	/* @var Deathcounter[] */ static $maxhealthDCs     = array();
	/* @var Deathcounter[] */ static $manaDCs          = array();
	/* @var Deathcounter[] */ static $maxmanaDCs       = array();
	/* @var Deathcounter[] */ static $damageDCs        = array();
	/* @var Deathcounter[] */ static $armorDCs         = array();
	/* @var Deathcounter[] */ static $magicresistDCs   = array();
	
	/* @var Deathcounter[] */ static $xDCs             = array();
	/* @var Deathcounter[] */ static $yDCs             = array();
	
	
	/* @var BSUnit[][] */ static $dcgroups;
	
	function __construct(){
		
		$this->Setup();
	}
	
	function Setup(){
		
		self::$dcgroups = array(
			
			array(
				self::$Roamers[] = new Roamer(P2, 1),
				self::$Roamers[] = new Roamer(P3, 2),
				self::$Heroes[]  =   new Hero(P4, 3),
				self::$Heroes[]  =   new Hero(P5, 4),
				self::$Heroes[]  =   new Hero(P6, 5),
				self::$Roamers[] = new Roamer(P7, 6),
				self::$Enemies[] =  new Enemy(P8, 7),
			),
			
			array(
				self::$Enemies[] =  new Enemy(P2, 8), 
				self::$Enemies[] =  new Enemy(P3, 9),
				# self::$Enemies[] =  new Enemy(P4, 10), // formerly merc
				# self::$Enemies[] =  new Enemy(P5, 11), // formerly merc
				# self::$Enemies[] =  new Enemy(P6, 12), // formerly merc
				self::$Enemies[] =  new Enemy(P7, 13),
				self::$Enemies[] =  new Enemy(P8, 14),
			),
			
			array(
				self::$Enemies[] =  new Enemy(P2, 15),
				self::$Enemies[] =  new Enemy(P3, 16),
				# self::$Bosses[]  =   new Boss(P4, 17),
				# self::$Bosses[]  =   new Boss(P5, 18),
				# self::$Bosses[]  =   new Boss(P6, 19),
				self::$Enemies[] =  new Enemy(P7, 20),
				self::$Enemies[] =  new Enemy(P8, 21),
			),
			/**/
			array(
				self::$Enemies[] =  new Enemy(P2, 22), 
				self::$Enemies[] =  new Enemy(P3, 23),
				# self::$Enemies[] =  new Enemy(P4, 24),
				# self::$Enemies[] =  new Enemy(P5, 25),
				# self::$Enemies[] =  new Enemy(P6, 26),
				self::$Enemies[] =  new Enemy(P7, 27),
				self::$Enemies[] =  new Enemy(P8, 28),
			),
			/**
			array(
				self::$Enemies[] =  new Enemy(P2, 29), 
				self::$Bosses[] =    new Boss(P3, 30),
				# self::$Enemies[] =  new Enemy(P4, 31),
				# self::$Enemies[] =  new Enemy(P5, 32),
				# self::$Enemies[] =  new Enemy(P6, 33),
				self::$Bosses[] =    new Boss(P7, 34),
				self::$Bosses[] =    new Boss(P8, 35),
			),
			/**/
		);
		
		
		// Assign each BSUnit their deathcounters for the proper players
		$owners = new Player(P2, P3, P4, P5, P6, P7, P8);
		$index = 0;
		foreach(self::$dcgroups as $group){
			if($index > 0){
				$owners = new Player(P2, P3, P7, P8);
			}
			$index++;
			/* @var BSUnit[] $group */
			self::$typeDCs[] =          $type =         new Deathcounter($owners, 15);
			self::$baseidDCs[] =        $baseidDCs =    new Deathcounter($owners, 228);
			self::$apparentidDCs[] =    $apparentid =   new Deathcounter($owners, 228);
			self::$attackTimeDCs[] =    $attackTime =   new Deathcounter($owners, 15);
			self::$attackTargetDCs[] =  $attackTarget = new Deathcounter($owners, 21);
			self::$healthDCs[] =        $health =       new Deathcounter($owners, 127);
			self::$maxhealthDCs[] =     $maxhealth =    new Deathcounter($owners, 127);
			self::$manaDCs[] =          $mana =         new Deathcounter($owners, 127);
			self::$maxmanaDCs[] =       $maxmana =      new Deathcounter($owners, 127);
			self::$damageDCs[] =        $damage =       new Deathcounter($owners, 127);
			self::$armorDCs[] =         $armor =        new Deathcounter($owners, 127);
			self::$magicresistDCs[] =   $magicresist =  new Deathcounter($owners, 127);
			self::$xDCs[] =             $x =            new Deathcounter($owners, Map::getWidth()*32);
			self::$yDCs[] =             $y =            new Deathcounter($owners, Map::getHeight()*32);
			
			foreach($group as $bsunit){
				$bsunit->type =             $type->{$bsunit->dcplayer};
				$bsunit->baseunitid =       $baseidDCs->{$bsunit->dcplayer};
				$bsunit->apparentunitid =   $apparentid->{$bsunit->dcplayer};
				$bsunit->attackTime =       $attackTime->{$bsunit->dcplayer};
				$bsunit->attackTarget =     $attackTarget->{$bsunit->dcplayer};
				$bsunit->health =           $health->{$bsunit->dcplayer};
				$bsunit->maxhealth =        $maxhealth->{$bsunit->dcplayer};
				$bsunit->mana =             $mana->{$bsunit->dcplayer};
				$bsunit->maxmana =          $maxmana->{$bsunit->dcplayer};
				$bsunit->damage =           $damage->{$bsunit->dcplayer};
				$bsunit->armor =            $armor->{$bsunit->dcplayer};
				$bsunit->magicresist =      $magicresist->{$bsunit->dcplayer};
				$bsunit->x =                $x->{$bsunit->dcplayer};
				$bsunit->y =                $y->{$bsunit->dcplayer};
				
			}
		}
		
		// TESTING
		$P1 = new Player(P1);
		
		/* @var BSUnit $hero1 */ $hero1 = self::$Heroes[0];
		/* @var BSUnit $hero2 */ $hero2 = self::$Heroes[1];
		/* @var BSUnit $hero3 */ $hero3 = self::$Heroes[2];
		
		$setTypes = '';
		for($i=0; $i<count(self::$dcgroups); $i++){
			$setTypes .= self::$typeDCs[$i]->All->setTo(1);
		}
		
		$P1->justonce(
			SetAlly(AllPlayers),
			$setTypes,
			$hero1->damage->setTo(20),
			$hero2->damage->setTo(20),
			$hero3->damage->setTo(20),
			self::$typeDCs[0]->leaderboard(),
		'');
		
	}
	
	function CreateEngine(){
		
		$P1 = new Player(P1);
		
		/**/
		foreach(self::getBSUnits() as $bsunit){
			
			$P1->_if( $bsunit->type->atLeast(2) )->then(
				$bsunit->scanUnit(),
				$bsunit->showHealth(),
			'');
			
			$P1->_if( $bsunit->swings() )->then(
				$bsunit->dealDamageToTarget(),
			'');
		}
		/**/
		
	}
	
	////
	// Auxillary
	//
	
	static function setAllyByTarget(Deathcounter $attackTarget, Deathcounter $dcgroupid){
		$text = repeat(1,
			SetEnemy(AllPlayers),
			$dcgroupid->setTo(0),
			
			_if( $attackTarget->atLeast(29) )->then( 
				$attackTarget->subtract(28), 
				$dcgroupid->add(4),
			''),
			_if( $attackTarget->atLeast(22) )->then( 
				$attackTarget->subtract(21), 
				$dcgroupid->add(3),
			''),
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