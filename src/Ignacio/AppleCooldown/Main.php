<?php
namespace Ignacio\AppleCooldown;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\GoldenAppleEnchanted;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$config = $this->getConfig();
		$config->save();
		$this->getScheduler()->scheduleRepeatingTask(new CheckTask($this), 20);
		if($config->get("cooldown-secs") == null){ $config->set("cooldown-secs", 10); $config->save(); }
		if($config->get("Players-in-cooldown") == null){ $config->set("Players-in-cooldown", []); $config->save(); }
		if(!is_dir($this->getDataFolder()."players/")) @mkdir($this->getDataFolder()."players/");
	}



	public function onConsume(PlayerItemConsumeEvent $event) {
		$item = $event->getItem();
		$p = $event->getPlayer();
		$name = $p->getName();
		if($item instanceof GoldenAppleEnchanted){

			$config = $this->getConfig();
			$playersInCoolDown = $config->get("Players-in-cooldown");
			if(!in_array($name, $playersInCoolDown)){
				$playerConfig = new Config($this->getDataFolder()."players/".$name.".yml", Config::YAML);
				$playerConfig->set("time-left", $this->getConfig()->get("cooldown-secs"));
				$playerConfig->save();
				$allPlayers = $this->getConfig()->get("Players-in-cooldown");
				$allPlayers[] = $name;
				$this->getConfig()->set("Players-in-cooldown", $allPlayers);
				$this->getConfig()->save();
			} else {
				$playerConfig = new Config($this->getDataFolder()."players/".$name.".yml", Config::YAML);
				$timeLeft = $playerConfig->get("time-left");
				$p->sendMessage("§cYou are in cooldown, and have $timeLeft seconds left");
				$event->setCancelled();
			}

		}
	}
}