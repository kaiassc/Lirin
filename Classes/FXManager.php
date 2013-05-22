<?php

class FXManager {
	
	/* @var FXManager */
	protected static $instance = null;
	
	// Wav Minting and Management
	private $WavFolder;
	private $WavNames = array();
	private $MintedWavs = array();
	private $Minted2DWavs = array();
	
	private $AtCommands = array();
	private $AtRumbles = array();
	
	/* @var Deathcounter[] */ private $AtDCs = array();
	/* @var Deathcounter[] */ private $RumbleDCs = array();
	
	/* @var Deathcounter */   public $RumbleLevel;
	/* @var Deathcounter */   public $DrunkStage;
	/* @var Deathcounter */   public $DrunkTimer;
	
	/* @var Deathcounter */   private static $ScreenX;
	/* @var Deathcounter */   private static $ScreenY;
	/* @var Deathcounter */   private static $LastOffsetUp;
	/* @var Deathcounter */   private static $LastOffsetDown;
	/* @var Deathcounter */   private static $LastOffsetLeft;
	/* @var Deathcounter */   private static $LastOffsetRight;
	
	/* @var Permswitch[] */   private static $RSwitches = array();
	
	
	function __construct($wavfolder){
		if (!is_dir($wavfolder)){ Error('Invalid $wavfolder. Directory does not exist'); }
		
		if( !isset(static::$instance) ){
			static::$instance = $this;
		} else {
			Error("Hey! What're you doing making a second instance of SoundManager?!");
		}
		
		$this->WavFolder = $wavfolder;
		
		$this->RumbleLevel = new Deathcounter(1000);
		$this->DrunkStage = new Deathcounter(100);
		$this->DrunkTimer = new Deathcounter(1000);
		self::$ScreenX = new Deathcounter(Map::getWidth()*32);
		self::$ScreenY = new Deathcounter(Map::getHeight()*32);
		
		self::$LastOffsetUp     = new Deathcounter(127);
		self::$LastOffsetDown   = new Deathcounter(127);
		self::$LastOffsetLeft   = new Deathcounter(127);
		self::$LastOffsetRight  = new Deathcounter(127);
		
		$randomizeswitches = '';
		for($i=0; $i<=7; $i++){
			self::$RSwitches[$i] = new PermSwitch();
			$randomizeswitches .= self::$RSwitches[$i]->randomize();
		}
		
		$P1 = new Player(P1);
		
		$P1->always(
			GetScreen(self::$ScreenX, self::$ScreenY, Map::getWidth(), Map::getHeight()),
			$randomizeswitches,
		'');
		
	}
	
	public static function getInstance(){
		if( isset(static::$instance) ){
			return static::$instance;
		} else {
			Error("You need to make an instance of SoundManager first");
			return false;
		}
	}
	
	////
	// Engine
	//
	
