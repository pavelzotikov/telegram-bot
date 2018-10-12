<?php
declare(strict_types=1);
namespace TelegramBot\Response;

class HideButtons
{

    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

}