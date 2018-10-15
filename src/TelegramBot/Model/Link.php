<?php
declare(strict_types=1);
namespace TelegramBot\Model;

class Link
{
    public $url;
    public $title;

    public $data;

    public function __construct(string $title, string $url = null)
    {
        $this->url = $url;
        $this->title = $title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}