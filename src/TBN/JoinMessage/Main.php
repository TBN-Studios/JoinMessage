<?php

declare(strict_types=1);

namespace TBN\JoinMessage;

use pocketmine\lang\Translatable;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class PingUpdateTask extends Task {

    private $plugin;

    public function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
    }
        public function onRun(): void {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $ping = $player->getNetworkSession()->getPing();
            $scoreTag = C::WHITE . "PING: " . $ping . "ms";
            $this->updatePlayerScoreTag($player, $scoreTag);
        }
    }

    private function updatePlayerScoreTag(Player $player, string $scoreTag): void {
        $player->setScoreTag($scoreTag);
    }
}

    class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->getScheduler()->scheduleRepeatingTask(new PingUpdateTask($this), 20);
    }

    public function onJoin(PlayerJoinEvent $event): void {
    $player = $event->getPlayer();
    $playerName = $player->getName();
    $config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
        "join_message" => "\033[31m>> \033[91m{player} \033[33mhas joined Havoc Factions! \033[31m<<",
        "title" => "\033[33mW\033[5mel\033[0mcome, \033[91m{player}",
        "messages" => [
            "\033[33mWelcome to Havoc Factions, \033[91m{player}!",
            "",
            "\033[33mServer Information:",
            "  - \033[31mDiscord: \033[91m<discord-link>",
            "  - \033[31mStore: \033[91m<store-link>",
            "",
            "\033[33mPlayer Information:",
            "  - \033[31mYour Username: \033[91m{player}",
            "  - \033[31mYour Current Ping: \033[91{ping}ms",
            "  - \033[31mYour Health: \033[91m{health}/{max_health}",
            "",
        ]
    ]);
    $joinMessage = $config->get("join_message");
    $joinMessage = str_replace("{player}", $playerName, $joinMessage);
    $event->setJoinMessage($joinMessage);
    $title = $config->get("title");
    $title = str_replace("{player}", $playerName, $title);
    $player->sendTitle(
        $title,
        "",
        5,
        20,
        5
    );
    $messages = $config->get("messages");
    foreach ($messages as $message) {
        $message = str_replace("{player}", $playerName, $message);
        $message = str_replace("{ping}", $player->getNetworkSession()->getPing() . "ms", $message);
        $message = str_replace("{health}", (string) $player->getHealth(), $message);
        $message = str_replace("{max_health}", (string) $player->getMaxHealth(), $message);
        $player->sendMessage($message);
    	}
    }
}