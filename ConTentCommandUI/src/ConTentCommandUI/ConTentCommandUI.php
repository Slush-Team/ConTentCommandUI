<?php
declare(strict_types=1);

namespace ConTentCommandUI;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use ConTentCommandUI\Commands\MainCommand;

class ConTentCommandUI extends PluginBase
{
  protected $config;
  public $db;
  private static $instance = null;

  public static function getInstance(): ConTentCommandUI
  {
    return static::$instance;
  }
  public function onLoad()
  {
    self::$instance = $this;
  }
  public function onEnable()
  {
    $this->player = new Config ($this->getDataFolder() . "players.yml", Config::YAML);
    $this->pldb = $this->player->getAll();
    $this->command = new Config ($this->getDataFolder() . "commands.yml", Config::YAML);
    $this->commanddb = $this->command->getAll();
    $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    $this->getServer()->getCommandMap()->register('ConTentCommandUI', new MainCommand($this));
  }
  public function CommandHelp($player, $blockcommand)
  {
    $this->getServer ()->getCommandMap ()->dispatch ( $player, $blockcommand );
  }
  public function getLists() : array{
    $arr = [];
    foreach($this->commanddb as $command => $v){
      array_push($arr, $command);
    }
    return $arr;
  }
  public function onDisable()
  {
    $this->save();
  }
  public function save()
  {
    $this->player->setAll($this->pldb);
    $this->player->save();
    $this->command->setAll($this->commanddb);
    $this->command->save();
  }
}
