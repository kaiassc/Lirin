<?php

class Loc {
	
	/* @var ExtendableLocation */ static $main;
	
	/* @var LirinLocation */ static $aoe1x1;
	/* @var LirinLocation */ static $aoe2x2;
	/* @var LirinLocation */ static $aoe3x3;
	/* @var LirinLocation */ static $aoe4x4;
	/* @var LirinLocation */ static $aoe5x5;
	
	/* @var LirinLocation */ static $slideLeft1;
	/* @var LirinLocation */ static $slideLeft8;
	/* @var LirinLocation */ static $slideLeft64;
	
	/* @var LirinLocation */ static $sandbox;
	/* @var LirinLocation */ static $origin;
	/* @var LirinLocation */ static $shiftLeft;
	/* @var LirinLocation */ static $shiftUp;
	/* @var LirinLocation */ static $topLeft;
	
	/* @var LirinLocation[] */ static $YLoc = array();
	
	function populate(){
		self::$main         = new ExtendableLocation("main");
		self::$aoe1x1       = LocationManager::MintLocation("aoe1x1", 0, 0, 1*32, 1*32);
		self::$aoe2x2       = LocationManager::MintLocation("aoe2x2", 0, 0, 2*32, 2*32);
		self::$aoe3x3       = LocationManager::MintLocation("aoe3x3", 0, 0, 3*32, 3*32);
		self::$aoe4x4       = LocationManager::MintLocation("aoe4x4", 0, 0, 4*32, 4*32);
		self::$aoe5x5       = LocationManager::MintLocation("aoe5x5", 0, 0, 5*32, 5*32);
		
		self::$slideLeft1   = Grid::$slideLeft1;
		self::$slideLeft8   = Grid::$slideLeft8;
		self::$slideLeft64  = Grid::$slideLeft64;
		
		self::$sandbox      = Grid::$sandbox; 
		self::$origin       = Grid::$origin; 
		self::$shiftLeft    = Grid::$shiftLeft; 
		self::$shiftUp      = Grid::$shiftUp; 
		self::$topLeft      = Grid::$topLeft;
		
		self::$YLoc = Grid::$YLoc;
	}
	
	
	
	
	
}