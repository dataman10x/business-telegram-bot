<?php
namespace App\Http\Controllers\API\Telegram;

use App\Classes\Parser;
use App\Http\Controllers\API\Telegram\Keyboards\OnetimeKeyboards;

class HelpController
{
    private $parser;
    private $onetimeKeyboard;
    private $data;
    private $content;
    private $userId;
    private $userFirstname;
    private $userUsername;
    private $chatId;
    private $replyToMessageId;
    private $messageCommandText;
    private $messageTime;
    private $messageTimeFormatted;

    public function __construct($data)
    {
        $text = "We could not find the exact help for you. You may consider sending us a <b>Direct Message</b> instead";
        $this->data = $data;
        $this->parser = new Parser;
        $this->onetimeKeyboard = new OnetimeKeyboards;

        $this->userId = $this->data['user-id'];
        $this->userFirstname = $this->data['user-firstname'];
        $this->userUsername = $this->data['user-username'];
        $this->chatId = $this->data['chat-id'];
        $this->replyToMessageId = $this->data['message-id'];
        $this->messageCommandText = $this->data['message-command'];
        $this->messageTime = $this->data['message-date'];
        $this->messageTimeFormatted = $this->parser->formatUnixTime($this->messageTime);

        $this->content = [
            'text' => $text,
            'chat_id' => $this->chatId
        ];
    }

    public function index()
    {
        if($this->messageCommandText == config('telegram.commands.help.name') || $this->messageCommandText == config('telegram.commands.help.botref')) {
            $this->intro();
        }

        $result = app('telegram_bot')->sendMessage( $this->content);
        return response()->json($result, 200);
    }

    public function intro($type = null)
    {
        $subs = [];
        array_push($subs, config('telegram.help.commands'));
        array_push($subs, config('telegram.help.find_biz'));
        array_push($subs, config('telegram.help.dm'));

        $subStr = implode("\n\n", $subs);
        $text = "<b>Help Center</b>\n\n$subStr";

        $keyboardBuilder = $this->onetimeKeyboard->aboutOnetimeKeyboard();

        $data = [
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboardBuilder
        ];

        $this->content = [
            ...$this->content,
            ...$data
        ];
    }
}

