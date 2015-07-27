<?php

namespace JacksonML\PrisonMining;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\block\block;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent; 
use pocketmine\utils\TextFormat;
use JacksonML\PrisonMining\Mine;
use JacksonML\PrisonMining\MovementRestriction;

class PrisonMining extends PluginBase implements Listener{

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getLogger()->info("Prison Mining is loading data");

        //Creates the config folder and file
        @mkdir($this->getDataFolder());
        $this->configFile = new Config($this->getDataFolder() . "saves.yml", Config::YAML, array());

        $this->mines = array();
        $this->configData = $this->configFile->get("Mines");
        for ($i = 0; $i < count($this->configData); $i++) {

            //Creates object
            array_push($this->mines, new Mine($this->configData[$i]["name"], $this->configData[$i]["coords"]["coords1"][0], $this->configData[$i]["coords"]["coords1"][1], $this->configData[$i]["coords"]["coords1"][2], $this->configData[$i]["coords"]["coords2"][0], $this->configData[$i]["coords"]["coords2"][1], $this->configData[$i]["coords"]["coords2"][2], $this->configData[$i]["blocks"], $this->configData[$i]["time"]));
            $this->getLogger()->info("Mine " . $this->configData[$i]["name"] . " has loaded.");
        }
        // User data config
        $this->userFile = new Config($this->getDataFolder() . "users.yml", Config::YAML, array());
        $this->userData = $this->userFile->get("Users");
        $this->users = array();
        for ($i = 0; $i < count($this->userData); $i++) {
            array_push($this->users, $this->userData[$i]);
        }
        $this->settingsFile = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        // DISABLED UNTIL CONFIG OPTION IS PUT IN
        /*$this->getLogger()->info("Prison Mining is starting the player watcher");
        $task = new MovementRestriction($this, $this->users);
        $this->getServer()->getScheduler()->scheduleRepeatingTask($task, 4);*/
    }

    public function onDisable() {
        $this->getLogger()->info("Prison Mining is saving data");

        //Constructs data for saving
        $mineDataConfig = array();
        for ($i = 0; $i < count($this->mines); $i++) {
            $mineSending = array("name" => $this->mines[$i]->name,
                "coords" => $this->mines[$i]->coords,
                "blocks" => $this->mines[$i]->blocks,
                "time" => $this->mines[$i]->time);
            array_push($mineDataConfig, $mineSending);
        }

        //Re-Saves mines to file
        //$this->configFile->set("Mines",$GLOBALS["mineData"]);
        $this->configFile->set("Mines", $mineDataConfig);
        $this->configFile->save();
        $this->userFile->set("Users", $this->users);
        $this->userFile->save();
        $this->getLogger()->info("Prison Mining has saved data");
    }

    /* ////// OLD FUNCTIONS ////////
     * THESE WILL BE REMOVED AFTER THE REWRITE IS FINISHED
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
      } */

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if (strtolower($command->getName()) === "prm") {
            $sender->sendMessage("/prm: Show help");
            $sender->sendMessage("/prmspot1: Defines first corner");
            $sender->sendMessage("/prmspot2: Defines second corner");
            $sender->sendMessage("/prmdefine <id>");
            $sender->sendMessage($this->x1);
            $sender->sendMessage($this->test1->coords["coords1"][0]);
            return true;
        } elseif (strtolower($command->getName()) === "prmspot1") {
            $this->x1 = $sender->x;
            $this->y1 = $sender->y;
            $this->z1 = $sender->z;

            $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." Acquired spot1");
            return true;
        } elseif (strtolower($command->getName()) === "prmspot2") {
            $this->x2 = $sender->x;
            $this->y2 = $sender->y;
            $this->z2 = $sender->z;

            $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." Acquired spot2");
            return true;
        } elseif (strtolower($command->getName()) === "prmdefine") {
            if (isset($this->x1,$this->y1,$this->z1,$this->x2,$this->y2,$this->z2) == false){
                $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." All positions were not found! Please use /prmspot1 and /prmspot2 to define corner points of the mine!");
                return true;
            }
            array_push($this->mines, new Mine($args[0], $this->x1, $this->y1, $this->z1, $this->x2, $this->y2, $this->z2));

            $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE."" . $args[0] . " has been created.");
            return true;
        } elseif (strtolower($command->getName()) === "prmfill") {
            /*
             * This command needs major optimization. Hopefully I will be able to make it
             * update chunks Asynchronously instead of on the main thread. For now, it will
             * send direct updates to the client, because if you use updateAround() it
             * appears to freeze the client for a few seconds.
             */
            if ($args[0]) { // Controls if command returns false or true
                if ($this->mines[0] == null){
                    $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." There are no mines! Create one with /prmdefine!");
                    return true;
                }
                for ($m = 0; $m < count($this->mines); $m++) {
                    if ($args[0] == $this->mines[$m]->name) {
                        break;
                    }
                }
                if ($this->mines[$m] == null){
                    $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." This is not a valid mine!");
                }
                // Creates variables for cleaner coding
                $x1Loop = &$this->mines[$m]->coords["coords1"][0];
                $x2Loop = &$this->mines[$m]->coords["coords2"][0];
                $y1Loop = &$this->mines[$m]->coords["coords1"][1];
                $y2Loop = &$this->mines[$m]->coords["coords2"][1];
                $z1Loop = &$this->mines[$m]->coords["coords1"][2];
                $z2Loop = &$this->mines[$m]->coords["coords2"][2];
                $blockOriginal = $this->mines[$m]->blocks;
                $percentageAdding = 0;
                $blockData = array();
                for ($i = 0; $i < count($blockOriginal); $i++) {

                    $blockProcess = array("blockId" => $blockOriginal[$i]["blockId"],
                        "percentage" => $percentageAdding);
                    array_push($blockData, $blockProcess);
                    $percentageAdding = $blockOriginal[$i]["percentage"] + $percentageAdding;
                }

                // Loops through all blocks and places all blocks
                for ($xLoop = 0; $xLoop <= $x2Loop - $x1Loop; $xLoop++) { //Loops through all X blocks
                    for ($yLoop = 0; $yLoop <= $y2Loop - $y1Loop; $yLoop++) { //Loops through all Y blocks
                        for ($zLoop = 0; $zLoop <= $z2Loop - $z1Loop; $zLoop++) { //Loops through all Z blocks
                            //Block chooser
                            $randomBlock = mt_rand(0, 1000) / 10;


                            for ($i = 0; $i < count($blockData); $i++) {

                                if (isset($blockData[$i + 1])) {
                                    if (($blockData[$i]["percentage"] < $randomBlock) and ( $blockData[$i + 1]["percentage"] > $randomBlock)) {
                                        $this->blockIdFill = $blockData[$i]["blockId"];
                                    }
                                } else {
                                    if ($blockData[$i]["percentage"] < $randomBlock) {
                                        $this->blockIdFill = $blockData[$i]["blockId"];
                                    }
                                }
                            }
                            //Block Placer
                            $this->getServer()->getLevelByName("world")->setBlock(new Vector3($xLoop + $x1Loop, $yLoop + $y1Loop, $zLoop + $z1Loop), Block::get($this->blockIdFill), true, false);
                        }
                    }
                }
                $i = 20;
                //$this->getServer()->getLevelByName("world")->updateAround(new Vector3($xLoop, $yLoop, $zLoop));
                /* for($xLoop = 0; $xLoop <= $x2Loop-$x1Loop;$xLoop += 16){ //Loops through all X blocks
                  for($yLoop = 0; $yLoop <= $y2Loop-$y1Loop;$yLoop += 16){ //Loops through all Y blocks
                  $this->getLogger()->info($i);
                  $i += 5000;
                  $this->getServer()->getLevelByName("world")->scheduleUpdate(new Vector3($xLoop,$yLoop,$zLoop),$i);
                  }
                  } */

                $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE."" . $args[0] . " has been filled");
                unset($percentageAdding, $blockOriginal, $blockProcess, $x1Loop, $y1Loop, $z1Loop, $x2Loop, $y2Loop, $z2Loop);
                return true;
            } else {
                return false;
            }
        } elseif (strtolower($command->getName()) === "prmaddblock") {
            for ($i = 0; $i < count($this->mines); $i++) {
                if ($args[0] == $this->mines[$i]->name) {
                    break;
                }
            }
            $this->mines[$i]->addBlock($args[1], $args[2], $sender);
            return true;
        } elseif (strtolower($command->getName()) === "jail") {
            $server = $this->getServer();
            array_push($this->users, array($args[0],
                $args[1],
                array($server->getPlayer($args[0])->x, $server->getPlayer($args[0])->y, $server->getPlayer($args[0])->z)));
            $mineToUse = mt_rand(0, count($this->mines));
            $this->getLogger()->info($mineToUse);
            $server->getPlayer($args[0])->teleport(new Vector3($this->mines[$mineToUse]->coords["coords2"][0], $this->mines[$mineToUse]->coords["coords2"][1], $this->mines[$mineToUse]->coords["coords2"][2]));
            return true;
        } elseif (strtolower($command->getName()) === "jailstatus") {
            // Loop to search through user database
            for($i = 0; $i < count($this->users);$i++){
                if($this->users[$i][0] == $sender->getName()){
                    $sender->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." You must break " . TextFormat::BOLD.$this->users[$i][1] . TextFormat::RESET." more blocks until you are free!");
                    return true;
                }
                else { $sender->sendMessage (TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." You are not in jail!"); return true; }
            }
            $sender->sendMessage (TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." You are not in jail!");
            return true;
        }
    }

    /*public function onPlayerPreCommand(PlayerCommandPreprocessEvent $event) {
        $playerCMD = array_search($event->getPlayer()->getName(), $this->users);

        //if ($this->isAtSpawn($event->getPlayer()->entity))
            //$event->setCancelled(true);
    }*/
    public function onBlockBreak(BlockBreakEvent $event){
        for($i = 0; $i < count($this->users);$i++){
            if($this->users[$i][0] == $event->getPlayer()->getName() and $event->isCancelled() != true){
                $this->users[$i][1]--;
                
                // Checks if the user doesn't have any blocks left to break. If they are below 1, they are set free
                if ($this->users[$i][1] < 1)
                {
                    $event->getPlayer()->sendMessage(TextFormat::DARK_PURPLE."[Jail]" . TextFormat::WHITE." You are free to go!");
                    $this->getLogger()->info($event->getPlayer()->getName(). " is now free!");
                    array_splice($this->users, $i, 1);
                }
                break;
            }
        }
    }

}
