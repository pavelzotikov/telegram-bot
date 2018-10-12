<?php
declare(strict_types=1);
namespace TelegramBot\Response;

class Buttons
{
    private $message;
    private $buttons;

    public function __construct(string $message, array $buttons)
    {
        $this->message = $message;
        $this->buttons = $buttons;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

}