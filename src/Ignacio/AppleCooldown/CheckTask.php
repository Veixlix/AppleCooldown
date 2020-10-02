<?php
namespace Ignacio\AppleCooldown;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class CheckTask extends Task{

	public $main;

	public function __construct(Main $main)
	{
		$this->main = $main;
	}

	public function onRun(int $currentTick)
	{
		$config = $this->main->getConfig();
		$playersInCoolDown = $config->get("Players-in-cooldown");
		foreach ($playersInCoolDown as $playerName){
			$playerFile = new Config($this->main->getDataFolder()."players/".$playerName.".yml");
		    $timeLeft = $playerFile->get("time-left");
		    if($timeLeft > 0){
		    	$timeLeft--;
		    	$playerFile->set("time-left", $timeLeft);
		    	$playerFile->save();
			} elseif($timeLeft <= 0){
		    	unset($playersInCoolDown[array_search($playerName, $playersInCoolDown)]);
		    	$config->set("Players-in-cooldown", $playersInCoolDown);
		    	$config->save();
			}
		}
	}
}