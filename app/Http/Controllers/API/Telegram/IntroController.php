<?php
namespace App\Http\Controllers\API\Telegram;

use App\Classes\Parser;
use App\Http\Controllers\API\Telegram\Keyboards\OnetimeKeyboards;
use App\Http\Controllers\API\UserAccountController;

class IntroController
{
    private $parser;
    private $cachePrefix;
    private $userAccount;
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
        $this->cachePrefix = config('constants.cache_prefix.user');
        $text = 'I am so glad you are here.';

        $this->data = $data;
        $this->parser = new Parser;
        $this->userAccount = new UserAccountController;
        $this->onetimeKeyboard = new OnetimeKeyboards;

        $this->userId = $this->data['user-id'];
        $this->userFirstname = $this->data['user-firstname'];
        $this->userUsername = $this->data['user-username'];
        $this->chatId = $this->data['chat-id'];
        $this->replyToMessageId = $this->data['message-id'];
        $this->messageCommandText = $this->data['message-command'];
        $this->messageCommandText = $this->data['message-text'];
        $this->messageTime = $this->data['message-date'];

        $this->messageTimeFormatted = '';
        try {
            $this->messageTimeFormatted = $this->parser->formatUnixTime($this->messageTime);
        } catch (\Throwable $th) {
            //throw $th;
        }

        $this->content = [
            'text' => $text,
            'chat_id' => $this->chatId
        ];
    }

    public function newUser()
    {
        $isRegistered = $this->userAccount->register($this->userId, $this->userFirstname, $this->userUsername);

        if($isRegistered === true) {
            $this->parser->cachePut($this->userId, $this->messageTimeFormatted, $this->cachePrefix, $this->parser->addYears(1));
        }

        $text = config('telegram.messages.intro_for_new_user');

        $keyboardBuilder = $this->onetimeKeyboard->mainOnetimeKeyboard();

        $data = [
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboardBuilder
        ];

        $this->content = [
            ...$this->content,
            ...$data
        ];

        $result = app('telegram_bot')->sendMessage( $this->content);
        return response()->json($result, 200);
    }

    public function regularUser()
    {
        $text = "I am so glad you are back $this->userFirstname.\nKindly click any of the buttons and I will satisfy you.";

        $keyboardBuilder = $this->onetimeKeyboard->mainOnetimeKeyboard();

        $data = [
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboardBuilder
        ];

        $this->content = [
            ...$this->content,
            ...$data
        ];
        // save state
        $this->userAccount->setUserInputFalse($this->userId);

        $result = app('telegram_bot')->sendMessage( $this->content);
        return response()->json($result, 200);
    }
}

