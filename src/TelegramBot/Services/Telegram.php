<?php
declare(strict_types=1);
namespace TelegramBot\Services;

use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use TelegramBot\Handler;
use TelegramBot\Model\Button;
use TelegramBot\Response\Buttons;
use TelegramBot\Response\HideButtons;
use TelegramBot\Services;

class Telegram extends Services
{
    public function execute(Handler $handler): void
    {
        parent::execute($handler);

        try {
            $_this = $this;

            $service_name = $this->getServiceName();
            $bot = new Client($this->token);

            foreach ($this->getCommands() as $index => $command) {
                $method = $this->getMethods()[$index];

                $bot->command($command, function (Message $message) use ($_this, $service_name, $handler, $bot, $method) {
                    $response = $handler->{$method}($service_name, (string) $message->getChat()->getId(), $message->getText());

                    $chat_id = (string) $message->getChat()->getId();
                    $_this->sendMessage($bot, $chat_id, $response);
                });
            }

            $bot->on(function(Update $update) use ($_this, $service_name, $handler, $bot) {

                $message = $update->getMessage();

                if (substr($message->getText(), 0, 1) === '/') {
                    $this->execute($handler);
                    return;
                }

                $response = $handler->onCatcher($service_name, (string) $message->getChat()->getId(), $message->getText());

                $chat_id = (string) $message->getChat()->getId();
                $_this->sendMessage($bot, $chat_id, $response);

            }, function(Update $update) {
                return !is_null($update->getMessage());
            });

            $bot->run();
        } catch (\TelegramBot\Api\InvalidJsonException $ignored) { }
    }

    public function sendMessage(Client $bot, string $chat_id, $response): void
    {
        switch (true) {
            case $response instanceof Buttons:
                $buttons = [];
                /** @var Button $button */
                foreach ($response->getButtons() as $button) {
                    $buttons[] = $button->getTitle();
                }

                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array_chunk($buttons, 3), true, true, true);
                $bot->sendMessage($chat_id, $response->getMessage(), null, false, null, $keyboard);
                break;

            case $response instanceof HideButtons:
                $keyboard_remove = new \TelegramBot\Api\Types\ReplyKeyboardRemove();
                $bot->sendMessage($chat_id, $response->getMessage(), null, false, null, $keyboard_remove);
                break;

            case is_string($response):
                $bot->sendMessage($chat_id, $response);
                break;
        }
    }

}