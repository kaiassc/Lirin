<?php
	
	////
	// Actions
	//
	
	//GET SCREEN !
	/*
		� Returns the screen's x and y coordinates to $x and $y, respectively
		� $xdim and $ydim are constants (integers) representing the dimensions of the map in tiles (eg. $xdim = 256, $ydim = 128 would be 256x128)
		� NOTE: LOCAL EUD, CAN'T USE IN MULTIPLAYER MAPS
		� NOTE: origin is top left of the map
		� Format:
			- GetScreen($x, $y, $xdim, $ydim) is analogous to $x = screen_x and $y = screen_y
			- $x must be a deathcounter
			- $y must be a deathcounter
			- $xdim must be a constant (integer)
			- $ydim must be a constant (integer)
		� Max values:
			- returned to $x: 1408 for $xdim=64, 7552 for $xdim=256
			- returned to $y: 1672 for $ydim=64, 7816 for $ydim=256
		�Specifics:
			- trigger number: 389 for 64x64, 1925 for 256x256
			- temp switch number: 1
			- temp deathcounters: 0
	*/
	function GetScreenLessDef($x, $y, $xdim, $ydim) {
		// Error
		if(func_num_args() != 4){
			Error('Incorrect number of arguments (needs 4: deathcounter, deathcounter, integer, integer)');
		}
		if(!($x instanceof Deathcounter)){
			Error('$x argument must be a deathcounter');
		}
		if(!($y instanceof Deathcounter)){
			Error('$y argument must be a deathcounter');
		}
		if(!(is_numeric($xdim))){
			Error('$xdim argument must be an integer');
		}
		if(!(is_numeric($ydim))){
			Error('$ydim argument must be an integer');
		}

		$screenx = new EPD(161849);
		$screeny = new EPD(161859);

        $text = '';

		for($i=0; $i<=$xdim*4-80; $i+=4) {
			$text .= _if( $screenx->between($i*8, ($i+4)*8-1) )->then(
				$x->setTo($i*8),
			'');
		}
		for($i=0; $i<=$ydim*4-47; $i+=4) {
			$text .= _if( $screeny->between($i*8, ($i+4)*8-1) )->then(
				$y->setTo($i*8),
			'');
		}

		return $text;

	}
	
	
	
	
?>