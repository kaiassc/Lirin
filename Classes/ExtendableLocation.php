<?php
class ExtendableLocation extends LirinLocation{
	
	// Constructor
	public function __construct($name){
		parent::__construct($name);
		
	}
	
	
	/////
	// ACTIONS
	///
	
	public function extendRight($n){
		if( is_numeric($n) && $n > 128 ){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
		return Give(P2, 1049, $n, P3, Anywhere);
	}
	
	public function extendDown($n){
		if( is_numeric($n) && $n > 128 ){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
		return Give(P2, 1049, $n, P4, Anywhere);
	}
	
	public function retract(){
		return Give(AllPlayers, 1049, All, P2, Anywhere);
	}
	
	public function slideRight($n){
		if( is_numeric($n) && $n > 64 ){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
		global $AoE0x0;
		
		return 
		$this->extendRight($n*2).
		$this->acquire($AoE0x0).
		$this->retract().
		$this->centerOn($AoE0x0);
	}
	
	public function slideDown($n){
		if( is_numeric($n) && $n > 64 ){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
		global $AoE0x0;
		
		return 
		$this->extendDown($n*2).
		$this->acquire($AoE0x0).
		$this->retract().
		$this->centerOn($AoE0x0);
	}
	
	public function slideLeft($n){
		if( is_numeric($n) && $n > 64 ){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
		global $AoE0x0;
		
		return
		$this->acquire($AoE0x0).
		$this->extendRight($n*2).
		$this->centerOn($AoE0x0).
		$this->retract();
	}
	
	public function slideUp($n){
		if( is_numeric($n) && $n > 64 ){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
		global $AoE0x0;
		
		return
		$this->acquire($AoE0x0).
		$this->extendDown($n*2).
		$this->centerOn($AoE0x0).
		$this->retract();
	}
	
	
	
	public function placeAt($x, $y){
		global $main, $shiftleft, $shiftup, $gridorigin, $XLoc, $YLoc, $AoE0x0;
		/* @var ExtendableLocation $main    */
		/* @var LirinLocation   $shiftleft  */
		/* @var LirinLocation   $shiftup    */
		/* @var LirinLocation   $AoE0x0     */
		/* @var LirinLocation   $gridorigin */
		/* @var LirinLocation[] $YLoc       */
		/* @var LirinLocation[] $XLoc       */
		
		$text = '';
		
		if(!((is_numeric($x) && is_numeric($y)) || ($x instanceof Deathcounter && $y instanceof Deathcounter))){
			Error('COMPILER ERROR FOR PLACEAT(): INCORRECT ARGUMENTS, NEED TO BOTH BE CONSTANTS (INTEGERS) OR DEATHCOUNTERS');
			
		}
		
		if(is_numeric($x) && is_numeric($y)){
			if($x > 8191){ Error('COMPILER ERROR FOR PLACEAT(): X COORDINATE MUST BE BETWEEN 0 AND 8191'); }
			if($y > 8191){ Error('COMPILER ERROR FOR PLACEAT(): Y COORDINATE MUST BE BETWEEN 0 AND 8191'); }
			
			//find hemisphere
			if($x < 4096){
				$text .= $shiftleft->centerOn($gridorigin);
				$lastlocation = $shiftleft;
			}
			else{
				//$x = 8192 - $x;
				$lastlocation = $gridorigin;
			}
			if($y < 4096){
				$text .= $shiftup->centerOn($lastlocation);
				$lastlocation = $shiftup;
			}
			else{
				//$y = 8192 - $y;
			}
			
			//place X 64
			if($x > 0){
				$n = (int)ceil($x/64);
				$text .= $XLoc[$n]->centerOn($lastlocation);
				$lastlocation = $XLoc[$n];
				$x = $n*64 - $x;
			}
			
			//place Y 64
			if($y > 0){
				$n = (int)ceil($y/64);
				$text .= $YLoc[$n]->centerOn($lastlocation);
				$lastlocation = $YLoc[$n];
				$y = $n*64 - $y;
			}
			
			//move x pixels
			if($x > 0){
				
				$text .= Give(P2, 1049, $x*2, P3, Anywhere);
				$text .= $main->centerOn($lastlocation);
				$lastlocation = $AoE0x0;
				$text .= Give(P3, 1049, $x*2, P2, Anywhere);
				$text .= $AoE0x0->centerOn($main);
			}
			if($y > 0){
				$text .= Give(P2, 1049, $y*2, P4, Anywhere);
				$text .= $main->centerOn($lastlocation);
				$text .= Give(P4, 1049, $y*2, P2, Anywhere);
				if($this != $main){
					$text .= $this->centerOn($main);
				}
			}   
			
			
			return $text;
		}
		
		
	}
	
}

?>
