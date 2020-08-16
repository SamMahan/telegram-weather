<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Unirest;

class Controller extends BaseController
{
    public function route(Request $request) {
        try{
            $botKey = getenv('BOT_TOKEN');
            $requestFactory = new \Http\Factory\Guzzle\RequestFactory();
            $streamFactory = new \Http\Factory\Guzzle\StreamFactory();
            $client = new \Http\Adapter\Guzzle6\Client();
            $apiClient = new \TgBotApi\BotApiBase\ApiClient($requestFactory, $streamFactory, $client);
            $bot = new \TgBotApi\BotApiBase\BotApi($botKey, $apiClient, new \TgBotApi\BotApiBase\BotApiNormalizer());
            
            $this->handleCommands($request, $bot);
        } catch (\Throwable $err) {
            error_log(print_r($err, true));
        }
    }

    private function handleCommands($request, $bot) {
       
        if ($request->input('message')) {
            $message = $request->input('message');
            $sanitizedString = $this->sanitizeString($message);

            $commandArr = [
                '/example' => function($bot, $request) { \App\Services\TelegramBot\ExampleService::runCommand($bot, $request); }
            ];
            $commandArr[$sanitizedString]($bot, $request);
        }
        return;
    }

    public function sanitizeString($message){
        
        $chat = $message['chat'];
        $text = $message['text'];
        $text = ($chat['type'] === 'private') ? $text : str_replace('@'.getenv('BOT_NAME'), '', $text);
        return $text;
    }
}