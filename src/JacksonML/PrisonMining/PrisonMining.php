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
        $GLOBALS["mineData"] = array();
        $this->mineNames = array();
        $this->configData = $this->configFile->get("Mines");
        for($i = 0;$i < count($this->configData);$i++){
            
            //Sends data to array. Will HOPEFULLY be removed/changed soon
            $mineToSendDataEnable = array("name" => $this->configData[$i]["name"],
            "coords" => $this->configData[$i]["coords"],
            "blocks" => $this->configData[$i]["blocks"]);
            $this->getLogger()->info("Mine " . $this->configData[$i]["name"] . " has loaded.");
            array_push($GLOBALS["mineData"], $mineToSendDataEnable);
            array_push($this->mineNames,$this->configData[$i]["name"]);
            
            //Creates object
            $GLOBALS["_MineObjData_" . $GLOBALS["mineData"][$i]["name"]] = new Mine($GLOBALS["mineData"][$i]["name"],$GLOBALS["mineData"][$i]["coords"]["coords1"][0],$GLOBALS["mineData"][$i]["coords"]["coords1"][1],$GLOBALS["mineData"][$i]["coords"]["coords1"][2],$GLOBALS["mineData"][$i]["coords"]["coords2"][0],$GLOBALS["mineData"][$i]["coords"]["coords2"][1],$GLOBALS["mineData"][$i]["coords"]["coords2"][2],$GLOBALS["mineData"][$i]["blocks"]);
        }
        $this->getLogger()->info("Prison Mining has been enabled");
    }
    public function onDisable(){
        $this->getLogger()->info("Prison Mining is saving mines");
        
        //Constructs data for saving
        $mineDataConfig = array();
        $this->getLogger()->info("BEFORE LOOP");
        for($i = 0; $i < count($this->mineNames); $i++){
            $mineSending = array("name" => $GLOBALS["_MineObjData_" . $this->mineNames[$i]]->name,
                "coords" => $GLOBALS["_MineObjData_" . $this->mineNames[$i]]->coords,
                "blocks" => $GLOBALS["_MineObjData_" . $this->mineNames[$i]]->blocks);
            array_push($mineDataConfig,$mineSending);
            $this->getLogger()->info("SAVED LOOP");
        }


        //Re-Saves mines to file
        //$this->configFile->set("Mines",$GLOBALS["mineData"]);
        $this->configFile->set("Mines",$mineDataConfig);
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
            $mineToSendData = array("name" => $this->test1->name,
                "coords" => $this->test1->coords);
            array_push($GLOBALS["mineData"], $mineToSendData);
            array_push($this->mineNames,$args[0]);
            
            $GLOBALS["_MineObjData_" . $args[0]] = new Mine($args[0],$this->x1,$this->y1,$this->z1,$this->x2,$this->y2,$this->z2);

            $sender->sendMessage($args[0] . " has been created.");
            return true;            
        }elseif(strtolower($command->getName()) === "prmfill"){
            if($args[0]){ // Controls if command returns false or true
            
            $mineDataFill = "_MineObjData_" . $args[0];
            
            // Creates variables for cleaner coding
            $x1Loop = &$GLOBALS[$mineDataFill]->coords["coords1"][0];$x2Loop = &$GLOBALS[$mineDataFill]->coords["coords2"][0];
            $y1Loop = &$GLOBALS[$mineDataFill]->coords["coords1"][1];$y2Loop = &$GLOBALS[$mineDataFill]->coords["coords2"][1];
            $z1Loop = &$GLOBALS[$mineDataFill]->coords["coords1"][2];$z2Loop = &$GLOBALS[$mineDataFill]->coords["coords2"][2];
            
            

            // Loops through all blocks and places all blocks
            for($xLoop = 0; $xLoop <= $x2Loop-$x1Loop;$xLoop++){ //Loops through all X blocks
                for($yLoop = 0; $yLoop <= $y2Loop-$y1Loop;$yLoop++){ //Loops through all Y blocks
                    for($zLoop = 0; $zLoop <= $z2Loop-$z1Loop;$zLoop++){ //Loops through all Z blocks
                        //Block chooser
                        $randomBlock = mt_rand (0, 1000) / 10;
                        $blockOriginal = &$GLOBALS[$mineDataFill]->blocks;
                        $percentageAdding = 0;
                        $blockData = array();
                        for($i = 0;$i < count($blockOriginal);$i++){
                            
                            $blockProcess = array ("blockId" => $blockOriginal[$i]["blockId"],
                                "percentage" => $percentageAdding);
                            array_push($blockData, $blockProcess);
                            $percentageAdding = $blockOriginal[$i]["percentage"] + $percentageAdding;
                        }
                        for($i = 0;$i < count($blockData);$i++){
                            
                            if(isset($blockData[$i+1])){
                                if(($blockData[$i]["percentage"] < $randomBlock) and ($blockData[$i+1]["percentage"] > $randomBlock)){
                                    $this->blockIdFill = $blockData[$i]["blockId"];
                                }
                            }else{
                                if($blockData[$i]["percentage"] < $randomBlock){
                                    $this->blockIdFill = $blockData[$i]["blockId"];
                                }
                            }
                        }
                        //Block Placer
                        $this->getServer()->getLevelByName("flat")->setBlock(new Vector3($xLoop+$x1Loop,$yLoop+$y1Loop,$zLoop+$z1Loop), Block::get($this->blockIdFill), true, false);
                    }     
                }
            }
            $sender->sendMessage($args[0] . " has been filled");
            return true;}else{return false;}
        }elseif(strtolower($command->getName()) === "prmaddblock"){
            $GLOBALS["_MineObjData_" . $args[0]]->addBlock($args[1],$args[2], $sender);
            return true;
        }
    }
}
