<?php




class DCArray implements ArrayAccess{
	
	/* @var Deathcounter[] */ private $dccontainer;
	
	function __construct($dcs){
		
		foreach(func_get_args() as $dc){
			if($dc instanceof Deathcounter){
				$this->dccontainer[] = $dc;
			}
			elseif(is_array($dc)){
				foreach($dc as $element){
					$this->dccontainer[] = $element;
				}
			}
			else {
				Error("Expecting a deathcounter");
			}
		}
		
	}
	
	
	public function offsetSet($offset, $value) {
		if( !($value instanceof Deathcounter) ){
			Error("You can only add Deathcounters to the DCArray");
		}
		
        if (is_null($offset)) {
            $this->dccontainer[] = $value;
        } else {
            $this->dccontainer[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->dccontainer[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->dccontainer[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->dccontainer[$offset]) ? $this->dccontainer[$offset] : null;
    }
	
	public function countElements(){
		return count($this->dccontainer);
	}
	public function highestBinaryPower(){
		$max = 0;
		foreach($this->dccontainer as $dc){
			$max = max($max, $dc->binaryPower());
		}
		return $max;
	}
	public function lowestBinaryPower(){
		$min = pow(2, 31);
		foreach($this->dccontainer as $dc){
			$min = min($min, $dc->binaryPower());
		}
		return $min;
	}
	
	public function __call($func, $args){
		$text = '';
		
		if(!method_exists(new Deathcounter, $func)){
			Error("Invalid Deathcounter function");
		}
		
		foreach($this->dccontainer as $dc){
			switch(func_num_args()-1){
				case 0:  $text .= $dc->$func(); break;
				case 1:  $text .= $dc->$func($args[0]); break;
				case 2:  $text .= $dc->$func($args[0], $args[1]); break;
				case 3:  $text .= $dc->$func($args[0], $args[1], $args[2]); break;
				case 4:  $text .= $dc->$func($args[0], $args[1], $args[2], $args[3]); break;
				case 5:  $text .= $dc->$func($args[0], $args[1], $args[2], $args[3], $args[4]); break;
				case 6:  $text .= $dc->$func($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]); break;
				case 7:  $text .= $dc->$func($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]); break;
				case 8:  $text .= $dc->$func($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]); break;
				default: $text .= $dc->$func($args); break;
			}
		}
		
		return $text;
	}
	
