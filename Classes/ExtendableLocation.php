<?php
class ExtendableLocation extends LirinLocation{
	
	private $maxslide;
	
	public function __construct($name, $maxslide = 64){
		if(!is_int($maxslide)){
			Error("maxslide must be an integer");
		}
		if($maxslide > 512){
			Error("maxslide cannot be greater than 512 pixels");
		}

		$this->maxslide = $maxslide;

		// create the required extended units
		for($i=1;$i<=$maxslide*2;$i++){
			//UnitManager::MintUnit(1049, P2, (Map::getWidth()/6+$i)*32, (Map::getHeight()-8)*32);
			UnitManager::MintUnit(1049, P2, (36+$i)*32, (106)*32);
		}
		
		parent::__construct($name);
		
	}
	
	
	////
	//ACTIONS
	//
	
	public function extendRight($n){
		if( is_int($n) && $n > $this->maxslide*2 ){ Error('You cannot extend that far (increase maxslide)'); }
		return Give(P2, 1049, $n, P3, Anywhere);
	}
	
	public function extendDown($n){
		if( is_int($n) && $n > $this->maxslide*2 ){ Error('You cannot extend that far (increase maxslide)'); }
		return Give(P2, 1049, $n, P4, Anywhere);
	}
	
	public function retract(){
		return Give(AllPlayers, 1049, All, P2, Anywhere);
	}
	
	public function slideRight($n){
		if( is_int($n) && $n > $this->maxslide ){ Error('You cannot slide that far (increase maxslide)'); }
		$holdpos = Loc::$aoe1x1;
		
		return 
		$this->extendRight($n*2).
		$this->acquire($holdpos).
		$this->retract().
		$this->centerOn($holdpos);
	}
	
	public function slideDown($n){
		if( is_int($n) && $n > $this->maxslide ){ Error('You cannot slide that far (increase maxslide)'); }
		$holdpos = Loc::$aoe1x1;
		
		return 
		$this->extendDown($n*2).
		$this->acquire($holdpos).
		$this->retract().
		$this->centerOn($holdpos);
	}
	
	public function slideLeft($n){
		if( is_int($n) && $n > $this->maxslide ){ Error('You cannot slide that far (increase maxslide)'); }
		$holdpos = Loc::$aoe1x1;
		
		return
		$this->acquire($holdpos).
		$this->extendRight($n*2).
		$this->centerOn($holdpos).
		$this->retract();
	}
	
	public function slideUp($n){
		if( is_int($n) && $n > $this->maxslide ){ Error('You cannot slide that far (increase maxslide)'); }
		$holdpos = Loc::$aoe1x1;
		
		return
		$this->acquire($holdpos).
		$this->extendDown($n*2).
		$this->centerOn($holdpos).
		$this->retract();
	}
	
}

