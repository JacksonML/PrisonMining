<?php

namespace JacksonML\PrisonMining;

class Mine{
    public $name;
    public $coords;
    public $blocks;
    public function __construct($name,$x1,$y1,$z1,$x2,$y2,$z2){
        $this->name = $name;
        $coords1 = array();
        $coords2 = array();
        $blocks = array();
        
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
        //$coords1 = array("x" => $x1,
            //"y" => $y1,
            //"z" => $z1);
        //$coords2 = array("x" => $x2,
            //"y" => $y2,
            //"z" => $z2);
        $this->coords = array("coords1" => $coords1,
            "coords2" => $coords2);
}
    public function checkBlocks(){
        if ($blocks){
            return 1;
        }else{
            return 0;
        }
    }
    public function addBlock($block, $percentage){
        array_push($block,$percentage);
    }
}
