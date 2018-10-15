<?php
declare(strict_types=1);
namespace TelegramBot\Response;

class Links
{
    private $message;
    private $links;

    public function __construct(string $message, array $links)
    {
        $this->message = $message;
        $this->links = $links;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

}