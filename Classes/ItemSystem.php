<?php


	
class Item {
	
	/* @var Deathcounter */ protected $prefix;
	/* @var Deathcounter */ protected $suffix;
	/* @var Deathcounter */ protected $base;
	
	public $ID;
	public $DCOwner;
	
	function __construct($DCOwner, Deathcounter $basedc = null, Deathcounter $prefixdc = null, Deathcounter $suffixdc = null){
		
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
		
		if(is_string($DCOwner)){
			$this->DCOwner = $DCOwner;
		} else{
			Error("The DCOwner must be a string of the correct player who owns the deathcounters");
		}
		
		$this->ID = rand(0, 10000);
		
	}
	
	function display(){
		
	}
	
	function isAvailable(){
		return $this->prefix->exactly(0) . $this->suffix->exactly(0) . $this->base->exactly(0);
	}
	
	function isInUse(){
		return $this->base->atLeast(1);
	}
	
	function transferInto(Deathcounter $tempbase, Deathcounter $tempprefix, Deathcounter $tempsuffix){
		$text = '';
		$text .= $tempbase->become($this->base);
		$text .= $tempsuffix->become($this->suffix);
		$text .= $tempprefix->become($this->prefix);
		return $text;
	}
	
	function loadFromDCs(Deathcounter $tempbase, Deathcounter $tempprefix, Deathcounter $tempsuffix){
		$text = '';
		$text .= $this->base->become($tempbase);
		$text .= $this->suffix->become($tempsuffix);
		$text .= $this->prefix->become($tempprefix);
		return $text;
	}
	
	function setItem($base, $prefix, $suffix){
		return $this->base->setTo($base) . $this->prefix->setTo($prefix) . $this->suffix->setTo($suffix);
	}
	
}

class WorldItem extends Item {
	
	/* @var Deathcounter */ public $x;
	/* @var Deathcounter */ public $y;
	
	public $DCOwner;
	public $WorldID;
	
	function __construct($DCOwner, Deathcounter $basedc = null, Deathcounter $prefixdc = null, Deathcounter $suffixdc = null, Deathcounter $xdc = null, Deathcounter $ydc = null, $WorldID = null){
		parent::__construct($DCOwner, $basedc, $prefixdc, $suffixdc);
		
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
		
		if(is_int($WorldID)){
			$this->WorldID = $WorldID;
		} elseif($WorldID !== null){
			Error("\$WorldId argument must be an integer");
		}
		
	}
	
}

class InventoryItem extends Item {
	
	public $x;
	public $y;
	
	public $InvID;
	
	function __construct($DCOwner, Deathcounter $basedc = null, Deathcounter $prefixdc = null, Deathcounter $suffixdc = null, $x = 0, $y = 0, $invid = 0){
		parent::__construct($DCOwner, $basedc, $prefixdc, $suffixdc);
		
		if( !is_int($x) || !is_int($y) ){
			Error("\$x and \$y arguments must be integers");
		}
		$this->x = $x;
		$this->y = $y;
		
		$this->InvID = $invid;
	}
	
}


class ItemSystem {
	
	/* @var InventoryItem[] */ public $InvItemsCP = array();
	/* @var InventoryItem[] */ public $InvItemsP4 = array();
	/* @var InventoryItem[] */ public $InvItemsP5 = array();
	/* @var InventoryItem[] */ public $InvItemsP6 = array();
	
	/* @var WorldItem[] */     public $WorldItems = array();
	/* @var Deathcounter[] */  public $WorldXDCs = array();
	/* @var Deathcounter[] */  public $WorldYDCs = array();
	
	/* @var WorldItem[] */     public $WorldAlliedItems = array();
	
	public $NumberOfWorldItems; 
	
	const basemax   = 64;
	const prefixmax = 64;
	const suffixmax = 64;
	
