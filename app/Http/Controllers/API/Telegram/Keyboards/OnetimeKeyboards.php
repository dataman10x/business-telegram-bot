<?php

namespace App\Http\Controllers\API\Telegram\Keyboards;

class OnetimeKeyboards
{

    public function mainOnetimeKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.menu.name')),
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.demo.name')),
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.products.name')),
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.contacts.name'))
            ]
        ];

        $builder = app('telegram_bot')->buildKeyBoard($buttonArr);

        return $builder;
    }

    public function cancelOnetimeKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.stop.name'))
            ]
        ];

        $builder = app('telegram_bot')->buildKeyBoard($buttonArr);

        return $builder;
    }

    public function aboutOnetimeKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.about.name')),
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.stats.name')),
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.search.name')),
                app('telegram_bot')->buildKeyboardButton(config('telegram.commands.help.name'))
            ]
        ];

        $builder = app('telegram_bot')->buildKeyBoard($buttonArr);

        return $builder;
    }

    public function paginationInlinekeyboard(int $len, int $current, int $limit = 10, bool $isView = false)
    {
        $rowArr = [];

        if($len > 0) {
            $button = app('telegram_bot')->buildKeyboardButton(config('telegram.commands.first.name'));
            array_push($rowArr, $button);
        }

        if($current > $limit - 1) {
            $button = app('telegram_bot')->buildKeyboardButton(config('telegram.commands.prev.name'));
            array_push($rowArr, $button);
        }

        if($isView) {
            $button = app('telegram_bot')->buildKeyboardButton(config('telegram.commands.view.name'));
            array_push($rowArr, $button);
        }

        if($len - 1 > $current) {
            $button = app('telegram_bot')->buildKeyboardButton(config('telegram.commands.next.name'));
            array_push($rowArr, $button);
        }

        if($len > $current) {
            $button = app('telegram_bot')->buildKeyboardButton(config('telegram.commands.last.name'));
            array_push($rowArr, $button);
        }

        $buttonArr = [
            $rowArr
        ];

        $builder = app('telegram_bot')->buildKeyBoard($buttonArr);

        return $builder;
    }

}
