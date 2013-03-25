<?php



class Projectile{
	
	/* @var Deathcounter */ public $xpos = array();
	/* @var Deathcounter */ public $ypos = array();
	/* @var Deathcounter */ public $vel = array();
	/* @var Deathcounter */ public $acc = array();
	/* @var Deathcounter */ public $duration = array();
	
	function __construct(){
		
		$this->xpos     = new Deathcounter((Map::getWidth()*32-1)*100);
		$this->ypos     = new Deathcounter((Map::getHeight()*32-1)*100);
		$this->vel      = new Deathcounter(6400*6400);
		$this->acc      = new Deathcounter(6400*6400);
		$this->duration = new Deathcounter(40/*TODO: change back to 720*/);
		
	}
	
	
	
	
	/////
	// ACTIONS
	///
	
	
	function setPosition($x, $y){
		return $this->xpos->setTo($x) . $this->ypos->setTo($y);
	}
	function setVelocity($n){
		return $this->vel->setTo($n);
	}
	function setDuration($n){
		return $this->duration->setTo($n);
	}
	function setAcceleration($n){
		return $this->acc->setTo($n);
	}
	
	
	//remove lifespan of projectile
	function tickDown(){
		return $this->duration->subtract(1);
	}
	
	
	// move projectile
	function move(){
		$temp = new TempDC(6400*6400);
		
		$text = '';
		
		//add velocity, acceleration, and make the changes
		$text .= _if( $this->duration->atLeast(1) )->then(
			$this->velocitycalc($temp),
			$this->accelerationcalc($temp),
			$this->vel->subtract(3200*6400+3200),
			$this->xpos->subtract(3200),
			$this->ypos->subtract(3200),
			$temp->release(),
			Display("Here I am"),
		'');
		
		return $text;
	}
	
	private function velocitycalc(TempDC $temp){
		
		//prepare velocity to load in
		$text = '';
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->vel->atLeast($k*6400))->then(
				$this->vel->subtract($k*6400),
				$temp->add($k*6400),
				$this->ypos->add($k),
			'');
		}
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->vel->atLeast($k))->then(
				$this->vel->subtract($k),
				$temp->add($k),
				$this->xpos->add($k),
			'');
		}
		for($i=25; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($temp->atLeast($k))->then(
				$this->vel->add($k),
				$temp->subtract($k),
			'');
		}
		
		return $text;
	}
	private function accelerationcalc(TempDC $temp){
		
		//prepare acceleration to load in
		$text = '';
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->acc->atLeast($k*6400))->then(
				$this->acc->subtract($k*6400),
				$temp->add($k*6400),
				$this->vel->add($k*6400),
			'');
		}
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->acc->atLeast($k))->then(
				$this->acc->subtract($k),
				$temp->add($k),
				$this->vel->add($k),
			'');
		}
		for($i=25; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($temp->atLeast($k))->then(
				$this->acc->add($k),
				$temp->subtract($k),
			'');
		}
		
		return $text;
	}
	
	
	//output
	function show(){
		$text = '';
		
		$outx = new TempDC(Map::getWidth()*32-1);
		$outy = new TempDC(Map::getHeight()*32-1);
		$success = new TempSwitch();
		
		$P4 = new Player(P4);
		
		//output
		$text .= _if( $this->duration->atLeast(1) )->then(
			$outx->roundedQuotientOf($this->xpos, 100),
			$outy->roundedQuotientOf($this->ypos, 100),
			
			// TODO: Remove test stuff
			$P4->setOre($outx),
			$P4->setGas($outy),
			
			Grid::putMain($outx, $outy, $success),
			$outx->kill(),
			$outy->kill(),
			_if( $success->is_set() )->then(
				$success->kill(),
				Grid::$main->explode(),
			''),
		'');
		
		return $text;
	}
	
	
	//kill projectile
	function kill(){
		$text = '';
		
		$text .= _if( $this->duration->exactly(1) )->then(
			$this->xpos->setTo(0),
			$this->ypos->setTo(0),
			$this->vel->setTo(0),
			$this->acc->setTo(0),
			$this->duration->setTo(0),
		'');
		
		return $text;
	}
	
	
	
}