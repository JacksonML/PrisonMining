<?php

namespace JacksonML\PrisonMining;

class Mine{
    public $name;
    public $coords;
    public $blocks;
    public $teleportPos;
    public function __construct($name,$x1,$y1,$z1,$x2,$y2,$z2,$blockArray = array(), $telpos = NULL){
        $this->name = $name;
        $coords1 = array();
        $coords2 = array();
        $this->blocks = array();
        if($x1 > $x2){
            array_push($coords2, $x1);
            array_push($coords1, $x2);
        }else{
            array_push($coords1, $x1);
            array_push($coords2, $x2);
        }
        if($y1 > $y2){
            array_push($coords2, $y1);
            array_push($coords1, $y2);
        }else{
            array_push($coords1, $y1);
            array_push($coords2, $y2);
        }
        if($z1 > $z2){
            array_push($coords2, $z1);
            array_push($coords1, $z2);
        }else{
            array_push($coords1, $z1);
            array_push($coords2, $z2);
        }
        $this->coords = array("coords1" => $coords1,
            "coords2" => $coords2);
        if(count($blockArray)){
            $this->blocks = $blockArray;
        }
        if (isset($telpos)){
            $this->teleportPos = $telpos;
        }
}
    public function addBlock($blockToAdd, $percentage,$sender){
        //Calculates total percentage
        $percentageTotal = 0;
        if(count($this->blocks) > 0){
            for($i = 0;$i < count($this->blocks); $i++){ $percentageTotal += $this->blocks[$i]["percentage"];}
        }
        //Checks if the total percentage is less than 100% to prevent going over
        if ($percentageTotal + $percentage <= 100){
            $block = array(
                "blockId" => $blockToAdd,
                "percentage" => $percentage
            );
            array_push($this->blocks, $block);
            //array_push($GLOBALS["mineData"]["blocks"],$block);
        }else{
            $sender->sendMessage("Percentage exceeds maximum value by " . $percentageTotal - 100 . "%!");
        }
    }
}
