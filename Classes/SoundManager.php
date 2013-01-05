<?php

class SoundManager {
	
	protected static $instance = null;
	
	private $WavFolder;
	private $WavNames = array();
	
	private $MintedWavs = array();
	private $Minted2DWavs = array();
	
	//private $PlayerCommands = array();
	private $AtCommands = array();
	
	/** @var Deathcounter[] */
	private $AtDCs = array();
	
	
	function __construct($wavfolder){
		if (!is_dir($wavfolder)){ Error('Invalid $wavfolder. Directory does not exist'); }
		
		if( !isset(static::$instance) ){
			static::$instance = $this;
		} else {
			Error("Hey! What're you doing making a second instance of SoundManager?!");
		}
		
		$this->WavFolder = $wavfolder;
		
		/*
		$this->Wavs = array(
			
		);
		/*
		$this->AtCommands = array(
			array($name, $x, $y),
			array($name, $x, $y),
		);
		*/
	}
	
	public static function getInstance(){
		if( isset(static::$instance) ){
			return static::$instance;
		} else {
			Error("You need to make an instance of SoundManager first");
		}
	}
	
	////
	// Engine
	//
	
	public function CreateEngine(){
		
		$text = '';
		
		
		$screenx = new TempDC(Map::$xdim*32);
		$screeny = new TempDC(Map::$ydim*32);
		$tempx = new TempDC(Map::$xdim*32);
		$tempy = new TempDC(Map::$ydim*32);
		
		/** @var $Xdcs Deathcounter[] @var $Ydcs Deathcounter[] */
		list($Xdcs, $Ydcs) = $this->getAtDCs();
		
		$AtSoundPrompted = new TempSwitch();
		foreach($this->AtDCs as $atdc){
			$text .= _if($atdc->atLeast(1) )->then(
				$AtSoundPrompted->set(),
			'');
		}
		
		$text .= _if( $AtSoundPrompted )->then(
			GetScreenLessDef($screenx, $screeny, Map::$xdim, Map::$ydim),
			Display("test1"),
			$this->addToAll($Xdcs, 10000),
			$this->addToAll($Ydcs, 10000),
			Display("test2"),
			$this->countOffDCs($Xdcs, $screenx, $tempx),
			$this->countOffDCs($Ydcs, $screeny, $tempy),
			Display("test3"),
			$this->checkAtCommands(),
			Display("test4"),
			$this->countUpDCs($Xdcs, $tempx),
			$this->countUpDCs($Ydcs, $tempy),
			Display("test5"),
			$this->subFromAll($Xdcs, 10000),
			$this->subFromAll($Ydcs, 10000),
			$screenx->release(),
			$screeny->release(),
			$tempx->release(),
			$tempy->release(),
			$AtSoundPrompted->release(),
		'');
		
		return $text;
	}
	
	////
	// Auxillary
	//
	
	private function getAtDCs(){
		$Xdcs = array();
		$Ydcs = array();
		foreach($this->AtCommands as $command){
			list($name, $x, $y) = $command;
			if( $x instanceof Deathcounter && !in_array($x, $Xdcs) ){
				$Xdcs[] = $x;
			}
			if( $y instanceof Deathcounter && !in_array($y, $Ydcs) ){
				$Ydcs[] = $y;
			}
			if( $name === '' ){ Error("Each command should have a signifier.."); }
		}
		return array($Xdcs, $Ydcs);
	}
	
	private function addToAll($DCArray, $amount){
		$text = '';
		/** @var Deathcounter[] $DCArray */
		foreach($DCArray as $dc){
			$text .= $dc->add($amount);
		}
		return $text;
	}
	
	private function subFromAll($DCArray, $amount){
		$text = '';
		/** @var Deathcounter[] $DCArray */
		foreach($DCArray as $dc){
			$text .= $dc->subtract($amount);
		}
		return $text;
	}
	
	private function countOffDCs($otherdcs, Deathcounter $basedc, Deathcounter $tempdc){
		$text = '';
		
		$maxbit = $basedc->binaryPower();
		for($i=$maxbit;$i>=5;$i--){
			$power = pow(2,$i);
			$text .= _if( $basedc->atLeast($power) )->then(
				$this->subFromAll($otherdcs, $power),
				$basedc->sub($power),
				$tempdc->add($power),
			'');
			
		}
		return $text;
	}
	
	private function countUpDCs($otherdcs, Deathcounter $basedc){
		/** @var Deathcounter[] $otherdcs */
		
		$text = '';
		
		$maxbit = $basedc->binaryPower();
		for($i=$maxbit;$i>=5;$i--){
			$power = pow(2,$i);
			$text .= _if( $basedc->atLeast($power) )->then(
				$this->addToAll($otherdcs, $power),
				$basedc->sub($power),
			'');
			
		}
		return $text;
	}
	
