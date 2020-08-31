<?php

namespace App\Services\TelegramBot;

use App\Services\TelegramBot\TelegramBotService;
use Illuminate\Http\Request;

use App\Services\WeatherApis\Location;

class LocationSearchService extends TelegramBotService
{
    public static function runCommand(
        \TgBotApi\BotApiBase\BotApi $bot, 
        Request $request
    ) {
        error_log("BOT");
        $message = $request->input('message');
        $chat = $message['chat'];
        $chatId = $chat['id'];
        $text = $message['text'];
        $textSplit = explode(' ', $text);
        $searchString = false;
        if (array_key_exists(1, $textSplit)) $searchString = $textSplit[1];

        if ($searchString) {
            $locationArray = Location::getLocations($searchString);
        }

        if (sizeof($locationArray) > 1) {

        }
        
        $data = [
            'reply_markup' => [
                'inline_keyboard' => $this->makeMessageKeyboard($locationArray)
            ]
        ];
        $bot->send(\TgBotApi\BotApiBase\Method\SendMessageMethod::create($chatId, 'howdy'));//, $data));
           error_log("AFTER SEND");
        return true;
    }

    private function makeMessageKeyboard($locationArr) {
        $buttonArray = [];
        foreach ($locationArr as $locationObj) {
            $buttonArray[] = [
                'text' => 'this is a test',
                'callback_data'=> [
                    'callbackType' => 'getWeatherByPostalCode',
                    'callbackPostal' => 'Postal Code Test'
                ]
            ];
        }
        return $buttonArray;
    }
}
