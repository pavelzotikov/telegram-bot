<?php
declare(strict_types=1);
namespace TelegramBot\Model;

class Button
{
    public $url;
    public $title;

    public $data;

    public function __construct(string $title, string $url = null, array $data = null)
    {
        $this->url = $url;
        $this->title = $title;

        $this->data = $data;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getData(): array
    {
        return $this->data ?: [];
    }
}