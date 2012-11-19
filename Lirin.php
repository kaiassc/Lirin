<?php require_once("Classes/Map.php");

class Lirin extends Map {
	
	protected $MapTitle = "Lirin";
	protected $MapDescription = "";
	protected $Force1 = [ "Name" => "Visioners",    "Players" => [P1 => Computer, P2 => Computer, P3 => Computer] ];
	protected $Force2 = [ "Name" => "Humans",       "Players" => [P4 => Human, P5 => Human, P6 => Human] ];
	protected $Force3 = [ "Name" => "Allied Comp",  "Players" => [P7 => Computer] ];
	protected $Force4 = [ "Name" => "Enemy Comp",   "Players" => [P8 => Computer] ];
	protected $MintIn = "filepath";
	protected $MintOut = "filepath";
	
	
	function main(){
		// Players
		$P1 = new Player(P1); $P2 = new Player(P2); $P3 = new Player(P3); $P4 = new Player(P4);
		$P5 = new Player(P5); $P6 = new Player(P6); $P7 = new Player(P7); $P8 = new Player(P8);
		$visioners = new Player(P1, P2, P3);
		$humans = new Player(P3, P4, P5);
		$allyComp = new Player(P7);
		$enemyComp = new Player(P8);
		$comps = new Player(P4,P5,P6,P7,P8);
		$PArray = array(1 => $P1, 2 => $P2, 3 => $P3, 4 => $P4, 5 => $P5, 6 => $P6, 7 => $P7, 8 => $P8);

		// Triggers
		$P1->_if( CommandTheMost("Protoss Zealot") )->then([
			Display("No problem man"),
		]);
		
		
		
	}
	
}

new Lirin();
