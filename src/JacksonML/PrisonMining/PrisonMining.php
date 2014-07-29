<?php

namespace JacksonML\PrisonMining;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\block\block;
use pocketmine\utils\Config;
use JacksonML\PrisonMining\Mine;

class PrisonMining extends PluginBase{
    public function onEnable(){
        $this->getLogger()->info("Prison Mining is loading mines");
        
        //Creates the config folder and file
        @mkdir($this->getDataFolder());
        $this->configFile = new Config($this->getDataFolder()."saves.yml", Config::YAML, array());
        
        //Pulls data out of config and into a variable
        $this->mineData = array();
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
        
        //Re-Saves mines to file
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
            $sender->sendMessage($this->test1->coords["coords1"][0]);
            $mineToSendData = array("name" => $this->test1->name,
                "coords" => $this->test1->coords);
            array_push($this->mineData, $mineToSendData);
            return true;
        }elseif(strtolower($command->getName()) === "prmfill"){
            if($args[0]){ // Controls if command returns false or true
            
            $mineId = $args[0];
            $this->getLogger()->info(count($this->test2));
            for($i = 0;$i < count($this->mineData);$i++){
            $this->getLogger()->info($this->mineData[$i]["name"]);
            if($this->mineData[$i]["name"] == $mineId){
                
                // Creates variables for easier coding
                $x1Loop = $this->mineData[$i]["coords"]["coords1"][0];$x2Loop = $this->mineData[$i]["coords"]["coords2"][0];
                $y1Loop = $this->mineData[$i]["coords"]["coords1"][1];$y2Loop = $this->mineData[$i]["coords"]["coords2"][1];
                $z1Loop = $this->mineData[$i]["coords"]["coords1"][2];$z2Loop = $this->mineData[$i]["coords"]["coords2"][2];
                
                // Loops through all blocks and places all blocks
                for($xLoop = 0; $xLoop <= $x2Loop-$x1Loop;$xLoop++){ //Loops through all X blocks
                    for($yLoop = 0; $yLoop <= $y2Loop-$y1Loop;$yLoop++){ //Loops through all Y blocks
                        for($zLoop = 0; $zLoop <= $z2Loop-$z1Loop;$zLoop++){ //Loops through all Z blocks
                            $this->getServer()->getLevelByName("flat")->setBlock(new Vector3($xLoop+$this->mineData[$i]["coords"]["coords1"][0],$yLoop+$this->mineData[$i]["coords"]["coords1"][1],$zLoop+$this->mineData[$i]["coords"]["coords1"][2]), Block::get(46), true, true);
                            $this->getLogger()->info("Placed block at x:" . $xLoop . ", y:" . $yLoop . ", z:" . $zLoop);
                        }     
                    }
                }
            }
        }
        return true;}else{return false;}
        }
    }
}
