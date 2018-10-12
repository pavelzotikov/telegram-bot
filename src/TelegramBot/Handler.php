<?php
declare(strict_types=1);
namespace TelegramBot;

class Handler
{
    /** @var \Memcache */
    protected $cache;

    /** @var array */
    protected $services;

    public function execute()
    {
        $this->cache = $this->getCacheInstance();

        if ($this->services) {
            foreach ($this->services as $service => $token) {
                $response = (new $service)->token($token)->execute($this);
                return $response;
            }
        }

        return null;
    }

    public function onCatcher(string $service_name, string $chat_id, string $message)
    {
        $handler_name = $this->getHandlerName();
        $cache = $this->cache->get(sprintf('Bot:Catcher:%s:%s:%s', $handler_name, $service_name, $chat_id));

        if ($cache) {
            $catcher_method = str_replace('command', 'catcher', $cache);
            return $this->{$catcher_method}($service_name, $chat_id, $message);
        }
    }

    public function startCatcher(string $service_name, string $chat_id)
    {
        $handler_name = $this->getHandlerName();
        $method_name = debug_backtrace()[1]['function'];

        if ($handler_name && $method_name) {

            $expires_hours = (24 + 3) - (int) date('H');
            $this->cache->set(sprintf('Bot:Catcher:%s:%s:%s', $handler_name, $service_name, $chat_id), $method_name, null, $expires_hours * 3600);
        }
        return;
    }

    public function stopCatcher(string $service_name, string $chat_id)
    {
        $handler_name = $this->getHandlerName();
        $method_name = debug_backtrace()[1]['function'];

        if ($handler_name && $method_name) {
            $this->cache->delete(sprintf('Bot:Catcher:%s:%s:%s', $handler_name, $service_name, $chat_id));
        }
    }

    public function statusCatcher(string $service_name, string $chat_id)
    {
        $handler_name = $this->getHandlerName();
        $cache = $this->cache->get(sprintf('Bot:Catcher:%s:%s:%s', $handler_name, $service_name, $chat_id));

        if ($cache) {
            return sprintf('Catcher «%s» запущен', $cache);
        }

        return 'Catcher остановлен';
    }

    public function getCacheInstance(): \Memcache
    {
        if (!$this->cache) {
            $this->cache = new \Memcache();
            $this->cache->addServer('localhost', 11211);
        }

        return $this->cache;

    }

    public function getHandlerName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

}