	function __construct(){
		
		#$P4 = new Player(P4); $P5 = new Player(P5); $P6 = new Player(P6);
		$humans = new Player(P4, P5, P6);
		$computers = new Player(P1, P2, P3, P7, P8);
		
		// Create World Item Slots
		$this->NumberOfWorldItems = $max = 10;
		$index = 0;
		$numalliedgroups = (int)ceil($max/5);
		for($i=0; $i<=$numalliedgroups; $i++){
			
			$basedc   = new Deathcounter($computers, self::basemax);
			$prefixdc = new Deathcounter($computers, self::prefixmax);
			$suffixdc = new Deathcounter($computers, self::suffixmax);
			$xdc      = new Deathcounter($computers, Map::getWidth() *32-1);
			$ydc      = new Deathcounter($computers, Map::getHeight()*32-1);
			
			$this->WorldAlliedItems[] = new WorldItem(Allies, $basedc->Allies, $prefixdc->Allies, $suffixdc->Allies);
			
			$players = array(P1, P2, P3, P7, P8);
			foreach($players as $player){
				$p = GetPlayerShorthand($player);
				$index++;
				if($index <= $max){
					$this->WorldXDCs[] = $xdc->$p;
					$this->WorldYDCs[] = $ydc->$p;
					
					$this->WorldItems[] = new WorldItem($player, $basedc->$p, $prefixdc->$p, $suffixdc->$p, $xdc->$p, $ydc->$p, $index);
				}
				
			}
			
		}
		
		// Create Inventory Item Slots
		$max = 8;
		for($i=0; $i<$max; $i++){
			$basedc   = new Deathcounter($humans, self::basemax);
			$prefixdc = new Deathcounter($humans, self::prefixmax);
			$suffixdc = new Deathcounter($humans, self::suffixmax);
			
			$x = 5584 + ($i%2)*64;
			$y = 624 + (int)floor($i/2)*64;
			
			$this->InvItemsCP[] = new InventoryItem(CP, $basedc->CP, $prefixdc->CP, $suffixdc->CP, $x, $y,       $i+1);
			$this->InvItemsP4[] = new InventoryItem(P4, $basedc->P4, $prefixdc->P4, $suffixdc->P4, $x, $y+448*0, $i+1);
			$this->InvItemsP5[] = new InventoryItem(P5, $basedc->P5, $prefixdc->P5, $suffixdc->P5, $x, $y+448*1, $i+1);
			$this->InvItemsP6[] = new InventoryItem(P6, $basedc->P6, $prefixdc->P6, $suffixdc->P6, $x, $y+448*2, $i+1);
		}
		
	}
	
	
	function CreateEngine(){
		
		$P1 = new Player(P1);
		$P4 = new Player(P4); $P5 = new Player(P5); $P6 = new Player(P6);
		$humans = new Player(P4, P5, P6);
		
		$selected = new TempDC(10);
		$targeted = new TempDC(10);
		
		$success = new TempSwitch();
		
		$P1->justonce(
			$this->WorldItems[0]->x->setTo(2192),
			$this->WorldItems[0]->y->setTo(2368),
			$this->WorldItems[0]->setItem(10,24,16),
		'');
		
		// Picking Up item
		$humans->_if( FRAGS::$Fragged->atLeast(1) )->then(
			
			$this->findSelected($selected, $success),
			_if( $success )->then(
				FRAGS::$Fragged->setTo(0),
				
				Display("found selected: $selected"),
				$this->findOpenSlot($targeted, $success),
				
				// Item on ground found, slot to put it in found
				_if( $success )->then(
					Display("found open slot: $targeted"),
					$this->transferWorldItemToInventory($selected, $targeted),
					
					// TODO: make these work
					$this->removeWorldItem($selected),
					$this->createInventoryItemDisplay($targeted),
				''),
				
				// Inventory full
				_if( $success->is_clear() )->then(
					Message("You don't have any space in your inventory!"),
					$selected->setTo(0),
					$targeted->setTo(0),
				''),
				
			''),
			
			$success->release(),
			
			$selected->release(),
			$targeted->release(),
		'');
		
		
	}
	
	private function removeWorldItem(Deathcounter $selected){
		$text = '';
		
		
		return $text;
	}
	
