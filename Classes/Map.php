<?php require_once($_SERVER['DOCUMENT_ROOT']."/Compiler/Oreo/initialize.php");

class Map {
	
	protected $MapTitle;
	protected $Force1 = [ "Name" => "Force 1" ];
	protected $Force2 = [ "Name" => "Force 2" ];
	protected $Force3 = [ "Name" => "Force 3" ];
	protected $Force4 = [ "Name" => "Force 4" ];
	
	public function __construct(){
		MintMapTitle($this->MapTitle);
		
		
		// Force Business
		$this->Force1["Force"] = Force1; $this->Force2["Force"] = Force2; $this->Force3["Force"] = Force3; $this->Force4["Force"] = Force4;
		$forces = [$this->Force1, $this->Force2, $this->Force3, $this->Force4];
		foreach($forces as $force){
			MintForceSettings($force["Force"], $force["Name"], $force["Allied"], $force["Shared Vistion"], $force["Randomize Start"], $force["Allied Victory"]);
			foreach( $force["Players"] as $player=>$type){
				MintPlayer($player, $type, $force["Force"]);
			}
		}
		
		
		
		$this->main();		
	}
	
	public function main(){
		
	}
	
}