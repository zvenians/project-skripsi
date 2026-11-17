<?php
namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use App\Conversations\ExampleConversation;
use BotMan\BotMan\Cache\LaravelCache;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;


class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        // Load the driver(s) you want to use
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

        $config = [
            // Your driver-specific configuration
            "telegram" => [
               "token" => "2056526181:AAEUQ-yCWce6EId9pMXcYd9RSqTNksokiSE"
            ]
        ];
        $botman = BotManFactory::create($config, new LaravelCache());

        $botman->hears('/start|start|mulai', function (BotMan $bot) {
            $user = $bot->getUser();
            $bot->reply('Halo '.$user->getFirstName().', Selamat datang di Fasilitas UNAI Telegram Bot!. ');
            $bot->startConversation(new ExampleConversation());
        })->stopsConversation();

        $botman->hears('/tempat|tempat', function (BotMan $bot) {
            $bot->startConversation(new ExampleConversation());
        })->stopsConversation();

        $botman->hears('/lapor|lapor|laporkan', function (BotMan $bot) {
            $bot->reply('Silahkan laporkan di email 1981013@unai.edu . Laporan kamu akan sangat berharga buat kemajuan bot ini.');
        })->stopsConversation();

        $botman->hears('/tentang|about|tentang', function (BotMan $bot) {
            $bot->reply('Fasilitas UNAI Telegram Bot By Kevin. Mohon maaf jika server terasa lamban, dikarenakan menggunakan free hosting .');
        })->stopsConversation();

        $botman->listen();
    }

}