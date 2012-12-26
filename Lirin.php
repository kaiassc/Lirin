<?php require_once("Classes/Map.php");

class Lirin extends Map {
	
	protected $MapTitle = "Lirin";
	protected $MapDescription = "";
	protected $Force1 = array( "Name" => "Visioners",    "Players" => [P1 => Computer, P2 => Computer, P3 => Computer] );
	protected $Force2 = array( "Name" => "Humans",       "Players" => [P4 => Human, P5 => Human, P6 => Human] );
	protected $Force3 = array( "Name" => "Allied Comp",  "Players" => [P7 => Computer] );
	protected $Force4 = array( "Name" => "Enemy Comp",   "Players" => [P8 => Computer] );
	
	function Main(){
		
		// Players
		$P1 = new Player(P1); $P2 = new Player(P2); $P3 = new Player(P3); $P4 = new Player(P4);
		$P5 = new Player(P5); $P6 = new Player(P6); $P7 = new Player(P7); $P8 = new Player(P8);
		$All = new Player(P1,P2,P3,P4,P5,P6,P7,P8);
		$visioners = new Player(P1, P2, P3);
		$humans = new Player(P3, P4, P5);
		$allyComp = new Player(P7);
		$enemyComp = new Player(P8);
		$comps = new Player(P4,P5,P6,P7,P8);
		$PArray = array(1 => $P1, 2 => $P2, 3 => $P3, 4 => $P4, 5 => $P5, 6 => $P6, 7 => $P7, 8 => $P8);
		 
		
		MintUnit("Start Location", $All, 250, 1400);
		MintMapRevealers(256,256,P4);
		
		for($i=1;$i<=128;$i++){
			MintUnit(1049,P2, (64+$i)*32,(256-8)*32);
		}
		
		// Locations
		$shiftleft = MintLocation("Shift Left",0,0,256*32*2,0);
		$shiftup = MintLocation("Shift Up",0,0,0,256*32*2);
		$playersection = MintLocation("Player Section", 64,640,8182,7552);
		$sandbox = MintLocation("sandbox",0,0,256*32,256*32);
		$AoE0x0 = MintLocation("AoE0x0",16,16,16,16);
		$AoE1x1 = MintLocation("AoE1x1",0,0,1*32,1*32);
		$AoE2x2 = MintLocation("AoE2x2",0,0,2*32,2*32);
		$AoE3x3 = MintLocation("AoE3x3",0,0,3*32,3*32);
		$AoE4x4 = MintLocation("AoE4x4",0,0,4*32,4*32);
		$AoE5x5 = MintLocation("AoE5x5",0,0,5*32,5*32);
		
		$XLoc = array();
		$YLoc = array();
		for($i=1;$i<=64;$i++){ $XLoc[$i] = MintLocation("XLoc$i",0,0,$i*128,0); }
		for($i=1;$i<=64;$i++){ $YLoc[$i] = MintLocation("YLoc$i",0,0,0,$i*128); }
		$gridorigin = MintLocation("Grid Origin", 256*32, 256*32, 256*32, 256*32);
		
		for($i=0;$i<7;$i++){ 
			MintLocation("Extra", 16, 16, 16, 16);
		}
		MintLocation("Main", 16,16,16,16);
		$main = new ExtendableLocation("Main");
		
		// Triggers
		$tempdc1 = new TempDC(100);
		$tempdc2 = new TempDC(100);
		$tempdc3 = new TempDC(100);
		//$CodeStorage = new LirinStorage($tempdc1, $tempdc2, $tempdc3);
		
		$success = new TempSwitch();
		$P1->_if( Elapsed(AtMost, 30) )->then(
			//$CodeStorage->storeCode(11, 6, 22, $success),
			_if( $success )->then(
				//$CodeStorage->export(),
			e)->_else(
				Display("You used an unrecognized character!"),
			''),
			$success->release(),
			$tempdc1->release(),
			$tempdc2->release(),
			$tempdc3->release(),
		'');
		
		
		
		
		
	}
	
	
}

require_once("$_SERVER[DOCUMENT_ROOT]/Compiler/UserSpecific.php");
new Lirin();
