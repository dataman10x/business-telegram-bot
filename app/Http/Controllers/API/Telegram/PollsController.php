<?php
namespace App\Http\Controllers\API\Telegram;

use App\Classes\Parser;
use App\Http\Controllers\API\Telegram\Keyboards\InlineKeyboards;
use App\Http\Controllers\API\Telegram\Keyboards\OnetimeKeyboards;
use App\Models\OpinionPolls;
use App\Models\OpinionPollUsers;

class PollsController
{
    private $parser;
    private $onetimeKeyboard;
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
    private $pollId;
    private $pollOptionIds;

    public function __construct($data)
    {
        $text = "We could not find the exact poll for you. You may consider sending us a <b>Direct Message</b> instead";
        $this->data = $data;
        $this->parser = new Parser;
        $this->inlineKeyboard = new InlineKeyboards;
        $this->onetimeKeyboard = new OnetimeKeyboards;

        $this->userId = $this->data['user-id'];
        $this->userFirstname = $this->data['user-firstname'];
        $this->userUsername = $this->data['user-username'];
        $this->chatId = $this->data['chat-id'];
        $this->replyToMessageId = $this->data['message-id'];
        $this->messageCommandText = $this->data['message-command'];
        $this->messageTime = $this->data['message-date'];
        $this->messageTimeFormatted = '';
        $this->pollId = $this->data['poll-id'];
        $this->pollOptionIds = $this->data['poll-option-ids'];

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

    public function index()
    {
        if($this->messageCommandText == config('telegram.commands.polls_stats.name')) {
            $this->intro();
        }
        else if($this->messageCommandText == config('telegram.commands.polls.name') || $this->messageCommandText == config('telegram.commands.polls.botref')) {
            return $this->polls();
        }
        else if(!is_null($this->pollOptionIds)) {
            $this->pollsSuccess();
        }

        $result = app('telegram_bot')->sendMessage( $this->content);
        return response()->json($result, 200);
    }

    public function intro()
    {
        $text = "<b>Opinion Polls Stats</b>\n
        None is available right now.";

        $keyboardBuilder = $this->inlineKeyboard->pollsInlineKeyboard();

        $polls = OpinionPolls::all();
        $countPolls = count($polls);
        $textArr = [];
        $sub = '';
        try {
            foreach ($polls as $poll) {
                $votes = count($poll->users);
                $label = $poll->label;
                $detail = $poll->detail;
                $question = $poll->question;
                $createdAt = $poll->created_at;
                $diffDate = $this->parser->diffHumans($createdAt);
                $endStr = '';
                $endAt = $poll->end_at;
                $isExpired = $this->parser->lessThan($endAt);

                if($isExpired) {
                    $diffDateExpired = $this->parser->diffHumans($endAt);
                    $endStr = "\nEnded since $diffDateExpired";
                }

                $plural = $votes > 1? "s":'';
                $sub = "<b>$label</b>; with $votes vote$plural
$detail
<b>Question was </b> $question
<b>Created since </b>$diffDate $endStr";

                array_push($textArr, $sub);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }

        if(count($textArr)) {
            $getText = implode('\n\n', $textArr);
            $intro = "\nOnly the latest Poll will be available for voting.";
            $text = "<b>Polls found: $countPolls</b>$intro\n\n$getText";
        }

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

    public function polls()
    {
        $text = config('telegram.messages.polls');

        $keyboardBuilder = $this->inlineKeyboard->pollsSuccessInlineKeyboard();

        $countPolls = OpinionPolls::count();
        $text = "<b>Polls found: $countPolls</b>\n\nNo Active Poll was found at this moment. You may check back later.";

        if($countPolls == 0) {
            $this->content = [
                ...$this->content,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $keyboardBuilder
            ];

            $result = app('telegram_bot')->sendMessage( $this->content);
            return response()->json($result, 200);
        }

        $poll = OpinionPolls::where('end_at', '>', $this->parser->dateNow())->latest()->first();

        if(is_null($poll)) {
            $text = "No active Poll is found at this moment";
            $this->content = [
                ...$this->content,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $keyboardBuilder
            ];

            $result = app('telegram_bot')->sendMessage( $this->content);
            return response()->json($result, 200);
        }

        // check if done by user
        $hashId = $this->parser->encoder($this->userId);
        $pollUser = OpinionPollUsers::where('user_id', $hashId)->where('poll_id', $poll->id)->first();

        if(!is_null($pollUser)) {
            $label = $poll->label;
            $createdAt = $pollUser->created_at;
            $sinceStr = $this->parser->diffHumans($createdAt);
            $text = "Oops! You have attempted the: $label, since $sinceStr";

            $this->content = [
                ...$this->content,
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $keyboardBuilder
            ];
            $result = app('telegram_bot')->sendMessage( $this->content);
            return response()->json($result, 200);
        }

        try {
            $question = $poll->question;
            $detail = $poll->detail;
            $options = $poll->options;
            $type = 'regular';
            $allowMultipleAnswers = false;

            $keyboardBuilder = $this->inlineKeyboard->pollsStatInlineKeyboard();

            $this->content = [
                'chat_id' => $this->chatId,
                'question' => $question,
                'options' => $options,
                // 'explanation' => $detail,
                'type' => $type,
                'is_anonymous' => false,
                'allow_multiple_answers' => $allowMultipleAnswers,
                // 'explanation_parse_mode' => 'HTML',
            'reply_markup' => $keyboardBuilder
            ];

            $result = app('telegram_bot')->sendPoll( $this->content);

            return response()->json($result, 200);
        } catch (\Throwable $th) {
            // $this->parser->log("ERROR: $th");
        }


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

    public function pollsSuccess()
    {
        $uFirstname = $this->userFirstname;
        $text = config('telegram.messages.polls_success');

        $poll = OpinionPolls::where('end_at', '>', $this->parser->dateNow())->latest()->first();

        try {
            $label = $poll->label;
            $dbOptions = $poll->options;
            $options = $this->pollOptionIds;
            // try {
            //     if(!is_array($dbOptions)) {
            //         $options = json_decode($dbOptions);
            //     }
            // } catch (\Throwable $th) {
            //     $this->parser->log($th);
            // }
            $optionId = $options[0];
            $answer = $dbOptions[$optionId];

            $hashId = $this->parser->encoder($this->userId);

            $save = new OpinionPollUsers;
            $save->selected = $answer;
            $save->user_id = $hashId;
            $save->poll_id = $poll->id;

            if($save->save()) {
                $text = "Thank you $uFirstname, for giving your opinion on the Poll: $label.";
            }
            else {
                $text = "$uFirstname, although we received your your opinion on the Poll: $label, we could not save it.";
            }
        } catch (\Throwable $th) {
            $text = "We got a notification you did a Poll, which could not be found on our server at this moment, or has expired before you submitted your opinion.\n\n All the same, we appreciate you. Thank you.";
        }

        $this->parser->log("TEXT: $text");

        $keyboardBuilder = $this->inlineKeyboard->pollsSuccessInlineKeyboard();

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

