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
	
	
	
	
	
	
	
	
?>