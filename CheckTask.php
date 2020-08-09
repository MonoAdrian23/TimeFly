<?php

declare(strict_types=1);

namespace MonoAdrian23\TimeFly;

use pocketmine\scheduler\Task;

class CheckTask extends Task {

    /** @var Main */
    private $plugin;

    public function __construct(Main $main)
    {
        $this->plugin = $main;
    }


    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        if($this->plugin->boost){
            if(time() - $this->plugin->boost >= $this->plugin->getConfig()->getNested("fly_duration")){
                foreach ($this->plugin->getServer()->getOnlinePlayers() as $player){
                    $player->setAllowFlight(false);
                    $player->setFlying(false);
                    $player->sendMessage($this->plugin->getConfig()->getNested("messages.expire"));
                }
                $this->plugin->boost = null;
            }
        }
    }
}