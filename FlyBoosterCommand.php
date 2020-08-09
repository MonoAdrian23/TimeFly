<?php

declare(strict_types=1);

namespace MonoAdrian23\TimeFly;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class FlyBoosterCommand extends Command {

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->setPermission("fboost.cmd");
        parent::__construct("fboost", "FlyBooster", "/flybooster");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof Player) {
            if ($this->testPermission($sender)) {
                $config = $this->plugin->getConfig();
                if (!$this->plugin->boost) {
                    $uuid = $sender->getUniqueId()->toString();
                    $data = $this->plugin->db->query("SELECT * FROM Players WHERE uuid = '$uuid'")->fetchArray();
                    if(!$data["time"] || time() - $data["time"] >= 86400){
                        $msg = str_replace("{player}", $sender->getName(), $config->getNested("messages.announce"));
                        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
                            $player->setAllowFlight(true);
                            $player->setFlying(true);
                            $player->sendMessage($msg);
                        }
                        $this->plugin->boost = time();
                        $time = time();
                        $this->plugin->db->query("UPDATE Players SET time = '$time' WHERE uuid = '$uuid'");
                    } else {
                        $sender->sendMessage($config->getNested("messages.boost_cooldown"));
                    }
            } else {
                    $sender->sendMessage($config->getNested("messages.boost_running"));
                }
                return true;
            }
        } else {
            $sender->sendMessage("Please use this command in-game");
        }

        return true;
    }
}