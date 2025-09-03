<?php

namespace App\Http\Controllers\API;

use App\Classes\Parser;
use App\Http\Controllers\API\Telegram\AboutController;
use App\Http\Controllers\API\Telegram\AdminController;
use App\Http\Controllers\API\Telegram\BusinessesController;
use App\Http\Controllers\API\Telegram\BusinessOwnerController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Telegram\ContactsController;
use App\Http\Controllers\API\Telegram\DemoController;
use App\Http\Controllers\API\Telegram\ErrorController;
use App\Http\Controllers\API\Telegram\HelpController;
use App\Http\Controllers\API\Telegram\IntroController;
use App\Http\Controllers\API\Telegram\MenuController;
use App\Http\Controllers\API\Telegram\PollsController;
use App\Http\Controllers\API\Telegram\ProductsController;
use App\Http\Controllers\API\Telegram\SearchController;
use App\Http\Controllers\API\Telegram\StatsController;
use App\Models\User;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    private $content;
    /**
     *
     * @return void
     */
    public function inbound(Request $request)
    {
        if(is_null($request)) {
            return response()->json($request, 200);
        }

        $parser = new Parser;
        $userAccount = new UserAccountController;
        // $parser->log($request->all());

        $inboundArr = $parser->telegramInbound($request);
        // $parser->log($inboundArr);

        $errorC = new ErrorController($inboundArr);

        // maintence mode
        if(env('MAINTENANCE_MODE')) {
            $offlineText = config('telegram.messages.offline');
            return $errorC->error($offlineText);
        }

        try {
            $chatId = $inboundArr['chat-id'];
            $userId = $inboundArr['user-id'];
            $replyToMessageId = $inboundArr['message-id'];
            $messageCommandText = $inboundArr['message-command'];
            $messageText = $inboundArr['message-text'];
            $pollOptionIds = $inboundArr['poll-option-ids'];
            $isBotCommand = $parser->isTelegramCommand($messageCommandText);

            // define defaults
            $cacheUserPrefix = config('constants.cache_prefix.user');
            $cacheSlidePrefix = config('constants.cache_prefix.slide');
            $cachePaginatorPrefix = config('constants.cache_prefix.paginator');
            $cacheViewPrefix = config('constants.cache_prefix.view');

            $text = "TestDataman Bot 🤖 may not have understood your request";

            // detect bots
            if($inboundArr['is-bot'] && !$inboundArr['is-callback']) {
                $text = "Oops! Bots 🤖 are not recognized by me.";
                $content = [
                    'text' => $text,
                    'chat_id' => $userId,
                    'reply_parameters'  => [
                        'message_id'    => $replyToMessageId
                    ]
                ];
                $result = app('telegram_bot')->sendMessage($content);
                return response()->json($result, 200);
            }

            $this->content = [
                'text' => $text,
                'chat_id' => $userId,
                'reply_parameters'  => [
                    'message_id'    => $replyToMessageId
                ]
            ];

            // set if prompted input
            $user = $userAccount->info($userId);
            $inputType = null;
            $inputInActive = false;
            try {
                $userInputs = $user->inputs;
                $inputType = $userInputs->type;
                $inputInActive = $userInputs->is_active;
            } catch (\Throwable $th) {
                //throw $th;
            }

            // update visits
            $userAccount->updateTotalVisits($userId);

            // prompt user
            $chatAction = config('telegram.chatactions.text');
            $this->content['action'] = $chatAction;
            // app('telegram_bot')->sendChatAction($this->content);

            /**
             * setup long permissions
             */
            $commandPaginator = false;
            if($messageCommandText == config('telegram.commands.prev.name') || $messageCommandText == config('telegram.commands.prev.botref')) {
                $commandPaginator = true;
            }

            if($messageCommandText == config('telegram.commands.next.name') || $messageCommandText == config('telegram.commands.next.botref')) {
                $commandPaginator = true;
            }

            if($messageCommandText == config('telegram.commands.first.name') || $messageCommandText == config('telegram.commands.first.botref')) {
                $commandPaginator = true;
            }

            if($messageCommandText == config('telegram.commands.last.name') || $messageCommandText == config('telegram.commands.last.botref')) {
                $commandPaginator = true;
            }

            // update user cache
            $hashId = $parser->encoder($userId);
            $isUserCache = $parser->cacheHas($userId??'', $cacheUserPrefix);
            $user = User::find($hashId);
            if(!is_null($user) && !$isUserCache) {
                $parser->cachePut($userId, $parser->dateNow(), $cacheUserPrefix, $parser->addYears(1));
            }

            if(is_null($user)) {
                // first time user
                $introC = new IntroController($inboundArr);
                $introC->newUser();
            }
            else if($messageCommandText == config('telegram.commands.start.name') || $messageCommandText == config('telegram.commands.start.botref')) {
                // intro
                    $introC = new IntroController($inboundArr);
                    $introC->regularUser();
            }
            else if($messageCommandText == config('telegram.commands.stop.name') || $messageCommandText == config('telegram.commands.stop.botref')) {
                // remove caches
                if($parser->cacheHas($userId??'', $cacheSlidePrefix)) {
                    $parser->cacheRemove(
                        $userId,
                        config('constants.cache_prefix.slide')
                    );
                }

                // stop menu
                    $stopC = new MenuController($inboundArr);
                    $stopC->index();
            }
            else if($messageCommandText == config('telegram.commands.menu.name') || $messageCommandText == config('telegram.commands.menu.botref')) {
                // main menu
                    $menuC = new MenuController($inboundArr);
                    $menuC->index();
            }
            else if($messageCommandText == config('telegram.commands.about.name') || $messageCommandText == config('telegram.commands.about.botref')) {
                // about
                    $aboutC = new AboutController($inboundArr);
                    $aboutC->index();
            }
            else if($messageCommandText == config('telegram.commands.stats.name') || $messageCommandText == config('telegram.commands.stats.botref')) {
                // stats
                    $statsC = new StatsController($inboundArr);
                    $statsC->index();
            }
            else if($messageCommandText == config('telegram.commands.search.name') || $messageCommandText == config('telegram.commands.search.botref')) {
                // search
                    $searchC = new SearchController($inboundArr);
                    $searchC->intro();
            }
            else if($messageCommandText == config('telegram.commands.help.name') || $messageCommandText == config('telegram.commands.help.botref')) {
                // help
                    $helpC = new HelpController($inboundArr);
                    $helpC->index();
            }
            else if(($messageCommandText == config('telegram.commands.demo.name') || $messageCommandText == config('telegram.commands.demo.botref')) ||
                $parser->isTelegramStepwise($messageCommandText, config('telegram.commands.demo.name') . '.')) {
                // Demo stepwise actions
                    $demoC = new DemoController($inboundArr);
                    $demoC->index(config('telegram.commands.demo.name'));
            }
            else if(($messageCommandText == config('telegram.commands.business.name') || $messageCommandText == config('telegram.commands.business.botref')) ||
                $parser->isTelegramStepwise($messageCommandText, config('telegram.commands.business.name') . '.')) {
                // Business stepwise actions
                    $demoC = new BusinessesController($inboundArr);
                    $demoC->index(config('telegram.commands.business.name'), null);
            }
            else if(($messageCommandText == config('telegram.commands.owner.name') || $messageCommandText == config('telegram.commands.owner.botref')) ||
                $parser->isTelegramStepwise($messageCommandText, config('telegram.commands.owner.name') . '.')) {
                // Business stepwise actions
                    $demoC = new BusinessOwnerController($inboundArr);
                    $demoC->index(config('telegram.commands.owner.name'));
            }
            else if($messageCommandText == config('telegram.commands.products.name') || $messageCommandText == config('telegram.commands.products.botref') ||
                $messageCommandText == config('telegram.commands.waitlist.botref') ||
                $parser->isTelegramStepwise($messageCommandText, config('telegram.commands.products.name') . '.')) {
                // waitlist actions
                    $productC = new ProductsController($inboundArr);
                    $productC->index();
            }
            else if($messageCommandText == config('telegram.commands.polls.name') || $messageCommandText == config('telegram.commands.polls.botref') ||
                    !is_null($pollOptionIds) ||
                    $parser->isTelegramStepwise($messageCommandText, config('telegram.commands.polls.name') . '.')) {
                // polls actions
                    $productC = new PollsController($inboundArr);
                    $productC->index();
            }
            else if(($messageCommandText == config('telegram.commands.contacts.name') || $messageCommandText == config('telegram.commands.contacts.botref')) ||
                $parser->isTelegramStepwise($messageCommandText, config('telegram.commands.contacts.name') . '.')) {
                // Contacts stepwise actions
                    $contactC = new ContactsController($inboundArr);
                    $contactC->index();
            }
            else if($parser->isTelegramStepwise($messageCommandText, config('telegram.commands.admin.name') . '.')) {
                // Admin stepwise actions
                    $adminC = new AdminController($inboundArr);
                    $adminC->index();
            }
            else if(($commandPaginator && $parser->cacheHas($userId??'', $cachePaginatorPrefix)) ||
                    $parser->isTelegramStepwise($messageCommandText, 'view.single')) {
                    $viewType = null;
                    $viewCacheData = null;


                    if($messageCommandText == config('telegram.commands.single_business.name')) {
                        $viewCacheData = config('telegram.commands.single_business.name');
                    }

                    if($messageCommandText == config('telegram.commands.list.name')) {
                        $viewCacheData = config('telegram.commands.list.name');
                    }

                    if($messageCommandText == config('telegram.commands.view.name')) {
                        $viewCacheData = config('telegram.commands.view.name');
                    }

                    $viewType = $parser->cacheGet($userId??'', $cacheViewPrefix);

                    if(!is_null($viewCacheData)) {
                        $parser->cachePut(
                            $userId,
                            $viewCacheData,
                            $cacheViewPrefix,
                            $parser->addMinutes(5)
                        );
                        $viewType = $viewCacheData;
                    }

                    $slideType = $parser->cacheGet($userId??'', $cachePaginatorPrefix);

                    if($parser->isTelegramStepwise($slideType, config('telegram.commands.business.name')) ||
                        $parser->isTelegramStepwise($messageCommandText, 'view.single')) {
                        $bizC = new BusinessesController($inboundArr);
                        $bizC->index($slideType, $viewType);
                    }
                    else {
                        $error = config('telegram.messages.error_pagination');
                        $errorC->error($error);
                    }
            }
            else if($messageCommandText == config('telegram.commands.prev.name') || $messageCommandText == config('telegram.commands.next.name')) {

                // $parser->log("C ERROR: $messageCommandText");
                if($parser->cacheHas($userId??'', $cacheSlidePrefix)) {
                    $slideType = $parser->cacheGet($userId??'', $cacheSlidePrefix);
                    $viewType = $parser->cacheGet($userId??'', $cacheViewPrefix);

                    if($parser->isTelegramStepwise($slideType, config('telegram.commands.demo.name'))) {
                        $demoC = new DemoController($inboundArr);
                        $demoC->index($slideType);
                    }
                    else if($parser->isTelegramStepwise($slideType, config('telegram.commands.owner.name'))) {
                        $ownerC = new BusinessOwnerController($inboundArr);
                        $ownerC->index($slideType, null);
                    }
                    else if($parser->isTelegramStepwise($slideType, config('telegram.commands.business.name'))) {
                        $bizC = new BusinessesController($inboundArr);
                        $bizC->index($slideType, null);
                    }
                } else {
                    $error = config('telegram.messages.error_slides');
                    $errorC->error($error);
                }
            }
            else {
                if($isBotCommand) {
                    $cmdArr = $parser->telegramBotCommandArray();
                    $errorText = "An Error occured";

                    if(array_search($messageCommandText, $cmdArr) !== false) {
                        // not assigned bot commands
                        $errorText = "Not Assigned Bot Command: $messageCommandText";
                    } else {
                        // bot commands not recognized
                        $errorText = "Unrecognized Bot Command: $messageCommandText";
                    }
                    $parser->log($errorText);
                    $errorC->error($errorText);

                } else if($inputInActive) {
                    // handle all prompted user inputs, media, admin actions received
                    if($parser->isTelegramStepwise($inputType, config('telegram.commands.demo.name'))) {
                        $demoC = new DemoController($inboundArr);
                        $demoC->inputHandler($inputType);
                    }

                    if($parser->isTelegramStepwise($inputType, config('telegram.commands.dm.name'))) {
                        $contactC = new ContactsController($inboundArr);
                        $contactC->inputHandler($inputType);
                    }

                    if($parser->isTelegramStepwise($inputType, config('telegram.commands.owner.name'))) {
                        $ownerC = new BusinessOwnerController($inboundArr);
                        $ownerC->inputHandler($inputType);
                    }

                    if($parser->isTelegramStepwise($inputType, config('telegram.commands.business.name'))) {
                        $ownerC = new BusinessesController($inboundArr);
                        $ownerC->inputHandler($inputType);
                    }

                    if($parser->isTelegramStepwise($inputType, config('telegram.commands.admin.name'))) {
                        $adminC = new AdminController($inboundArr);
                        $adminC->inputHandler($inputType);
                    }

                } else {
                    // otherwords, treat as search terms
                    $stripIntType = preg_replace('/[^0-9]/', '', $messageCommandText);
                    $stripMessageInt = intval($stripIntType);

                    $searchC = new SearchController($inboundArr);

                    if(is_numeric($messageCommandText)) {
                        $bizC = new BusinessesController($inboundArr);
                        $bizC->searchById($messageCommandText);
                    }
                    else if($parser->isTelegramStepwise($messageCommandText, 'name ')) {
                        $searchC->name();
                    }
                    else if($parser->isTelegramStepwise($messageCommandText, 'info ')) {
                        $searchC->info();
                    }
                    else if($messageCommandText == 'dm') {
                        $dmC = new ContactsController($inboundArr);
                        $dmC->dm();
                    }
                    else if($parser->isTelegramStepwise($messageCommandText, 'dm all') ||
                        $parser->isTelegramStepwise($messageCommandText, 'dm reply') ||
                        $parser->isTelegramStepwise($messageCommandText, 'dm unreply')) {
                        $searchC->dm();
                    }
                    else if($parser->isTelegramStepwise($messageCommandText, 'question ')) {
                        $searchC->question();
                    }
                    else if($messageCommandText == 'admin panel') {
                        $adminC = new AdminController($inboundArr);
                        $adminC->index();
                    }
                    else {
                        $searchC->faq();
                    }
                }
            }

        } catch (\Throwable $th) {
            $parser->log("Error: $th");
            $errorC->main();
        }
    }
}
