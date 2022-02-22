<?php

namespace Warp;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\{CommandSender, Command};
use pocketmine\world\Position;

class Warp extends PluginBase implements Listener {

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->data = new Config($this->getDataFolder(). "Warp.yml",Config::YAML);
        $this->db = $this->data->getAll();    
        }                                                                                    

    public function save():void{
        $this->data->setAll($this->db);
        $this->data->save();
    }

    public function onCommand (CommandSender $p, Command $cmd, string $label, array $args): bool{
        $cmd = $cmd->getName();
        $name = strtolower($p->getName());
        if ($cmd == "setwarp"){
            if (! isset($args[0])){
                $p->sendMessage("/setwarp (warpName))");
                return true;
            }
        if(isset($this->db["world"][$args[0]])){
            $p->sendMessage("This warp already exists");
            return true;
        }
        $a = $p->getPosition();
        $world = $p->getWorld()->getFolderName();
        $x = (int)$a->getX();
        $y = (int)$a->getY();
        $z = (int)$a->getZ();
        $this->db["워프이름"][$args[0]] = $args[0];
        $this->db["world"][$args[0]] = $world;
        $this->db["X"][$args[0]] = $x;
        $this->db["Y"][$args[0]] = $y;
        $this->db["Z"][$args[0]] = $z;
        $this->save();
        $p->sendMessage("Added warp $args[0]}");
    }
    if ($cmd == "Warp"){
        if (!isset($args[0])){
            $p->sendMessage("/warp (warpName)");
            return true;
        }
        if(! isset($this->db["world"][$args[0]])){
            $p->sendMessage("Warp does not exist. {$args[0]}");
            return true;
        }
        if ($this->getServer()->getWorldManager()->getWorldByName($this->db["world"][$args[0]]) == null){
            $p->sendMessage("There is a problem with the world you are trying to move.");
            return true;
        }
        $x = $this->db["X"][$args[0]];
        $y = $this->db["Y"][$args[0]];
        $z = $this->db["Z"][$args[0]];
        $world = $this->db["world"][$args[0]];

        $p->teleport(new Position(floatval($x), floatval($y), floatval($z), $this->getServer()->getWorldManager()->getWorldByName($world)));
        $p->sendMessage("Warp teleport {$args[0]}");
    }
    if ($cmd == "delwarp"){
        if (!isset($args[0])){
            $p->sendMessage("/delwarp (warpName)");
            return true;
        }
        if(! isset($this->db["world"][$args[0]])){ 
            $p->sendMessage("The warp cannot be removed.");
            return true;
        }
        unset($this->db["world"][$args[0]]);
        unset ($this->db["X"][$args[0]]);
        unset($this->db["Y"][$args[0]]);
        unset($this->db["Z"][$args[0]]);
        unset($this->db["워프이름"][$args[0]]);
        $this->save();
        $p->sendMessage("{$args[0]} Warp Remove");
    }
    return true;
}
}
