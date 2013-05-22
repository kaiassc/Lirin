<?php


class GloreWorm {
	
	public $x;
	public $y;
	
	function __construct($x, $y){
		if( !is_int($x) || !is_int($y) ){
			Error('$x and $y parameters must be integers, silly!');
		}
		
		$this->x = $x;
		$this->y = $y;
		
		GloreManager::registerWorm($this);
		
		
	}
	
	
	
	
	
	
}