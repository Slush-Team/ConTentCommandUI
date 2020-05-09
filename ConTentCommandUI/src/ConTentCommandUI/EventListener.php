<?php
declare(strict_types=1);

namespace ConTentCommandUI;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

class EventListener implements Listener
{

  protected $plugin;

  public function __construct(ConTentCommandUI $plugin)
  {
    $this->plugin = $plugin;
  }
  public function onPacket(DataPacketReceiveEvent $event)
  {
    $packet = $event->getPacket();
    $player = $event->getPlayer();
    $name = $player->getName();
    $tag = "§l§b[나침반] §r§7";
    if ($packet instanceof ModalFormResponsePacket) {
      $id = $packet->formId;
      $data = json_decode($packet->formData, true);
      if ($id === 423452345) {
        if ($data === 0) {
          if ($player->isOp()) {
            $this->HelpNewUI($player);
            return true;
          } else {
            $player->sendMessage($tag . "권한이 없습니다.");
            return true;
          }
        }
        if ($data === 1) {
          if ($player->isOp()) {
            $this->HelpSetUI($player);
            return true;
          } else {
            $player->sendMessage($tag . "권한이 없습니다.");
            return true;
          }
        }
      }
      if ($id === 423452346) {
        if (!isset($data[0])) {
          $player->sendMessage( $tag . '해당 컨텐츠 이름을 적어주세요.');
          return;
        }
        if (!isset($data[1])) {
          $player->sendMessage( $tag . '기본 명령어를 적어주세요.');
          return;
        }
        $Chack = explode ( "/", $data[1] );
        if (isset($Chack[1])) {
          $player->sendMessage ( $tag . "/ 를 제외하고 적어주세요. " );
          return true;
        }
        if (isset($this->plugin->commanddb [$data[0]])) {
          $player->sendMessage ( $tag . "이미 있는 이름의 컨텐츠입니다. " );
          return true;
        }
        $this->plugin->commanddb [$data[0]] = $data[1];
        $this->plugin->save();
        $player->sendMessage($tag . "정상적으로 생성했습니다.");
        return true;
      }
      if ($id === 423452347) {
        if($data !== null){
          $arr = [];
          foreach($this->plugin->getLists() as $command){
            array_push($arr, $command);
          }
          $player->sendMessage($tag . "해당 컨텐츠 이용부분이 제거되었습니다.");
          unset ($this->plugin->commanddb [$arr[$data]]);
          $this->plugin->save ();
        }
      }
      if ($id === 423452348) {
        if ($data === 0) {
          $this->ContentUI($player);
          return true;
        }
        if ($data === 1) {
          $player->sendMessage ( $tag . "도우미 창을 종료했습니다." );
          return true;
        }
      }
      if ($id === 423452349) {
        if($data !== null){
          $arr = [];
          foreach($this->plugin->getLists() as $command){
            array_push($arr, $command);
          }
          $blockcommand = $this->plugin->commanddb [$arr[$data]];
          $this->plugin->CommandHelp ( $player, $blockcommand );
        }
      }
    }
  }
  public function HelpNewUI(Player $player)
  {
    $encode = [
      'type' => 'custom_form',
      'title' => '§l§b[나침반]',
      'content' => [
        [
          'type' => 'input',
          'text' => '§r§7해당 컨텐츠 이름을 적어주세요.'
        ],
        [
          'type' => 'input',
          'text' => '§r§7기본 명령어를 적어주세요.'
        ]
      ]
    ];
    $packet = new ModalFormRequestPacket ();
    $packet->formId = 423452346;
    $packet->formData = json_encode($encode);
    $player->sendDataPacket($packet);
    return true;
  }
  public function HelpSetUI(Player $player)
  {
    $arr = [];
    foreach($this->plugin->getLists() as $list){
      array_push($arr, array('text' => '- ' . $list));
    }
    $encode = [
      'type' => 'form',
      'title' => '§l§b[나침반]',
      'content' => '§r§7제거할 컨텐츠 명령어를 선책해주세요.',
      'buttons' => $arr
    ];
    $packet = new ModalFormRequestPacket();
    $packet->formId = 423452347;
    $packet->formData = json_encode($encode);
    $player->sendDataPacket($packet);
    return true;
  }
  public function MainUI(Player $player)
  {
    $encode = [
      'type' => 'form',
      'title' => '§l§b[도우미]',
      'content' => '§r§7버튼을 눌러주세요.',
      'buttons' => [
        [
          'text' => '§l§b[명령어]'
        ],
        [
          'text' => '§l§b[창종료]'
        ]
      ]
    ];
    $packet = new ModalFormRequestPacket ();
    $packet->formId = 423452348;
    $packet->formData = json_encode($encode);
    $player->sendDataPacket($packet);
    return true;
  }
  public function ContentUI(Player $player)
  {
    $arr = [];
    foreach($this->plugin->getLists() as $list){
      array_push($arr, array('text' => '- ' . $list));
    }
    $encode = [
      'type' => 'form',
      'title' => '§l§b[나침반]',
      'content' => '§r§7이용하신 컨텐츠의 명령어를 선택해주세요..',
      'buttons' => $arr
    ];
    $packet = new ModalFormRequestPacket();
    $packet->formId = 423452349;
    $packet->formData = json_encode($encode);
    $player->sendDataPacket($packet);
    return true;
  }
  public function Interact(PlayerInteractEvent $event) {
    $player = $event->getPlayer ();
    $name = $player->getName ();
    $tag = "§l§b[나침반] §r§7";
    $item = $player->getInventory ()->getItemInHand ();
    if ($item->getId () == 345) {
      $this->MainUI($player);
      return true;
    }
  }
}
