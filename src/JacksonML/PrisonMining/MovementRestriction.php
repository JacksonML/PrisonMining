<?php

namespace JacksonML\PrisonMining;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

class MovementRestriction extends PluginTask{
    public function onRun($currentTick){
        $this->getOwner()->getServer()->broadcastMessage($this->users[0][0]);
        for($i = 0; count($users);$i++){
            $player = $this->getOwner()->getServer()->getPlayerExact($users[$i]);
            // Checks if the player is OUTSIDE of the mine. If the player is, then he/she is teleported
            if (isset($player)){ // Checks if player is online to avoid errors and excess resource usage
                if($player->x <= $this->mineBounds["coords1"][0] and $player->x >= $this->mineBounds["coords2"][0] and
                        $player->y <= $this->mineBounds["coords1"][1] and $player->y >= $this->mineBounds["coords2"][1] and
                        $player->z <= $this->mineBounds["coords1"][2] and $player->z >= $this->mineBounds["coords2"][2]){
                    $player = $this->getOwner()->getServer()->getPlayerExact($users[$i])->teleport(new Vector3(
                            $this->mineBounds["coords2"][0],$this->mineBounds["coords2"][1],$this->mineBounds["coords2"][2]));
                }
            }
        }
        
    }
    public function __construct(Plugin $owner, &$users, $mine) {
        parent::__construct($owner);
        $this->users = $users;
        $this->mineBounds = $mine["coords"];
    }
}
