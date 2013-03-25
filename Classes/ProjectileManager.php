<?php


class ProjectileManager {
	
	
	/* @var Projectile[] */
	static public $projectiles = array();
	
	function __construct($num){
		if( !is_numeric($num) ){
			Error('Projectile class takes number of projectiles as an argument');
		}
		
		for($i=0; $i<$num; $i++){
			self::$projectiles[] = new Projectile();
		}
		
	}
	
	
	/////
	// ACTIONS
	///
	
	
	static function engine(){
		$text = '';
		
		foreach(self::$projectiles as $proj){
			$text .= $proj->move();
		}
		foreach(self::$projectiles as $proj){
			$text .= $proj->show();
		}
		foreach(self::$projectiles as $proj){
			$text .= $proj->kill();
		}
		foreach(self::$projectiles as $proj){
			$text .= $proj->tickDown();
		}
		
		return $text;
	}
	
	
	
	/**
	// Old stuff that doesn't belong anymore but still here for reference
	
	//remove lifespan of projectile
	static private function tickDown(){
		$text = '';
		
		for($i=0; $i<self::$num; $i++){
			$text .= self::$duration[$i]->subtract(1);
		}
		
		return $text;
	}
	
	
	// move projectile
	static private function move($duration, $x, $y, $vel, $acc){
		$text = '';
		
		$temp = new TempDC(6400*6400);
		
		//prepare velocity to load in
		$velocity = '';
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$velocity .= _if($vel->atLeast($k*6400))->then(
				$vel->subtract($k*6400),
				$temp->add($k*6400),
				$y->add($k),
			'');
		}
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$velocity .= _if($vel->atLeast($k))->then(
				$vel->subtract($k),
				$temp->add($k),
				$x->add($k),
			'');
		}
		for($i=25; $i>=0; $i--){
			$k = pow(2, $i);
			$velocity .= _if($temp->atLeast($k))->then(
				$vel->add($k),
				$temp->subtract($k),
			'');
		}
		
		//prepare acceleration to load in
		$acceleration = '';
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$acceleration .= _if($acc->atLeast($k*6400))->then(
				$acc->subtract($k*6400),
				$temp->add($k*6400),
				$vel->add($k*6400),
			'');
		}
		for($i=12; $i>=0; $i--){
			$k = pow(2, $i);
			$acceleration .= _if($acc->atLeast($k))->then(
				$acc->subtract($k),
				$temp->add($k),
				$vel->add($k),
			'');
		}
		for($i=25; $i>=0; $i--){
			$k = pow(2, $i);
			$acceleration .= _if($temp->atLeast($k))->then(
				$acc->add($k),
				$temp->subtract($k),
			'');
		}
		
		//add velocity, acceleration, and make the changes
		$text .= _if( $duration->atLeast(1) )->then(
			$velocity,
			$acceleration,
			$vel->subtract(3200*6400+3200),
			$x->subtract(3200),
			$y->subtract(3200),
			Display("HERE I AM"),
		'');
		
		$temp->kill();
		
		
		return $text;
	}
	
	
	
	//output
	static private function show($duration, $x, $y){
		$text = '';
		
		$outx = new TempDC(Map::getWidth()*32-1);
		$outy = new TempDC(Map::getHeight()*32-1);
		$success = new TempSwitch();
		
		$P4 = new Player(P4);
		
		//output
		$text .= _if( $duration->atLeast(1) )->then(
			$outx->roundedQuotientOf($x, 100),
			$outy->roundedQuotientOf($y, 100),
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
	static private function kill($duration, $x, $y, $vel, $acc){
		$text = '';
		
		$text .= _if( $duration->exactly(1) )->then(
			$x->setTo(0),
			$y->setTo(0),
			$vel->setTo(0),
			$acc->setTo(0),
			$duration->setTo(0),
		'');
		
		return $text;
	}
	
	/**/
	
	
	
	
}