<?php
declare(strict_types=1);
namespace TelegramBot\Response;

class Images
{
    private $message;
    private $images;

    public function __construct(array $images)
    {
        $this->images = $images;
    }

    public function getImages(): array
    {
        return $this->images;
    }

}