	private function checkAtCommands() {
		$text = '';
		$index = count($this->AtCommands)-1;
		$activated = new TempSwitch();
		foreach(array_reverse($this->AtCommands) as $command){
			list($name, $x, $y) = $command;
			
			$dcindex = (int)floor($index*2 / 30);
			$dcbit = $index*2 % 30;
			$power1 = pow(2,$dcbit);
			$power2 = pow(2,$dcbit+1);
			
			$playorderdc = $this->AtDCs[$dcindex];
			
			
			$text .= _if( $playorderdc->atLeast($power2) )->then(
				$playorderdc->subtract($power2),
				$activated->set(),
			'');
			$text .= _if( $playorderdc->atLeast($power1) )->then(
				$playorderdc->subtract($power1),
				$activated->set(),
			'');
			
			$xepd = new EPD(161849);
			$yepd = new EPD(161859);
			
			$ycondition  = '';
			$xcondCenter = '';
			$xcondRight  = '';
			$xcondLeft   = '';
			
			if($y instanceof Deathcounter){
				$centered = 10000 + 6*32;
				$ycondition = $y->between($centered-15*32, $centered+15*32);
			}
			elseif(is_int($y)){
				$centered = $y-6*32;
				$min = $centered-15*32;
				$max = $y+15*32;
				$ycondition = $yepd->between($min,$max);
			}
			
			if($x instanceof Deathcounter){
				$centered = 10000 + 10*32;
				$xcondCenter = $x->between($centered-5*32, $centered+5*32);
				$xcondRight  = $x->between($centered-15*32, $centered-5*32-1);
				$xcondLeft   = $x->between($centered+5*32+1, $centered+15*32);
			}
			elseif(is_int($y)){
				$centered = $x-10*32;
				$xcondCenter = $xepd->between($centered-5*32,$centered+5*32);
				$xcondRight  = $xepd->between($centered+5*32+1,$centered+15*32);
				$xcondLeft   = $xepd->between($centered-15*32,$centered-5*32-1);
			}
			
			$sound = new Sound("$name");
			$text .= _if( $activated, $ycondition, $xcondCenter )->then(
				$sound->play(),
			'');
			$text .= _if( $activated, $ycondition, $xcondRight )->then(
				PlayWav("$name-R"),
			'');
			$text .= _if( $activated, $ycondition, $xcondLeft )->then(
				PlayWav("$name-L"),
			'');
			
			$text .= $activated->clear();
			
			$index--;
		}
		
		$activated->release();
		
		return $text;
	}
	
	////
	// Commands
	//
	
	public function getPlayerCommand($name, $player){
		$this->mintRegular($name);
		echo $player;
		
	}
	
	public function getPlayAtCommand($name, $x, $y){
		if($x instanceof Deathcounter){
			if($x->Player === CP || $x->Player === Allies || $x->Player === Foes){
				Error('The deathcounter you\'re using for the $x parameter can\'t be for "Current Player", "Allies", or "Foes". This is because the logic for this command may take place in another player\'s triggers');
			}
			if($x instanceof TempDC){
				Error('$x parameter cannot be a TempDC');
			}
		}
		if($y instanceof Deathcounter){
			if($y->Player === CP || $y->Player === Allies || $y->Player === Foes){
				Error('The deathcounter you\'re using for the $y parameter can\'t be for "Current Player", "Allies", or "Foes". This is because the logic for this command may take place in another player\'s triggers');
			}
			if($y instanceof TempDC){
				Error('$x parameter cannot be a TempDC');
			}
		}
		
		// Mint if not already done
		$this->mint2D($name);
		
		// Current command in question
		$command = func_get_args();
		
		// Find index
		$index = array_search($command, $this->AtCommands, true);
		
		// If not found, add it to command list
		if( $index === false ){
			$index = count($this->AtCommands);
			$this->AtCommands[] = $command;
		}
		
		$dcindex = (int)floor($index*2 / 30);
		$dcbit = $index*2 % 30;
		$value = pow(2,$dcbit);
		
		// If there aren't enough deathcounters, make another
		if( $dcindex >= count($this->AtDCs) ){
			$this->AtDCs[] = new Deathcounter();
		}
		
		/*test*/$new = $this->AtDCs[0];
		
		return $this->AtDCs[$dcindex]->add($value).$new->leaderboard();
	}
	
	
	////
	// Register
	//
	
	public function mintRegular($name){
		if( !in_array($name,$this->MintedWavs, true) ){
			$this->MintedWavs[] = $name;
			$folder = $this->WavFolder;
			MintWav("$folder/$name.wav", $name);
		}
	}
	
	public function mint2D($name){
		if( !in_array($name,$this->Minted2DWavs, true) ){
			$this->Minted2DWavs[] = $name;
			$folder = $this->WavFolder;
			
			// If the 2D files don't exist, then make them
			$mainpath = "$folder/$name.wav";
			$filepath = "$folder/2D/$name-L.wav";
			if ( !file_exists($filepath)){
				if( file_exists($mainpath) ){
					$exec = "$_SERVER[DOCUMENT_ROOT]/Lirin/Wavs/2D/SoX/convert.bat $mainpath";
					system($exec);
					if ( !file_exists($filepath)){
						Error("It seems creating the 2D wavs failed. Exec string: $exec");
					}
				} else {
					Error("I can't create the 2D wavs because I can't find the main wav! Failed path: $mainpath");
				}
			}
			
			MintWav("$folder/2D/$name-L.wav", "$name-L");
			MintWav("$folder/2D/$name-R.wav", "$name-R");
		}
	}
	
	function registerWav($name){
		if( !in_array($name,$this->WavNames, true) ){
			$this->WavNames[] = $name;
		}
	}
	
	////
	// External
	//
	
	static function wavDur($file) {
		$fp = fopen($file, 'r');
		$size_in_bytes = filesize($file);
		fseek($fp, 20);
		$rawheader = fread($fp, 16);
		$header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits', $rawheader);
		$sec = $size_in_bytes/$header['bytespersec'];
		return $sec;
	}
	
}