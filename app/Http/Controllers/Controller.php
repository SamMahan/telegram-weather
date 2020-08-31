<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Unirest;

class Controller extends BaseController
{
    public function route(Request $request) {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        error_log('BOT_ENTER');
        try{
            $botKey = getenv('BOT_TOKEN');
            $requestFactory = new \Http\Factory\Guzzle\RequestFactory();
            // $requestFactory->createRequest($method, $uri);
            $streamFactory = new \Http\Factory\Guzzle\StreamFactory();
            $client = new \Http\Adapter\Guzzle6\Client();
            $apiClient = new \TgBotApi\BotApiBase\ApiClient($requestFactory, $streamFactory, $client);
            $bot = new \TgBotApi\BotApiBase\BotApi($botKey, $apiClient, new \TgBotApi\BotApiBase\BotApiNormalizer());
            
            $this->handleCommands($request, $bot);
        } catch (\Throwable $err) {
            error_log(print_r($err->getMessage(), true));
            error_log($err->getFile());
            error_log($err->getLine());
        }
    }

    private function handleCommands($request, $bot) {
        if ($request->input('message')) {
            $message = $request->input('message');
            $sanitizedString = $this->sanitizeString($message);
            error_log($sanitizedString);
            $commandArr = [
                // '/example' => function($bot, $request) { \App\Services\TelegramBot\ExampleService::runCommand($bot, $request); }
                '/location' => function($bot, $request) { \App\Services\TelegramBot\LocationSearchService::runCommand($bot, $request); }
            ];
            if (array_key_exists($sanitizedString, $commandArr)){
                $commandArr[$sanitizedString]($bot, $request);
            }
        }
        return;
    }

    public function sanitizeString($message){
        
        $chat = $message['chat'];
        $text = $message['text'];
        $command = explode( ' ', $text)[0];
        
        $text = ($chat['type'] === 'private') ? $command : str_replace('@'.getenv('BOT_NAME'), '', $command);
        return $text;
    }
}