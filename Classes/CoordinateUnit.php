<?php


class CoordinateUnit extends IndexedUnit{
	
	/* @var Integer */ public $x;
	/* @var Integer */ public $y;
	
	function __construct($index, $x, $y, $unit=NULL, $player=NULL, $location=NULL){
		if( !(is_int($index) && is_int($x) && is_int($y)) ){
			Error("\$index, \$x and \$y parameters must be integers");
		}
		
		parent::__construct($index, $unit, $player, $location);
		
		$this->x = $x;
		$this->y = $y;
	}
	
}