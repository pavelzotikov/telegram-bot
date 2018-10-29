<?php
declare(strict_types=1);
namespace TelegramBot\Services;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use TelegramBot\Handler;
use TelegramBot\Model\Button;
use TelegramBot\Model\Image;
use TelegramBot\Model\Link;
use TelegramBot\Response\Buttons;
use TelegramBot\Response\HideButtons;
use TelegramBot\Response\Images;
use TelegramBot\Response\Links;
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

                $response = $handler->onCatcher($service_name, (string) $message->getChat()->getId(), $message->getText());

                $chat_id = (string) $message->getChat()->getId();
                $_this->sendMessage($bot, $chat_id, $response);

            }, function(Update $update) {
                return !is_null($update->getMessage());
            });

            $bot->run();
        } catch (\TelegramBot\Api\InvalidJsonException $ignored) { }
    }

    public function sendMessage($bot, $chat_id, $response, $disable_sound = true): void
    {
        /** @var BotApi $bot */
        switch (true) {
            case $response instanceof Buttons:
                $buttons = [];
                /** @var Button $button */
                foreach ($response->getButtons() as $button) {
                    $buttons[] = $button->getTitle();
                }

                $keyboard = new \TelegramBot\Api\Types\ReplyKeyboardMarkup(array_chunk($buttons, 3), true, true, true);
                $bot->sendMessage($chat_id, $response->getMessage(), 'HTML', false, null, $keyboard, $disable_sound);
                break;

            case $response instanceof Images:
                $media = new \TelegramBot\Api\Types\InputMedia\ArrayOfInputMedia();
                /** @var Image $image */
                foreach ($response->getImages() as $image) {
                    $media->addItem(new \TelegramBot\Api\Types\InputMedia\InputMediaPhoto($image->getUrl(), $image->getCaption(), 'HTML'));
                }

                $bot->sendMediaGroup($chat_id, $media, true);
                break;

            case $response instanceof Links:
                /** @var Link $link */
                foreach ($response->getLinks() as $link) {
                    $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup(
                        [[['text' => $link->getTitle(), 'url' => $link->getUrl()]]]
                    );

                    $bot->sendMessage($chat_id, $response->getMessage(), 'HTML', false, null, $keyboard, $disable_sound);
                }

                break;

            case $response instanceof HideButtons:
                $keyboard_remove = new \TelegramBot\Api\Types\ReplyKeyboardRemove();
                $bot->sendMessage($chat_id, $response->getMessage(), 'HTML', false, null, $keyboard_remove, $disable_sound);
                break;

            case is_string($response):
                $bot->sendMessage($chat_id, $response, null, false, null, null, $disable_sound);
                break;

            case is_array($response):
                foreach ($response as $item) {
                    $this->sendMessage($bot, $chat_id, $item);
                }
                break;
        }
    }

}