	public function add($var){
		$text = '';
		if(is_int($var)){
			foreach($this->dccontainer as $dc){
				$text .= $dc->add($var);
			}
			return $text;
		}
		
		// If DCArray passed in, add each dc to the corresponding dc in $var 
		if($var instanceof DCArray){
			foreach($this->dccontainer as $key=>$dc){
				if(!isset($var[$key]) ){
					Error("DCArray \$var argument doesn't have a corresponding key \"$key\", to match");
				}
				$text .= $dc->add($var[$key]);
			}
			return $text;
		}
		
		// If Deathcounter, add the passed in DC to each DC in DCArray
		if($var instanceof Deathcounter){
			
			$tempdc = new TempDC();
			$maxbit = $var->binaryPower();
			
			$text .= $tempdc->setTo(0);
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $var->atLeast($power) )->then(
					$this->add($power),
					$var->subtract($power),
					$tempdc->add($power),
				'');
			}
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $tempdc->atLeast($power) )->then(
					$var->add($power),
					$tempdc->subtract($power),
				'');
			}
			return $text;
		}
		
		Error("Invalid type: \$var must be an integer, Deathcounter or DCArray");
	}
	
	public function addDel($var){
		$text = '';
		
		// If DCArray passed in, add each dc to the corresponding dc in $var 
		if($var instanceof DCArray){
			foreach($this->dccontainer as $key=>$dc){
				if(!isset($var[$key]) ){
					Error("DCArray \$var argument doesn't have a corresponding key \"$key\", to match");
				}
				$text .= $dc->addDel($var[$key]);
			}
			return $text;
		}
		
		// If Deathcounter, add the passed in DC to each DC in DCArray
		if($var instanceof Deathcounter){
			
			$maxbit = $var->binaryPower();
			
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $var->atLeast($power) )->then(
					$this->add($power),
					$var->subtract($power),
				'');
			}
			return $text;
		}
		
		Error("Invalid type: \$var must be a Deathcounter or DCArray");
	}
	
	
	public function subtract($var){
		$text = '';
		if(is_int($var)){
			foreach($this->dccontainer as $dc){
				$text .= $dc->subtract($var);
			}
			return $text;
		}
		
		// If DCArray passed in, add each dc to the corresponding dc in $var 
		if($var instanceof DCArray){
			foreach($this->dccontainer as $key=>$dc){
				if(!isset($var[$key]) ){
					Error("DCArray \$var argument doesn't have a corresponding key \"$key\", to match");
				}
				$text .= $dc->subtract($var[$key]);
			}
			return $text;
		}
		
		// If Deathcounter, subtract the passed in DC to each DC in DCArray
		if($var instanceof Deathcounter){
			
			$tempdc = new TempDC();
			$maxbit = min($var->binaryPower(), $this->highestBinaryPower());
			
			$text .= $tempdc->setTo(0);
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $var->atLeast($power) )->then(
					$this->subtract($power),
					$var->subtract($power),
					$tempdc->add($power),
				'');
			}
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $tempdc->atLeast($power) )->then(
					$var->add($power),
					$tempdc->subtract($power),
				'');
			}
			return $text;
		}
		
		Error("Invalid type: \$var must be an integer, Deathcounter or DCArray");
	}
	public function sub($var){ return $this->subtract($var); }
	
	public function subtractDel($var){
		$text = '';
		
		// If DCArray passed in, add each dc to the corresponding dc in $var 
		if($var instanceof DCArray){
			foreach($this->dccontainer as $key=>$dc){
				if(!isset($var[$key]) ){
					Error("DCArray \$var argument doesn't have a corresponding key \"$key\", to match");
				}
				$text .= $dc->subtractDel($var[$key]);
			}
			return $text;
		}
		
		// If Deathcounter, subtract the passed in DC to each DC in DCArray
		if($var instanceof Deathcounter){
			
			$maxbit = min($var->binaryPower(), $this->highestBinaryPower());
			
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $var->atLeast($power) )->then(
					$this->subtract($power),
					$var->subtract($power),
				'');
			}
			
			return $text;
		}
		
		Error("Invalid type: \$var must be a Deathcounter or DCArray");
	}
	
	
	public function setTo($var){
		$text = '';
		if(is_int($var)){
			foreach($this->dccontainer as $dc){
				$text .= $dc->setTo($var);
			}
			return $text;
		}
		
		// If DCArray passed in, set each dc to the corresponding dc in $var 
		if($var instanceof DCArray){
			foreach($this->dccontainer as $key=>$dc){
				if(!isset($var[$key]) ){
					Error("DCArray \$var argument doesn't have a corresponding key \"$key\", to match");
				}
				$text .= $dc->setTo($var[$key]);
			}
			return $text;
		}
		
		// If Deathcounter, set each DC to the passed in DC
		if($var instanceof Deathcounter){
			
			$tempdc = new TempDC();
			$maxbit = $var->binaryPower();
			
			$text .= $this->setTo(0);
			$text .= $tempdc->setTo(0);
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $var->atLeast($power) )->then(
					$this->add($power),
					$var->subtract($power),
					$tempdc->add($power),
				'');
			}
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $tempdc->atLeast($power) )->then(
					$var->add($power),
					$tempdc->subtract($power),
				'');
			}
			return $text;
		}
		
		Error("Invalid type: \$var must be an integer, Deathcounter or DCArray");
		
	}
	
	public function become($var){
		$text = '';
		
		// If DCArray passed in, set each dc to the corresponding dc in $var 
		if($var instanceof DCArray){
			foreach($this->dccontainer as $key=>$dc){
				if(!isset($var[$key]) ){
					Error("DCArray \$var argument doesn't have a corresponding key \"$key\", to match");
				}
				$text .= $dc->become($var[$key]);
			}
			return $text;
		}
		
		// If Deathcounter, set each DC to the passed in DC
		if($var instanceof Deathcounter){
			
			$maxbit = $var->binaryPower();
			
			$text .= $this->setTo(0);
			for($i=$maxbit; $i>=0; $i--){
				$power = pow(2, $i);
				$text .= _if( $var->atLeast($power) )->then(
					$this->add($power),
					$var->subtract($power),
				'');
			}
			return $text;
		}
		
		Error("Invalid type: \$var must be a Deathcounter or DCArray");
	}
	
	public function countOff(Deathcounter $dc, Deathcounter $store){
		$text = '';
		
		$maxbit = $dc->binaryPower();
		for($i=$maxbit;$i>=0;$i--){
			$power = pow(2,$i);
			$text .= _if( $dc->atLeast($power) )->then(
				$dc->subtract($power),
				$this->subtract($power),
				$store->add($power),
			'');
			
		}
		return $text;
	}
	
	public function countUp(Deathcounter $store, Deathcounter $restoredc = null){
		$text = '';
		
		$maxbit = $store->binaryPower();
		if($restoredc !== null){
			$maxbit = $restoredc->binaryPower();
		}
			
		for($i=$maxbit;$i>=0;$i--){
			$power = pow(2,$i);
			
			$restore = '';
			if($restoredc !== null){
				$restore = $restoredc->add($power);
			}
			
			$text .= _if( $store->atLeast($power) )->then(
				$store->subtract($power),
				$this->add($power),
				$restore,
			'');
			
		}
		
		return $text;
	}
	
	
}