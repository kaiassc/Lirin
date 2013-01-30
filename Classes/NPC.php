<?php

class NPC extends IndexedUnit{
	
	private $Name;
	
	public $position = array(); 
	private $positiondc; 
	
	
	function __construct($name, $unit, $x, $y){
		$this->Name = $name;
		$this->Unit = $unit;
		$this->Player = P12;
		$this->Index = UnitManager::MintUnitWithAnyIndex($unit, P12, $x, $y);
		
		$this->position["home"] = array($x, $y);
		$this->positiondc = new Deathcounter(30);
		
		$P1 = new Player(P1);
		$P1->justonce(
			$this->positiondc->setTo(2),
		'');
	}
	
	public function walkTo($position){
		$text = '';
		$index = 2;
		$posindex = null;
		foreach($this->position as $posname=>$pos){
			if($position === $posname){
				$posindex = $index;
				$index++;
				continue;
			}
			
			list($x, $y) = $pos;
			$text .= _if( $this->positiondc->exactly($index) )->then(
				Grid::putMainRes($x, $y),
				Loc::$main->acquire(Loc::$aoe1x1),
			'');
			$index++;
		}
		if($posindex === null){
			Error("position not found in position array for NPC");
		}
		
		list($x, $y) = $this->position[$position];
		$text .= Grid::putMainRes($x, $y);
		$text .= $this->moveTo(Loc::$main, Loc::$aoe1x1);
		$text .= $this->positiondc->setTo($posindex);
		
		return $text;
	}
	
}