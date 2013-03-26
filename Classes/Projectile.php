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
		$this->vel      = new Deathcounter(6400*6401);
		$this->acc      = new Deathcounter(6400*6401);
		$this->duration = new Deathcounter(40/*TODO: change back to 720*/);
		
	}
	
	
	
	
	/////
	// ACTIONS
	///
	
	
	function setPosition($x, $y){
		if(is_numeric($x)){
			if($x > Map::getWidth()*32-1 || $x < 0)
				Error("Error! X needs to be within the bounds of the map");
			$x = round($x*100);
		}
		elseif(!($x instanceof Deathcounter))
			Error("Error! X needs to be a number or a Deathcounter");
		if(is_numeric($y)){
			if($y > Map::getHeight()*32-1 || $y < 0)
				Error("Error! Y needs to be within the bounds of the map");
			$y = round($y*100);
		}
		elseif(!($y instanceof Deathcounter))
			Error("Error! Y needs to be a number or a Deathcounter");
		
		return $this->xpos->setTo($x) . $this->ypos->setTo($y);
	}
	function setVelocity($x, $y){
		if(is_numeric($x)){
			if($x > 32 || $x < -32)
				Error("Error! X needs to be between -32 and 32");
			$x = round(($x+32)*100);
		}
		elseif(!($x instanceof Deathcounter))
			Error("Error! X needs to be a number or a Deathcounter");
		if(is_numeric($y)){
			if($y > 32 || $y < -32)
				Error("Error! Y needs to be between -32 and 32");
			$y = round(($y+32)*100);
		}
		elseif(!($y instanceof Deathcounter))
			Error("Error! Y needs to be a number or a Deathcounter");
		
		if(is_numeric($x) && is_numeric($y)){
			return $this->vel->setTo($y*6401+$x);
		}
		else{
			return $this->vel->productOf($y, 6401).
				$this->vel->add($x);
		}
	}
	function setAcceleration($x, $y){
		if(is_numeric($x)){
			if($x > 32 || $x < -32)
				Error("Error! X needs to be between -32 and 32");
			$x = round(($x+32)*100);
		}
		elseif(!($x instanceof Deathcounter))
			Error("Error! X needs to be a number or a Deathcounter");
		if(is_numeric($y)){
			if($y > 32 || $y < -32)
				Error("Error! Y needs to be between -32 and 32");
			$y = round(($y+32)*100);
		}
		elseif(!($y instanceof Deathcounter))
			Error("Error! Y needs to be a number or a Deathcounter");
		
		if(is_numeric($x) && is_numeric($y)){
			return $this->acc->setTo($y*6401+$x);
		}
		else{
			return $this->vel->productOf($y, 6401).
				$this->vel->add($x);
		}
	}
	function setDuration($n){
		return $this->duration->setTo($n);
	}
	
	
	//remove lifespan of projectile
	function tickDown(){
		return $this->duration->subtract(1);
	}
	
	
	// move projectile
	function move(){
		$tempx = new TempDC(6400);
		$tempy = new TempDC(6400);
		$tempz = new TempDC(6400*6401);
		
		$text = '';
		
		//add velocity, acceleration, and make the changes
		$text .= _if( $this->duration->atLeast(1) )->then(
			$this->velocitycalc($tempy, $tempx),
			$this->accelerationcalc($tempy, $tempx, $tempz),
			$this->velocityrestore($tempy, $tempx),
		'');
		
		$tempx->release();
		$tempy->release();
		$tempz->release();
		
		return $text;
	}
	
	private function velocitycalc(TempDC $tempy, TempDC $tempx){
		
		//prepare velocity to load in
		$text = '';
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->vel->atLeast($k*6401))->then(
				$this->vel->subtract($k*6401),
				$tempy->add($k),
				$this->ypos->add($k),
			'');
		}
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->vel->atLeast($k))->then(
				$this->vel->subtract($k),
				$tempx->add($k),
				$this->xpos->add($k),
			'');
		}
		
		return $text;
	}
	private function velocityrestore(TempDC $tempy, TempDC $tempx){
		$text = '';
		
		$text .= $tempx->subtract(3200);
		$text .= $tempy->subtract(3200);
		$text .= _if( $tempx->atLeast(6401) )->then(
			$tempx->setTo(6400),
		'');
		$text .= _if( $tempy->atLeast(6401) )->then(
			$tempy->setTo(6400),
		'');
		
		for($i=25; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($tempy->atLeast($k))->then(
				$this->vel->add($k*6401),
				$tempy->subtract($k),
			'');
		}
		for($i=25; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($tempx->atLeast($k))->then(
				$this->vel->add($k),
				$tempx->subtract($k),
			'');
		}
		
		$text .= $this->xpos->subtract(3200);
		$text .= $this->ypos->subtract(3200);
		
		return $text;
	}
	private function accelerationcalc(TempDC $tempy, TempDC $tempx, TempDC $tempz){
		
		//prepare acceleration to load in
		$text = '';
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->acc->atLeast($k*6401))->then(
				$this->acc->subtract($k*6401),
				$tempz->add($k*6401),
				$tempy->add($k),
			'');
		}
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$text .= _if($this->acc->atLeast($k))->then(
				$this->acc->subtract($k),
				$tempz->add($k),
				$tempx->add($k),
			'');
		}
		$text .= $this->acc->become($tempz);
		
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