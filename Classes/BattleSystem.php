<?php require_once("BS/BSUnit.php"); require_once("BS/Boss.php"); require_once("BS/Enemy.php"); require_once("BS/Hero.php"); require_once("BS/Roamer.php"); 


class BattleSystem {

	/* @var Hero[] */   static private $Heroes  = array();
	/* @var BSUnit[] */ static private $Enemies = array();
	/* @var BSUnit[] */ static private $Roamers = array();
	/* @var BSUnit[] */ static private $Bosses  = array();
	
	/* @var Deathcounter[] */ private $typeDCs          = array();
	/* @var Deathcounter[] */ private $attackTimeDCs    = array();
	/* @var Deathcounter[] */ private $attackTargetDCs  = array();
	/* @var Deathcounter[] */ private $healthDCs        = array();
	/* @var Deathcounter[] */ private $maxhealthDCs     = array();
	/* @var Deathcounter[] */ private $manaDCs          = array();
	/* @var Deathcounter[] */ private $damageDCs        = array();
	/* @var Deathcounter[] */ private $armorDCs         = array();
	
	/* @var Deathcounter[] */ private $xDCs             = array();
	/* @var Deathcounter[] */ private $yDCs             = array();
	
	
	/* @var BSUnit[][] */ private $dcgroups;
	
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
				//
				//
				//
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
			$this->typeDCs[] =         $type =         new Deathcounter($All, 15);
			$this->attackTimeDCs[] =   $attackTime =   new Deathcounter($All, 15);
			$this->attackTargetDCs[] = $attackTarget = new Deathcounter($All, 18);
			$this->healthDCs[] =       $health =       new Deathcounter($All, 127);
			$this->maxhealthDCs[] =    $maxhealth =    new Deathcounter($All, 127); 
			$this->manaDCs[] =         $mana =         new Deathcounter($All, 127);
			$this->damageDCs[] =       $damage =       new Deathcounter($All, 127); 
			$this->armorDCs[] =        $armor =        new Deathcounter($All, 127);
			$this->xDCs[] =            $x =            new Deathcounter($All, Map::getWidth()*32);
			$this->yDCs[] =            $y =            new Deathcounter($All, Map::getHeight()*32);
			
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
		
	}
	
	function CreateEngine(){
		
		$P1 = new Player(P1);
		
		foreach(self::getBSUnits() as $bsunit){
			
			$P1->_if( $bsunit->swings() )->then(
				$this->dealDamage($bsunit->damage, $bsunit->attackTarget),
			'');
		}
		
		
	}
	
	////
	// Auxillary
	//
	
	public function dealDamage(Deathcounter $damage, Deathcounter $attackTarget){
		
		$dcgroupid = new TempDC(count($this->dcgroups));
		$tempdc = new TempDC(127);
		
		$text = repeat(1,
			
			// set ally
			$this->setAllyByTarget($attackTarget, $dcgroupid),
			
			// load armor
			_if( $dcgroupid->exactly(0) )->then( $tempdc->setTo($this->armorDCs[0]->Allies) ),
			_if( $dcgroupid->exactly(1) )->then( $tempdc->setTo($this->armorDCs[1]->Allies) ),
			_if( $dcgroupid->exactly(2) )->then( $tempdc->setTo($this->armorDCs[2]->Allies) ),
			
			// armor calculation
			$this->convertToVulnerability($tempdc),
			$tempdc->multiplyBy($damage),
			$tempdc->max(12700),
			_if( $tempdc->atMost(50) )->then( $tempdc->setTo(50) ),
			
			//deal damage
			_if( $dcgroupid->exactly(0) )->then( $this->healthDCs[0]->Allies->subDivBecome($tempdc, 100) ),
			_if( $dcgroupid->exactly(1) )->then( $this->healthDCs[1]->Allies->subDivBecome($tempdc, 100) ),
			_if( $dcgroupid->exactly(2) )->then( $this->healthDCs[2]->Allies->subDivBecome($tempdc, 100) ),
			$tempdc->max(100),
			
			//restore
			SetAlly(AllPlayers),
			$tempdc->release(),
			$dcgroupid->release(),
			$attackTarget->setTo(0),
		'');
		
		return $text;
	}
	
	
	private function setAllyByTarget(Deathcounter $attackTarget, Deathcounter $dcgroupid){
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
	
	
	private function convertToVulnerability(Deathcounter $armor){
		$notyetfound = new TempSwitch();
		$text = $notyetfound->set();
		
		$lastvalue = null;
		
		for($i=1; $i<=100; $i++){
			$neededvalue = (int)round((100/$i-1)/0.06);
			
			// if the armor you need to get the percent is too large or the same amount of armor as the last percent, then move on 
			if( $neededvalue > $this->armorDCs[0]->Max || $lastvalue === $neededvalue ){ continue; }
			
			$text .= _if( $armor->atLeast($neededvalue), $notyetfound )->then(
				$armor->setTo($i),
				$notyetfound->clear(),
			'');
			
			$lastvalue = $neededvalue;
		}
		
		$text .= _if( $notyetfound )->then( $armor->setTo(100), $notyetfound->release() );
		
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