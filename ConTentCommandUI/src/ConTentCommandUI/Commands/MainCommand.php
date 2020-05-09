<?php
declare(strict_types=1);

namespace ConTentCommandUI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use ConTentCommandUI\ConTentCommandUI;

class MainCommand extends Command
{

  protected $plugin;

  public function __construct(ConTentCommandUI $plugin)
  {
    $this->plugin = $plugin;
    parent::__construct('나침반설정', '나침반설정 명령어.', '/나침반설정');
  }

  public function execute(CommandSender $sender, string $commandLabel, array $args)
  {
    $encode = [
      'type' => 'form',
      'title' => '§l§b[나침반]',
      'content' => '§r§7버튼을 눌러주세요',
      'buttons' => [
        [
          'text' => '§l§b[추가하기]'
        ],
        [
          'text' => '§l§b[제거하기]'
        ]
      ]
    ];
    $packet = new ModalFormRequestPacket ();
    $packet->formId = 423452345;
    $packet->formData = json_encode($encode);
    $sender->sendDataPacket($packet);
  }
}
