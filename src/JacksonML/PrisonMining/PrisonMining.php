<?php

namespace JacksonML\PrisonMining;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
//use pocketmine\Server;
//use pocketmine\math\Vector3;
//use pocketmine\block\block;
use pocketmine\utils\Config;

class PrisonMining extends PluginBase{
    public function onEnable(){
        $this->getLogger()->info("Prison Mining is loading mines");
        @mkdir($this->getDataFolder());
        $this->mineData = array();
        $this->configFile = new Config($this->getDataFolder()."saves.yml", Config::YAML, array());
        $this->test2 = $this->configFile->get("Mines");
        for($i = 0;$i < count($this->test2);$i++){
            $mineToSendDataEnable = array("name" => $this->test2[$i]["name"],
            "coords" => $this->test2[$i]["coords"]);
            $this->getLogger()->info("Mine " . $this->test2[$i]["name"] . " has loaded.");
            array_push($this->mineData, $mineToSendDataEnable);
        }
        
        $this->getLogger()->info("Prison Mining has been enabled");
    }
    public function onDisable(){
        $this->getLogger()->info("Prison Mining is saving mines");
        
        $this->configFile->set("Mines",$this->mineData);
        $this->configFile->save();
        
        $this->getLogger()->info("Prison Mining saved the mines and has been disabled");
    }
    public function onCommand(CommandSender $sender,Command $command, $label, array $args){
        if(strtolower($command->getName()) === "prm"){
            $sender->sendMessage("/prm: Show help");
            $sender->sendMessage("/prmspot1: Defines first corner");
            $sender->sendMessage("/prmspot2: Defines second corner");
            $sender->sendMessage("/prmdefine <id>");
            $sender->sendMessage($this->x1);
            $sender->sendMessage($this->test1->coords["coords1"][0]);
            return true;
        }elseif(strtolower($command->getName()) === "prmspot1"){
            $this->x1 = $sender->x;
            $this->y1 = $sender->y;
            $this->z1 = $sender->z;
            
            $sender->sendMessage("Acquired spot1");
            return true;
        }elseif(strtolower($command->getName()) === "prmspot2"){
            $this->x2 = $sender->x;
            $this->y2 = $sender->y;
            $this->z2 = $sender->z;
            
            $sender->sendMessage("Acquired spot2");
            return true;
        }elseif(strtolower($command->getName()) === "prmdefine"){
            $this->test1 = new Mine($args[0],$this->x1,$this->y1,$this->z1,$this->x2,$this->y2,$this->z2);
            $sender->sendMessage($this->test1->coords["coords1"]["x"]);
            $mineToSendData = array("name" => $this->test1->name,
                "coords" => $this->test1->coords);
            array_push($this->mineData, $mineToSendData);
            return true;
        }
        return false;
    }
}

class Mine{
    public $name;
    public $coords;
    public function __construct($name,$x1,$y1,$z1,$x2,$y2,$z2){
        $this->name = $name;
        $coords1 = array("x" => $x1,
            "y" => $y1,
            "z" => $z1);
        $coords2 = array("x" => $x2,
            "y" => $y2,
            "z" => $z2);
        $this->coords = array("coords1" => $coords1,
            "coords2" => $coords2);
}
}
