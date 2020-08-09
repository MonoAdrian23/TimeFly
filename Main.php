<?php

declare(strict_types=1);

namespace MonoAdrian23\TimeFly;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    /** @var \SQLite3 */
    public $db;

    public $boost = null;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("flybooster", new FlyBoosterCommand($this));
        $this->db = new \SQLite3($this->getDataFolder() . "database.db");
        $this->db->query("CREATE TABLE IF NOT EXISTS Players (uuid VARCHAR(50), username VARCHAR(50), time INTEGER DEFAULT null)");
        $this->getScheduler()->scheduleRepeatingTask(new CheckTask($this), 200);
    }

    public function onJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $uuid = $player->getUniqueId()->toString();
        $username = $player->getName();

        $this->db->query("INSERT OR IGNORE INTO Players (uuid, username) VALUES ('$uuid', '$username')");
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        if($this->boost){
            $player->setAllowFlight(false);
            $player->setFlying(false);
        }
    }


    public function onDisable()
    {
        if($this->boost){
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                $player->setAllowFlight(false);
                $player->setFlying(false);
            }
        }
    }

}