	public function CreateEngine(){
		
		$text = '';
		
		$tempx   = new TempDC(Map::getWidth()*32);
		$tempy   = new TempDC(Map::getHeight()*32);
		
		/** @var $Xdcs Deathcounter[] @var $Ydcs Deathcounter[] */
		list($Xdcs, $Ydcs) = $this->getAtDCs();
		
		$AtPrompted = new TempSwitch();
		foreach(array_merge($this->AtDCs, $this->RumbleDCs) as $dc){
			/* @var Deathcounter $dc */
			$text .= _if($dc->atLeast(1) )->then( $AtPrompted->set(), '');
		}
		
		$XdcsAndScreen = $Xdcs;
		$XdcsAndScreen[] = self::$ScreenX;
		$YdcsAndScreen = $Ydcs;
		$YdcsAndScreen[] = self::$ScreenY;
		
		
		//Error(var_export($this->AtRumbles, true)); // TODO:remove
		
		$text .= _if( $AtPrompted )->then(
			
			// get ready for comparison
			$this->addToAll($Xdcs, 10000),
			$this->addToAll($Ydcs, 10000),
			$this->countOffDCs($Xdcs, self::$ScreenX, $tempx),
			$this->countOffDCs($Ydcs, self::$ScreenY, $tempy),
			
			// check commands
			$this->checkAtCommands(),
			$this->checkAtRumbles(),
			
			// restore
			$this->countUpDCs($XdcsAndScreen, $tempx),
			$this->countUpDCs($YdcsAndScreen, $tempy),
			$this->subFromAll($Xdcs, 10000),
			$this->subFromAll($Ydcs, 10000),
			$tempx->release(),
			$tempy->release(),
			$AtPrompted->release(),
		'');
		
		$offsetright    = new TempDC(127);
		$offsetleft     = new TempDC(127);
		$offsetup       = new TempDC(127);
		$offsetdown     = new TempDC(127);
		
		$rumble = $this->RumbleLevel;
		
		$centerview = new TempSwitch();
		$loadoffset = new TempSwitch();
		
		$text .= _if( self::$LastOffsetRight->atLeast(1) )->then(
			self::$ScreenX->subtractDel(self::$LastOffsetRight),
			$centerview->set(),
		'');
		$text .= _if( self::$LastOffsetLeft->atLeast(1) )->then(
			self::$ScreenX->addDel(self::$LastOffsetLeft),
			$centerview->set(),
		'');
		$text .= _if( self::$LastOffsetUp->atLeast(1) )->then(
			self::$ScreenY->addDel(self::$LastOffsetUp),
			$centerview->set(),
		'');
		$text .= _if( self::$LastOffsetDown->atLeast(1) )->then(
			self::$ScreenY->subtractDel(self::$LastOffsetDown),
			$centerview->set(),
		'');
		
		$text .= _if( $rumble->atLeast(1) )->then(
			
			_if(self::$RSwitches[0], $rumble->atMost(25))->then(
				$offsetleft->add(32),
				$offsetright->add(32),
			''),
			_if(self::$RSwitches[1])->then(
				$offsetleft->add(16),
				$offsetright->add(16),
			''),
			_if(self::$RSwitches[2])->then(
				$offsetleft->add(8),
				$offsetright->add(8),
			''),
			
			_if(self::$RSwitches[6])->then(
				$offsetleft->setTo(0),
			''),
			_if(self::$RSwitches[6]->is_clear())->then(
				$offsetright->setTo(0),
			''),
			
			_if(self::$RSwitches[3], $rumble->atMost(25))->then(
				$offsetup->add(32),
				$offsetdown->add(32),
			''),
			_if(self::$RSwitches[4])->then(
				$offsetup->add(16),
				$offsetdown->add(16),
			''),
			_if(self::$RSwitches[5])->then(
				$offsetup->add(8),
				$offsetdown->add(8),
			''),
			
			_if(self::$RSwitches[7])->then(
				$offsetdown->setTo(0),
			''),
			_if(self::$RSwitches[7]->is_clear())->then(
				$offsetup->setTo(0),
			''),
			
			
			$rumble->subtract(15),
			$centerview->set(),
			$loadoffset->set(),
			
		'');
		
		$DrunkTimer = $this->DrunkTimer;
		$DrunkStage = $this->DrunkStage;
		
		$text .= _if( $DrunkTimer->atLeast(1) )->then(
			
			$DrunkTimer->sub(1),
			$DrunkStage->add(1),
			
			_if( $DrunkStage->atLeast(16) )->then(
				$DrunkStage->setTo(0),
			''),
			
			_if( $DrunkStage->atMost(4) )->then(
				$offsetright->add($DrunkStage),
			''),
			_if( $DrunkStage->between(5, 8) )->then(
				$offsetright->add(8),
				$offsetright->subtract($DrunkStage),
			''),
			_if( $DrunkStage->between(9, 12) )->then(
				$offsetleft->add($DrunkStage),
				$offsetleft->subtract(8),
			''),
			_if( $DrunkStage->atLeast(13) )->then(
				$offsetleft->add(16),
				$offsetleft->subtract($DrunkStage),
			''),
			
			$offsetleft->multiplyBy(8),
			$offsetright->multiplyBy(8),
			$centerview->set(),
			$loadoffset->set(),
			
		'');
		
		$success = new TempSwitch();
		
		$text .= _if( $centerview )->then(
			
			self::$ScreenX->add($offsetright),
			self::$ScreenX->sub($offsetleft),
			self::$ScreenY->sub($offsetup),
			self::$ScreenY->add($offsetdown),
			
			self::$ScreenX->add(10*32),
			self::$ScreenY->add(200),
			
			// Adjust for bottom half of map
			_if(self::$ScreenY->atLeast(Map::getHeight()*32/2-8))->then(
				self::$ScreenY->add(8),
			''),
			
			Grid::putMainRes(self::$ScreenX, self::$ScreenY, $success),
			_if( $success )->then(
				Grid::$main->centerView(),
				//Loc::$main->explode(P8),
			''),
			_if( $success->is_clear() )->then(
				$loadoffset->clear(),
				Display("foop! out of bounds"),
			''),
			
			_if(self::$ScreenY->atLeast(Map::getHeight()*32/2-8))->then(
				self::$ScreenY->subtract(8),
			''),
			
			
			self::$ScreenX->sub(10*32),
			self::$ScreenY->sub(200),
			
			_if( $loadoffset )->then(
				self::$LastOffsetUp->becomeDel($offsetup),
				self::$LastOffsetDown->becomeDel($offsetdown),
				self::$LastOffsetLeft->becomeDel($offsetleft),
				self::$LastOffsetRight->becomeDel($offsetright),
			''),
			
			$centerview->release(),
			$loadoffset->release(),
			$success->release(),
		'');
		
		$text .= $offsetup->release();
		$text .= $offsetdown->release();
		$text .= $offsetright->release();
		$text .= $offsetleft->release();
		
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
		foreach($this->AtRumbles as $rumble){
			list($intensity, $x, $y) = $rumble;
			if( $x instanceof Deathcounter && !in_array($x, $Xdcs) ){
				$Xdcs[] = $x;
			}
			if( $y instanceof Deathcounter && !in_array($y, $Ydcs) ){
				$Ydcs[] = $y;
			}
			if( !is_int($intensity) ){ Error("Each rumble should have an integer intensity value"); }
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
		for($i=$maxbit;$i>=3;$i--){
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
		for($i=$maxbit;$i>=3;$i--){
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
			
			$xepd = new EPD(161849);
			$yepd = new EPD(161859);
			
			$ycondition  = '';
			$xcondCenter = '';
			$xcondRight  = '';
			$xcondLeft   = '';
			
			if($y instanceof Deathcounter){
				$c = 10000 + 6*32;
				$ycondition = $y->between($c-15*32, $c+15*32);
			}
			if(is_int($y)){
				$c = $y-6*32;
				$min = $c-15*32;
				$max = $c+15*32;
				$ycondition = $yepd->between($min,$max);
			}
			
			if($x instanceof Deathcounter){
				$c = 10000 + 10*32;
				$xcondCenter = $x->between($c-5*32,     $c+5*32);
				$xcondRight  = $x->between($c-15*32,    $c-5*32-1);
				$xcondLeft   = $x->between($c+5*32+1,   $c+15*32);
			}
			if(is_int($x)){
				$c = $x-10*32;
				$xcondCenter = $xepd->between($c-5*32,  $c+5*32);
				$xcondRight  = $xepd->between($c+5*32+1,$c+15*32);
				$xcondLeft   = $xepd->between($c-15*32, $c-5*32-1);
			}
			
			$sound = new Sound("$name");
			
			$text .= repeat(1,
				
				// detect if sound was activated
				_if( $playorderdc->atLeast($power2) )->then(
					$playorderdc->subtract($power2),
					$activated->set(),
				''),
				_if( $playorderdc->atLeast($power1) )->then(
					$playorderdc->subtract($power1),
					$activated->set(),
				''),
				
				// play sound based on where it is
				_if( $activated, $ycondition, $xcondCenter )->then(
					$sound->play(),
				''),
				_if( $activated, $ycondition, $xcondRight )->then(
					PlayWav("$name-R"),
				''),
				_if( $activated, $ycondition, $xcondLeft )->then(
					PlayWav("$name-L"),
				''),
				
				$activated->clear(),
			
			'');
			
			$index--;
		}
		
		$activated->release();
		
		return $text;
	}
	
	private function checkAtRumbles() {
		$text = '';
		$index = count($this->AtRumbles)-1;
		$activated = new TempSwitch();
		foreach(array_reverse($this->AtRumbles) as $command){
			list($intensity, $x, $y) = $command;
			
			$dcindex = (int)floor($index*2 / 30);
			$dcbit = $index*2 % 30;
			$power1 = pow(2,$dcbit);
			$power2 = pow(2,$dcbit+1);
			
			$playorderdc = $this->RumbleDCs[$dcindex];
			
			$xepd = new EPD(161849);
			$yepd = new EPD(161859);
			
			$ycondFar = ''; $ycondMed = ''; $ycondNear = '';
			$xcondFar = ''; $xcondMed = ''; $xcondNear = '';
			
			if($y instanceof Deathcounter){
				$c = 10000 + 6*32;
				$ycondFar  = $y->between($c-5*32,   $c+5*32);
				$ycondMed  = $y->between($c-10*32,  $c-10*32);
				$ycondNear = $y->between($c-15*32,  $c+15*32);
			}
			if(is_int($y)){
				$c = $y-6*32;
				$ycondFar  = $yepd->between($c-5*32,    $c+5*32);
				$ycondMed  = $yepd->between($c-10*32,   $c+10*32);
				$ycondNear = $yepd->between($c-15*32,   $c+15*32);
			}
			
			if($x instanceof Deathcounter){
				$c = 10000 + 10*32;
				$xcondFar  = $x->between($c-5*32,   $c+5*32);
				$xcondMed  = $x->between($c-10*32,  $c-10*32);
				$xcondNear = $x->between($c-15*32,  $c+15*32);
			}
			if(is_int($x)){
				$c = $x-10*32;
				$xcondFar  = $xepd->between($c-5*32,    $c+5*32);
				$xcondMed  = $xepd->between($c-10*32,   $c+10*32);
				$xcondNear = $xepd->between($c-15*32,   $c+15*32);
			}
			
			$thirdofintensity = (int)round($intensity/3);
			
			$text .= repeat(1,
				
				// detect if rumble activated
				_if( $playorderdc->atLeast($power2) )->then(
					$playorderdc->subtract($power2),
					$activated->set(),
				''),
				_if( $playorderdc->atLeast($power1) )->then(
					$playorderdc->subtract($power1),
					$activated->set(),
				''),
				
				// rumble based on how far out it is
				_if( $activated, $ycondFar,  $xcondFar  )->then( $this->RumbleLevel->add($thirdofintensity) ),
				_if( $activated, $ycondMed,  $xcondMed  )->then( $this->RumbleLevel->add($thirdofintensity) ),
				_if( $activated, $ycondNear, $xcondNear )->then( $this->RumbleLevel->add($thirdofintensity) ),
				
				$activated->clear(),
			'');
			
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
		
		return $this->AtDCs[$dcindex]->add($value);
	}
	
	public function getRumbleAtCommand($intensity, $x, $y){
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
		if(!is_int($intensity)){
			Error('$intensity parameter must be an integer');
		}
		
		// Current command in question
		$command = func_get_args();
		
		// Find index
		$index = array_search($command, $this->AtRumbles, true);
		
		// If not found, add it to command list
		if( $index === false ){
			$index = count($this->AtRumbles);
			$this->AtRumbles[] = $command;
		}
		
		$dcindex = (int)floor($index*2 / 30);
		$dcbit = $index*2 % 30;
		$value = pow(2,$dcbit);
		
		// If there aren't enough deathcounters, make another
		if( $dcindex >= count($this->RumbleDCs) ){
			$this->RumbleDCs[] = new Deathcounter();
		}
		
		return $this->RumbleDCs[$dcindex]->add($value);
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