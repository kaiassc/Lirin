<?php

class Type {
	
	// Enemies
	/* @var EnemyType */    static $Scurrier;
	/* @var EnemyType */    static $Champion;
	/* @var EnemyType */    static $Squirt;
	/* @var EnemyType */    static $GloreHulk;
	
	/* @var EnemyType */    static $Guardian;
	/* @var EnemyType */    static $Sentinel;
	
	/* @var RoamerType */   static $Gusano;
	/* @var RoamerType */   static $Elreu;
	/* @var RoamerType */   static $Brin;
	
	/* @var HeroType */     static $Melee;
	/* @var HeroType */     static $Ranged;
	/* @var HeroType */     static $Caster;
	
	/* @var EnemyType[] */  private static $EnemyTypes;
	/* @var RoamerType[] */ private static $RoamerTypes;
	/* @var HeroType[] */   private static $HeroTypes;
	
	function __construct(){

	}
	
	static function populate(){
		
		self::$EnemyTypes = array(
			self::$Scurrier = new EnemyType(array(
				"name"          => "Scurrier",
				"baseunit"      => "Zerg Zergling",
				"health"        => 21,
				"damage"        => 2,
				"armor"         => 4,
				"magicresist"   => 4,
				"mana"          => 0,
				"codex"         => "approach without caution",
			)),
			
			self::$Champion = new EnemyType(array(
				"name"          => "Champion",
				"baseunit"      => "Protoss Zealot",
				"health"        => 115,
				"damage"        => 20,
				"armor"         => 5,
				"magicresist"   => 5,
				"mana"          => 20,
				"codex"         => "not to be trifled with",
			)),
			
			self::$Squirt = new EnemyType(array(
				"name"          => "Squirt",
				"baseunit"      => "Zerg Hydralisk",
				"health"        => 40,
				"damage"        => 15,
				"armor"         => 0,
				"magicresist"   => 0,
				"mana"          => 0,
				"codex"         => "considering the name, Squirts probably won't make it into the final game",
			)),
			
			self::$GloreHulk = new EnemyType(array(
				"name"          => "Glore Hulk",
				"baseunit"      => "Zerg Ultralisk",
				"health"        => 100,
				"damage"        => 22,
				"armor"         => 30,
				"magicresist"   => 42,
				"mana"          => 15,
				"codex"         => "given the chance, she'll rip your face off and feed it to her glore babies",
			)),
			
		);
		
		self::$RoamerTypes = array(
			self::$Gusano = new RoamerType(array(
				"name"          => "Gusano",
				"baseunit"      => "Jim Raynor (Marine)",
				"health"        => 100,
				"damage"        => 10,
				"armor"         => 10,
				"magicresist"   => 10,
				"mana"          => 50,
			)),
			
			self::$Elreu = new RoamerType(array(
				"name"          => "Elreu",
				"baseunit"      => "Fenix (Zealot)",
				"health"        => 120,
				"damage"        => 20,
				"armor"         => 14,
				"magicresist"   => 20,
				"mana"          => 10,
			)),
			
			self::$Brin = new RoamerType(array(
				"name"          => "Brin",
				"baseunit"      => "Terran Medic",
				"health"        => 70,
				"damage"        => 0,
				"armor"         => 11,
				"magicresist"   => 12,
				"mana"          => 80,
			)),
			
		);
		
		self::$HeroTypes = array(
			self::$Melee = new HeroType(array(
				"baseunit"      => "Protoss Zealot",
			)),
			
			self::$Ranged = new HeroType(array(
				"baseunit"      => "Terran Ghost",
			)),
			
			self::$Caster = new HeroType(array(
				"baseunit"      => "Tassadar (Templar)",
			)),
			
		);
		
		
	}
	
	static function getAllTypes(){
		return array_merge(self::$EnemyTypes, self::$HeroTypes, self::$RoamerTypes);
	}
	static function getEnemyTypes(){
		return self::$EnemyTypes;
	}
	static function getHeroTypes(){
		return self::$HeroTypes;
	}
	static function getRoamerTypes(){
		return self::$RoamerTypes;
	}
	
}