<?php require_once("$_SERVER[DOCUMENT_ROOT]/Compiler/Oreo/"."initialize.php");


class Map {
	
	protected $MapTitle;
	protected $MapDescription = "";
	protected $Force1 = array( "Name" => "Force 1" );
	protected $Force2 = array( "Name" => "Force 2" );
	protected $Force3 = array( "Name" => "Force 3" );
	protected $Force4 = array( "Name" => "Force 4" );
	
	protected $Mint;
	protected $ClassFolder = "Classes";
	
	protected $SuppressOutput = FALSE;
	protected $ShowAnalysis = FALSE;
	protected $RetainXML = FALSE;
	
	static $xdim = 256;
	static $ydim = 256;
	
	protected $P1, $P2, $P3, $P4, $P5, $P6, $P7, $P8; 
	
	public function __construct(){
		if( static::$xdim ){
			Map::$xdim = static::$xdim;
		}
		if( static::$ydim ){
			Map::$ydim = static::$ydim;
		}
		
		// Settings
		RetainTmpXML($this->RetainXML);
		if ( $this->SuppressOutput ){ SuppressOutput(); }
		if ( $this->ShowAnalysis ){ ShowAnalysis(); }
		
		if( $this->Mint ){ 
			Mint($this->Mint[0], $this->Mint[1]);
		}
		MintMapTitle($this->MapTitle);
		MintMapDesc($this->MapDescription);
		SetClassFolder($this->ClassFolder);
		
		// Force Business
		$this->Force1["Force"] = Force1; $this->Force2["Force"] = Force2; $this->Force3["Force"] = Force3; $this->Force4["Force"] = Force4;
		$forces = [$this->Force1, $this->Force2, $this->Force3, $this->Force4];
		foreach($forces as $force){
			MintForceSettings($force["Force"], $force["Name"], $force["Allied"], $force["Shared Vistion"], $force["Randomize Start"], $force["Allied Victory"]);
			foreach( $force["Players"] as $player=>$type){
				MintPlayer($player, $type, $force["Force"]);
			}
		}
		
		
		$this->P1 = new Player(P1);
		$this->P2 = new Player(P2);
		$this->P3 = new Player(P3);
		$this->P4 = new Player(P4);
		$this->P5 = new Player(P5);
		$this->P6 = new Player(P6);
		$this->P7 = new Player(P7);
		$this->P8 = new Player(P8);
		
		$this->Main();		
	}
	
	public function Main(){
		Error("You need to define a main function to put your triggers in");
	}
	
	public function __destruct(){
		
	}
	
}