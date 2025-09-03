<?php
namespace App\Http\Controllers\API\Telegram;

use App\Classes\Parser;
use App\Http\Controllers\API\Telegram\Keyboards\InlineKeyboards;
use App\Models\OpinionPolls;
use App\Models\Waitlist;

class ProductsController
{
    private $parser;
    private $inlineKeyboard;
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
        $text = 'I may not understand your request about our Products & Services.';
        $this->data = $data;
        $this->parser = new Parser;
        $this->inlineKeyboard = new InlineKeyboards;

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
        switch ($this->messageCommandText) {
            case config('telegram.commands.products.name'):
                $this->intro();
                break;
            case config('telegram.commands.products.botref'):
                $this->intro();
                break;
            case config('telegram.commands.waitlist.name'):
                $this->waitlist();
                break;
            case config('telegram.commands.waitlist.botref'):
                $this->waitlist();
                break;
            case config('telegram.commands.join_waitlist.name'):
                $this->waitlistSuccess();
                break;
            case config('telegram.commands.bots.name'):
                $this->bots();
                break;
            case config('telegram.commands.bizapps.name'):
                $this->bizapps();
                break;
            case config('telegram.commands.cbtapps.name'):
                $this->cbtapps();
                break;
            case config('telegram.commands.payments.name'):
                $this->payments();
                break;
            case config('telegram.commands.crypto.name'):
                $this->crypto();
                break;

            default:
            //
                break;
        }

        $result = app('telegram_bot')->sendMessage( $this->content);
        return response()->json($result, 200);
    }

    public function intro()
    {
        $text = config('telegram.messages.products');

        $keyboardBuilder = $this->inlineKeyboard->productsInlineKeyboard();

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

    public function waitlist()
    {
        $text = config('telegram.messages.waitlist');

        $keyboardBuilder = $this->inlineKeyboard->waitlistInlineKeyboard();

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

    public function waitlistSuccess()
    {
        $text = config('telegram.messages.waitlist_success');

        $listType = config('constants.waitlist_types.bot_releases');

        // save user to waitlist
        $hashId = $this->parser->encoder($this->userId);
        $waitList = Waitlist::where('user_id', $hashId)->first();
        if(is_null($waitList)) {
            $save = new Waitlist;
            $save->type = $listType;
            $save->user_id = $hashId;
            $save->save();
        } else {
            try {
                $getTime = $waitList->created_at;
                $joinedAt = $this->parser->diffHumans($getTime);
                $text = "You are already in our waiting list, <b>$joinedAt</b>. Thank you for keeping up with our updates.";
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        $keyboardBuilder = $this->inlineKeyboard->waitlistSuccessInlineKeyboard();

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

    public function bots()
    {
        $text = "We got some bots in development, and will be listed here when the stable version is launched.";

        $keyboardBuilder = $this->inlineKeyboard->productsInlineKeyboard();

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

    public function bizapps()
    {
        $text = "Our Business Apps are very user friendly and blazing fast. They will be listed here.";

        $keyboardBuilder = $this->inlineKeyboard->productsInlineKeyboard();

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

    public function cbtapps()
    {
        $text = "The CBT App we developed is fast becoming a favorite among students. The details will be included here.";

        $keyboardBuilder = $this->inlineKeyboard->productsInlineKeyboard();

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

    public function payments()
    {
        $text = "You need a very secure and fast payment option for your customers. The details will be included here.";

        $keyboardBuilder = $this->inlineKeyboard->productsInlineKeyboard();

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

    public function crypto()
    {
        $text = "Crypto is the world's gold now. Our links to earn with crypto will be included here.";

        $keyboardBuilder = $this->inlineKeyboard->productsInlineKeyboard();

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
