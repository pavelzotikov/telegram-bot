<? ini_set("log_errors", 1); ?>
<? ini_set("error_log", "/tmp/php-error.log"); ?>

<? require_once 'vendor/autoload.php'; ?>

<?
    if (is_file(sprintf('%s/.env', __DIR__))) {
	$dotenv = new \Dotenv\Dotenv(__DIR__);
	$dotenv = $dotenv->load();
    } else {
	die("File not found");
    }
?>

<? $bot = new \TelegramBot\Api\Client($_SERVER["telegram_bot_token"] ?? ""); ?>
<?
    $bot->command("ping", function ($message) use ($bot) {
        $bot->sendMessage($message->getChat()->getId(), "pong!");
    });

    $bot->command("start", function ($message) use ($bot) {
        $bot->sendMessage($message->getChat()->getId(), "Don't be too aggressive, it doesn't work.");
    });

    $bot->run();
?>

<?= 'end' ?>
