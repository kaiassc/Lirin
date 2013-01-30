<?php

class EnemyType  extends BSType{
	
	public $Name;
	public $Damage      = 10;
	public $Health      = 10;
	public $Mana        = 0;
	public $Armor       = 0;
	public $MagicResist = 0;
	
	public $Codex       = "";
	
	protected static $NextID = 2;
	
	function __construct($statsarray){
		parent::__construct($statsarray);
		$this->Name         = $statsarray["name"]; 
		$this->Damage       = $statsarray["damage"];
		$this->Health       = $statsarray["health"];
		$this->Mana         = $statsarray["mana"];
		$this->Armor        = $statsarray["armor"];
		$this->MagicResist  = $statsarray["magicresist"];
		
		if(isset($statsarray["codex"])){
			$this->Codex    = $statsarray["codex"];
		}
		
	}
	
}


