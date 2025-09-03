<?php
namespace App\Http\Controllers\API\Telegram;

use App\Classes\Parser;
use App\Http\Controllers\API\Telegram\Keyboards\OnetimeKeyboards;

class AboutController
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
        $text = 'I could not find a match for your enquiry about us.';
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
        if($this->messageCommandText == config('telegram.commands.about.name') || $this->messageCommandText == config('telegram.commands.about.botref')) {
            $this->intro();
        }

        $result = app('telegram_bot')->sendMessage( $this->content);
        return response()->json($result, 200);
    }

    public function intro()
    {
        $botName = env('TELEGRAM_BOT_NAME');
        $botReleaseDate = env('TELEGRAM_BOT_RELEASE_DATE');
        $botCreator = AUTHOR_NAME . " (" . AUTHOR_NICKNAME . ")";
        $text = "
<b>$botName</b> was launched on <b>$botReleaseDate</b>, to test new features we plan including in Creating Bot Series. Users will have a first-hand experience with such features, before the stable versions of the Bots are released.

The list of Bots in Creating Bot Series could be found in the Bots section under /products. We will always update this list.

Creating Bot Series provide automation to various businesses, thereby giving the owners the stress-free life they deserve. <b>Stress is real</b>. We don't want you to joke with that.

Ensure you play the /demo, join our <b>Wait List</b>, and save your opinion in the <b>Billing Opinion /polls</b>.

We'll appreciate your feedback or call through our /contacts.

- Your favourite Software Engineer, $botCreator



        ";

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

