<?php


	
class Item {
	
	/* @var Deathcounter */ private $prefix;
	/* @var Deathcounter */ private $suffix;
	/* @var Deathcounter */ private $base;
	
	function __construct(Deathcounter $basedc = null, Deathcounter $prefixdc = null, Deathcounter $suffixdc = null){
		
		if($basedc === null){
			$this->base = new Deathcounter(ItemSystem::basemax);
		} elseif($basedc instanceof Deathcounter){
			$this->base = $basedc;
		} else{
			Error("\$basedc must be a deathcounter, if anything");
		}
		
		if($prefixdc === null){
			$this->prefix = new Deathcounter(ItemSystem::prefixmax);
		} elseif($prefixdc instanceof Deathcounter){
			$this->prefix = $prefixdc;
		} else{
			Error("\$prefixdc must be a deathcounter, if anything");
		}
		
		if($suffixdc === null){
			$this->suffix = new Deathcounter(ItemSystem::suffixmax);
		} elseif($suffixdc instanceof Deathcounter){
			$this->suffix = $suffixdc;
		} else{
			Error("\$suffixdc must be a deathcounter, if anything");
		}
		
	}
	
	function display(){
		
	}
	
}

class WorldItem extends Item {
	
	/* @var Deathcounter */ public $x;
	/* @var Deathcounter */ public $y;
	
	function __construct(Deathcounter $basedc = null, Deathcounter $prefixdc = null, Deathcounter $suffixdc = null, Deathcounter $xdc = null, Deathcounter $ydc = null){
		parent::__construct($basedc, $prefixdc, $suffixdc);
		
		if($xdc === null){
			$this->x = new Deathcounter(Map::getWidth() *32-1);
		} elseif($xdc instanceof Deathcounter) {
			$this->x = $xdc;
		} else {
			Error("\$xdc must be a deathcounter, if anything");
		}
		
		if($ydc === null){
			$this->y = new Deathcounter(Map::getHeight()*32-1);
		} elseif($ydc instanceof Deathcounter) {
			$this->y = $ydc;
		} else {
			Error("\$ydc must be a deathcounter, if anything");
		}
		
	}
	
	
}


class ItemSystem {
	
	/* @var WorldItem[] */    public $WorldItems = array();
	/* @var Deathcounter[] */ public $WorldXDCs = array();
	/* @var Deathcounter[] */ public $WorldYDCs = array();
	
	const basemax   = 64;
	const prefixmax = 64;
	const suffixmax = 64;
	
	function __construct(){
		
	}
	
	function CreateEngine(){
		
		$P4 = new Player(P4); $P5 = new Player(P5); $P6 = new Player(P6);
		$humans = new Player(P4, P5, P6);
		
		$fragged = new TempSwitch();
		$P4->_if( FRAGS::$P4Fragged )->then( $fragged->set() );
		$P5->_if( FRAGS::$P5Fragged )->then( $fragged->set() );
		$P6->_if( FRAGS::$P6Fragged )->then( $fragged->set() );
		
		$max = 10;
		for($i=0; $i<$max; $i++){
			$this->WorldXDCs[] = new Deathcounter(Map::getWidth() *32-1);
			$this->WorldYDCs[] = new Deathcounter(Map::getHeight()*32-1);
			$this->WorldItems[] = new WorldItem();
		}
		
		$xdcs = new DCArray($this->WorldXDCs);
		$ydcs = new DCArray($this->WorldYDCs);
		
		$tempx = new TempDC();
		$tempy = new TempDC();
		
		$origin = 32000;
		$humans->_if( $fragged )->then(
			$fragged->release(),
			
			// count down dcs for comparison
			$xdcs->add($origin),
			$xdcs->countOff(FRAGS::$x->CP, $tempx),
			
			$ydcs->add($origin),
			$ydcs->countOff(FRAGS::$y->CP, $tempy),
			
			
			// compare and stuff 
			
			
			// count up to restore
			$xdcs->countUp($tempx, FRAGS::$x->CP),
			$xdcs->subtract($origin),
			
			$ydcs->countUp($tempy, FRAGS::$y->CP),
			$ydcs->subtract($origin),
			
		'');
		
		
	}
	
	
}