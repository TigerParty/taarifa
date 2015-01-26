<?php
//The location code is the simple concatenation of the row/col index of
//a cell in a rectangular grid drawn over Tanzania (so its only valid there :))
//A bit hacky and a total disregard for the earths curvature etc.
//but hey, it gives you an approximate location
class LocationCode {
	private $tr_lat,$tr_long;
	private $bl_lat, $bl_long;
	private $lat_step, $long_step;
	private $latdiff,$longdiff;
	private $cellsize,$pad;
 
 	public function __construct() {
 		//make a *very* rough bounding box arond TZ
 		//using top-right and bottom-left corners
		$this->tr_lat = -0.922812;
		$this->tr_long = 40.561523;
		
		$this->bl_lat = -12.039;
		$this->bl_long = 29.399;

		//cellsize in meters
		$this->cellsize = 100;

		//*roughly* 1200 by 1300 km
		//divide by the cellsize
		$this->lat_step = 1213 * 1000 / $this->cellsize;
		$this->long_step = 1335 * 1000 / $this->cellsize;
	
		$this->latdiff = abs($this->bl_lat - $this->tr_lat) / $this->lat_step ;
		$this->longdiff = abs($this->bl_lat - $this->tr_lat) / $this->long_step;
		
		//length of the string representing a coordinate (left padded with 0 if needed)
		$this->len = 5;
	}
	
	public function coords2id($lat, $long) {
		//floor -> bottom left point is the origin
		$lat_id = floor(($lat - $this->bl_lat) / $this->latdiff);
    	$long_id = floor(($long - $this->bl_long) / $this->longdiff);
		
		//simply join both coords padded to a fixed length
		//we only want numbers and the cellsize is quite small -> big number  	
    	$id = str_pad(strval($lat_id),$this->len,"0",STR_PAD_LEFT) . str_pad(strval($long_id),$this->len,"0",STR_PAD_LEFT);
    	
    	return $id;
    }
    
	//TODO: No bounds checking done
	public function tocoords($lat_id, $long_id) {
    	$lat = $this->bl_lat + ($lat_id * $this->latdiff);
    	$long = $this->bl_long + ($long_id * $this->longdiff);
    	
    	return array($lat,$long);
    }
    
    public function id2coords($id){
    	$lats = intval(ltrim(substr($id,0,$this->len),"0"));
    	$long = intval(ltrim(substr($id,$this->len),"0"));

		return $this->tocoords($lats,$long);
    }
}


?>
