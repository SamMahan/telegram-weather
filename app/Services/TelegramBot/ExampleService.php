<?php

namespace App\Services\TelegramBot;

use App\Services\TelegramBot\TelegramBotService;
use Illuminate\Http\Request;

class ExampleService extends TelegramBotService
{

    public static function runCommand(
        \TgBotApi\BotApiBase\BotApi $bot, 
        Request $request
    ) {
        
        $message = $request->input('message');
        $chat = $message['chat'];
        $chatId = $chat['id'];

        $bot->send(\TgBotApi\BotApiBase\Method\SendMessageMethod::create($chatId, 'howdy'));
        return true;
    }
}
