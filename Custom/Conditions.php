<?php
	
	////
	// Conditions
	//
	
	/**
	 * DETECT TEXT
	 * 
	 *          1111111112
	 * 1234567890123456790
	 * Lethal_Illusion:7 Whatever I say
	 * 
	 * @param $string string
	 * @param $line int
	 * @param $startingchar int (1-based)
	 * @return string
	 */
	function DetectText($string, $line, $startingchar){
		// Error
		
		if ( $line < 1 || $line > 11 ) {
			Error('Error: $line must be between 1 and 11');
		}
		if ( $startingchar < 1 || $startingchar > 84 ) {
			Error('Error: $startingchar must be between 1 and 84');
		}
		
		global $FullCharCodes;
		
		$base = 0;
		$text = '';
		
		if( $line === 1  ){ $base = 186879; } if( $line === 2  ){ $base = 186933; }
		if( $line === 3  ){ $base = 186988; } if( $line === 4  ){ $base = 187042; }
		if( $line === 5  ){ $base = 187097; } if( $line === 6  ){ $base = 187151; }
		if( $line === 7  ){ $base = 187206; } if( $line === 8  ){ $base = 187260; }
		if( $line === 9  ){ $base = 187315; } if( $line === 10 ){ $base = 187369; }
		if( $line === 11 ){ $base = 187424; }
		
		//$text .= $successSwitch->set();
		
		$startblock = (int)floor($startingchar / 4);
		$subindex = $startingchar % 4;
		
		/*// each block
		for($i=$startblock; $i<=$endblock; $i++){
			$block = $i;
			$epd = new EPD($base + $block - 1);
			
			$text .= $successcheck->clear();
			
			// find characater
			foreach($FullCharCodes as $char=>$info){
				$index = $info['index'];
				$value = $info['value'];
				
				// check each possible value
				$text .= _if( $successSwitch, $epd->between( pow(2,24)*$index, pow(2,24)*($index+1)-1 ) )->then(
					$this->addBits($i*6, $value),
					$successcheck->set(),
				'');
			}
			
			$text .= _if( $successcheck->is_clear() )->then(
				$this->clearStorage(),
				$successSwitch->clear(),
			'');
		}
		*/
		
	}

	/**
	 * If the player playing the game is the player running 
	 * the trigger then it evaluates as TRUE. Uses EPDs.
	 * 
	 * @return string
	 */
	function IsCurrentPlayer(){
		static $initialized = false;
		static $IsCurrentPlayer;
		if($initialized === false){
			$initialized = true;
			$P1 = new Player(P1); $P2 = new Player(P2); $P3 = new Player(P3); $P4 = new Player(P4);
			$P5 = new Player(P5); $P6 = new Player(P6); $P7 = new Player(P7); $P8 = new Player(P8);
			$All = new Player(AllPlayers);
			$IsCurrentPlayer = new PermSwitch();
			$CP = new EPD(-122682);
			
			$All->prepend->always(
				$IsCurrentPlayer->clear(),
			'');
			
			$P1->prepend->_if( $CP->exactly(0) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P2->prepend->_if( $CP->exactly(1) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P3->prepend->_if( $CP->exactly(2) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P4->prepend->_if( $CP->exactly(3) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P5->prepend->_if( $CP->exactly(4) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P6->prepend->_if( $CP->exactly(5) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P7->prepend->_if( $CP->exactly(6) )->then(
				$IsCurrentPlayer->set(),
			'');
			$P8->prepend->_if( $CP->exactly(7) )->then(
				$IsCurrentPlayer->set(),
			'');
		}
		return $IsCurrentPlayer->is_set();
	}
	
	
	
	
	
?>