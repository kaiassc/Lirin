<?php



class FX {
	
	
	
	static function rumbleAt($intensity, $x, $y){
		$sfxmanager = FXManager::getInstance();
		return $sfxmanager->getRumbleAtCommand($intensity, $x, $y);
	}
	
	static function playWavAt(Sound $wav, $x, $y){
		return $wav->playAt($x, $y);
	}
	
	
}