	private function createInventoryItemDisplay(Deathcounter $targeted){
		$text = '';
		
		
		
		return $text;
	}
	
	
	private function findSelected($selected, TempSwitch $success){
		$text = '';
		
		$hero = BattleSystem::$CPHero;
		
		$xdcs = new DCArray($this->WorldXDCs, $hero->x);
		$ydcs = new DCArray($this->WorldYDCs, $hero->y);
		
		$tempx = new TempDC();
		$tempy = new TempDC();
		
		$origin = 32000;
		$text .= repeat(1,
			$success->clear(),
			
			// count down dcs for comparison
			$xdcs->add($origin),
			$xdcs->countOff(FRAGS::$x->CP, $tempx),
			
			$ydcs->add($origin),
			$ydcs->countOff(FRAGS::$y->CP, $tempy),
			
			
			// compare and stuff if hero is within 80 pixels of the frag coordinate
			_if( $hero->x->between($origin-80, $origin+80), $hero->y->between($origin-80, $origin+80) )->then(
				$this->compareItemCoordinates($selected, $origin, $success),
			''),
			
			
			// count up to restore
			$xdcs->countUp($tempx, FRAGS::$x->CP),
			$xdcs->subtract($origin),
			
			$ydcs->countUp($tempy, FRAGS::$y->CP),
			$ydcs->subtract($origin),
			
			$tempx->release(),
			$tempy->release(),
		'');
		
		return $text;
	}
	
	private function compareItemCoordinates(Deathcounter $selected, $origin, TempSwitch $success){
		$range = 16/*px*/;
		
		$text = $success->clear();
		
		foreach($this->WorldItems as $item){
			$text .= _if( 
				$item->x->between($origin-$range, $origin+$range), 
				$item->y->between($origin-$range, $origin+$range), 
				$selected->exactly(0), 
				$item->isInUse() 
			)->then(
				$selected->setTo($item->WorldID),
				$success->set(),
			'');
		}
		return $text;
	}
	
	
	private function findOpenSlot(Deathcounter $targeted, TempSwitch $success){
		$text = $targeted->setTo(0);
		$text .= $success->clear();
		foreach($this->InvItemsCP as $invitem){
			$text .= _if( $invitem->isAvailable(), $success->is_clear()  )->then(
				$targeted->setTo($invitem->InvID),
				$success->set(),
			'');
		}
		
		return $text;
	}
	
	
	private function transferWorldItemToInventory(Deathcounter $selected, Deathcounter $targeted){
		$text = '';
		
		$tempbase   = new TempDC(self::basemax);
		$tempprefix = new TempDC(self::prefixmax);
		$tempsuffix = new TempDC(self::suffixmax);
		
		// set alliances
		$text .= $this->setAllianceByWorldID($selected);
		
		// transfer allied item into tempdcs 
		$index = 1;
		foreach($this->WorldAlliedItems as $item){
			$text .= _if( $selected->between($index, $index+4) )->then(
				$item->transferInto($tempbase, $tempprefix, $tempsuffix),
			'');
			$index += 5;
		}
		
		// restore alliance
		$text .= $this->restoreAlliances();
		
		// transfer tempdcs into inventory slot
		foreach($this->InvItemsCP as $invitem){
			$text .= _if( $targeted->exactly($invitem->InvID) )->then(
				$invitem->loadFromDCs($tempbase, $tempprefix, $tempsuffix),
			'');
		}
		
		$text .= $tempbase->release();
		$text .= $tempprefix->release();
		$text .= $tempsuffix->release();
		
		return $text;
	}
	
	private function setAllianceByWorldID(Deathcounter $selected){
		$text = '';
		
		$tempdc = new TempDC(20);
		
		$high = (int)ceil($this->NumberOfWorldItems/5) - 1;
		
		// expose selected for alliance
		for($i=$high; $i>0; $i--){
			$text .= _if( $selected->atLeast($i*5 + 1) )->then(
				$selected->subtract(5),
				$tempdc->add(5),
			'');
		}
		
		// set ally appropriately
		$text .= repeat(1,
			SetEnemy(AllPlayers),
			
			_if( $selected->exactly(1) )->then(
				SetAlly(P1),
			''),
			_if( $selected->exactly(2) )->then(
				SetAlly(P2),
			''),
			_if( $selected->exactly(3) )->then(
				SetAlly(P3),
			''),
			_if( $selected->exactly(4) )->then(
				SetAlly(P7),
			''),
			_if( $selected->exactly(5) )->then(
				SetAlly(P8),
			''),
		'');
		
		// restore selected
		for($i=$high; $i>0; $i--){
			$text .= _if( $tempdc->atLeast(5) )->then(
				$selected->add(5),
				$tempdc->subtract(5),
			'');
		}
		
		$tempdc->release();
		return $text;
	}
	
	private function restoreAlliances(){
		return SetAlly(AllPlayers) . SetEnemy(P8);
	}
	
}