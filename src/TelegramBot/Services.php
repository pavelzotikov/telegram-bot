<?php
declare(strict_types=1);
namespace TelegramBot;

class Services
{

    public $methods;
    public $commands;

    public $token;
    public $prefix = 'command';

    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function execute(Handler $handler)
    {
        $this->methods = array_filter(get_class_methods($handler), function($command) {
            return substr($command, 0, strlen($this->prefix)) === $this->prefix;
        });

        $this->commands = array_map(function($command) {
            return strtolower(substr($command, strlen($this->prefix)));
        }, $this->methods);
    }

    public function getMethods()
    {
        return $this->methods ?: [];
    }

    public function getCommands()
    {
        return $this->commands ?: [];
    }

    public function getServiceName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

}