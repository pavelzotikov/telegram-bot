# Telegram Bot PHP Library
[PHP] Telegram Bot based on the TelegramBot/Api library

This bot is used on the telegram chanel https://t.me/bidbaits_bids

## Requirements
> PHP: >=7.1.0

### Extension
> Memcache: * (Any version)

### Dependencies
> Composer

## Install
```composer require pavelzotikov/telegram-bot```

## Init class
```
class Bot extends \TelegramBot\Handler
```
## Route
```
['GET|POST', '/bot', [Bot::class => 'execute']]
```
## Add Commands
Command `/start`:
```
public function commandStart(string $service_name, string $chat_id)
```
## Add Catcher
Catcher messages before command `/filter`:
```
public function commandFilter(string $service_name, string $chat_id)
{
    $this->startCatcher($service_name, $chat_id);
    return 'Напишите слово для фильтрации обьвлений.';
}
public function catcherFilter(string $service_name, string $chat_id, string $message)
{
    $this->stopCatcher($service_name, $chat_id);
    return sprintf('Фильтр по слову «%s» успешно добавлен.', $message);
}
```
## Add Default Catcher
Get user messages when any command catcher is off
```
public function catcherDefault(string $service_name, string $chat_id, string $message)
{
    return sprintf('Ваше сообщение: %s', $message)
}
```
## Example
```
class Bot extends \TelegramBot\Handler
{

    protected $services = [
        Telegram::class => '12345567890:XXX-XxX-XxxXxXxxXxxXXXxXXXXxXXX' // Your token
    ];

    public function commandStart(string $service_name, string $chat_id)
    {
        // Save enabled flag in database
        // <code> ... </code>

        return 'Теперь вы будете получать новые товары с сайта bidbaits.ru';
    }

    public function commandStop(string $service_name, string $chat_id)
    {
        // Save disabled flag in database
        // <code> ... </code>

        return 'Рассылка остановлена. Чтобы снова начать получать новые товары с сайта bidbaits.ru вызовите команду /start';
    }

    public function commandFilter(string $service_name, string $chat_id)
    {
        $this->commandStop($service_name, $chat_id);
        $this->startCatcher($service_name, $chat_id);

        return 'Напишите слово для фильтрации обьвлений.';
    }

    public function catcherFilter(string $service_name, string $chat_id, string $message)
    {
        $this->stopCatcher($service_name, $chat_id);
        $this->commandStart($service_name, $chat_id);

        // Save filter in database
        // <code> ... </code>

        return sprintf('Фильтр по слову «%s» успешно добавлен.', $message);
    }

    public function commandReset(string $service_name, string $chat_id)
    {
        // Reset filter in database
        // <code> ... </code>

        return 'Фильтр по слову успешно сброшен.';
    }

    public function commandHelp(string $service_name, string $chat_id)
    {
        $text = [];

        $text[] = 'Команды:';
        $text[] = '/start – запустить';
        $text[] = '/stop – остановить';
        $text[] = '/filter – фильтр по слову';
        $text[] = '/reset – сброс фильтра';
        $text[] = '/help – помощь';

        return implode("\r\n", $text);
    }

}
```
