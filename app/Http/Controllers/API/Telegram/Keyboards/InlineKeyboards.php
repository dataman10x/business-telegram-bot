<?php

namespace App\Http\Controllers\API\Telegram\Keyboards;

class InlineKeyboards
{

    public function startInlinekeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.start.label'),
                    '',
                    config('telegram.commands.start.name')
                )
            ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function adminMainMenuInlinekeyboard()
    {
        $adminName = config('telegram.commands.admin.name');

        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    'Users',
                    '',
                    $adminName . '.' . config('constants.admin_commands.user_view')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    'Businesses',
                    '',
                    $adminName . '.' . config('constants.admin_commands.biz_view')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    'Unread DM',
                    '',
                    $adminName . '.' . config('constants.admin_commands.dm_unread')
                )
                ],
                [
                    app('telegram_bot')->buildInlineKeyboardButton(
                        'Stats',
                        '',
                        $adminName . '.' . config('constants.admin_commands.stats_view')
                    ),
                    app('telegram_bot')->buildInlineKeyboardButton(
                        'Wait List',
                        '',
                        $adminName . '.' . config('constants.admin_commands.waitlist_view')
                    ),
                    app('telegram_bot')->buildInlineKeyboardButton(
                        'Reviews',
                        '',
                        $adminName . '.' . config('constants.admin_commands.review_view')
                    )
                    ],
                    [
                        app('telegram_bot')->buildInlineKeyboardButton(
                            'Polls',
                            '',
                            $adminName . '.' . config('constants.admin_commands.poll_view')
                        ),
                        app('telegram_bot')->buildInlineKeyboardButton(
                            'Broadcasts',
                            '',
                            $adminName . '.' . config('constants.admin_commands.broadcast_view')
                        )
                    ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function adminInlinekeyboard($isHome = true, $loadName = null, $loadLabel = null, $backName = null, $backLabel = 'Back')
    {
        $adminName = config('telegram.commands.admin.name');
        $getExitName = $adminName .  '.exit';
        $getLoadName = $adminName .  '.' . $loadName;
        $getBackName = $adminName .  '.' . $backName;

        $sub = [];

        $homeBtn = app('telegram_bot')->buildInlineKeyboardButton(
            config('telegram.commands.admin.label'),
            '',
            $adminName . '.panel'
        );

        $backBtn = app('telegram_bot')->buildInlineKeyboardButton(
            $backLabel,
            '',
            $getBackName
        );

        $loadBtn = app('telegram_bot')->buildInlineKeyboardButton(
            $loadLabel,
            '',
            $getLoadName
        );

        $exitBtn = app('telegram_bot')->buildInlineKeyboardButton(
            'Exit',
            '',
            $getExitName
        );

        if($isHome) {
            array_push($sub, $homeBtn);
        }

        if(!is_null($backName) && !is_null($backLabel)) {
            array_push($sub, $backBtn);
        }

        if(!is_null($loadName) && !is_null($loadLabel)) {
            array_push($sub, $loadBtn);
        }

        array_push($sub, $exitBtn);

        $buttonArr = [
            $sub
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function businessViewInlinekeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.single_business.label'),
                    '',
                    config('telegram.commands.single_business.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.list.label'),
                    '',
                    config('telegram.commands.list.name')
                )
            ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function yesNoInlinekeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.yes.label'),
                    '',
                    config('telegram.commands.yes.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.no.label'),
                    '',
                    config('telegram.commands.no.name')
                )
            ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function prevNextInlinekeyboard($isNext = true, $isPrev = false, $isCancel = false)
    {
        $rowArr = [];

        if($isPrev) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.prev.label'),
                '',
                config('telegram.commands.prev.name')
            );
            array_push($rowArr, $button);
        }

        if($isCancel) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.stop.label'),
                '',
                config('telegram.commands.stop.name')
            );
            array_push($rowArr, $button);
        }

        if($isNext) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.next.label'),
                '',
                config('telegram.commands.next.name')
            );
            array_push($rowArr, $button);
        }
        $buttonArr = [
            $rowArr
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function mainInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.start.label'),
                    '',
                    config('telegram.commands.start.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.stop.label'),
                    '',
                    config('telegram.commands.stop.name')
                )
            ],
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.bots.label'),
                    '',
                    config('telegram.commands.bots.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.bizapps.label'),
                    '',
                    config('telegram.commands.bizapps.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.cbtapps.label'),
                    '',
                    config('telegram.commands.cbtapps.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.payments.label'),
                    '',
                    config('telegram.commands.payments.name')
                )
            ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function contactsInlinekeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.call.label'),
                    '',
                    config('telegram.commands.call.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.mail.label'),
                    '',
                    config('telegram.commands.mail.name')
                )
                ],
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.dm.label'),
                    '',
                    config('telegram.commands.dm.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.socials.label'),
                    '',
                    config('telegram.commands.socials.name')
                )
            ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function productsInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.waitlist.label'),
                    '',
                    config('telegram.commands.waitlist.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.polls.label'),
                    '',
                    config('telegram.commands.polls.name')
                )
            ],
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.bots.label'),
                    '',
                    config('telegram.commands.bots.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.bizapps.label'),
                    '',
                    config('telegram.commands.bizapps.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.cbtapps.label'),
                    '',
                    config('telegram.commands.cbtapps.name')
                ),
            ],
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.payments.label'),
                    '',
                    config('telegram.commands.payments.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.crypto.label'),
                    '',
                    config('telegram.commands.crypto.name')
                )
            ]
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function waitlistInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.join_waitlist.label'),
                    '',
                    config('telegram.commands.join_waitlist.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function waitlistSuccessInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.polls.label'),
                    '',
                    config('telegram.commands.polls.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.menu.label'),
                    '',
                    config('telegram.commands.menu.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function pollsInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.polls.label'),
                    '',
                    config('telegram.commands.polls.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.waitlist.label'),
                    '',
                    config('telegram.commands.waitlist.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function pollsStatInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.polls_stats.label'),
                    '',
                    config('telegram.commands.polls_stats.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function pollsSuccessInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.polls_stats.label'),
                    '',
                    config('telegram.commands.polls_stats.name')
                ),
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.waitlist.label'),
                    '',
                    config('telegram.commands.waitlist.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function businessSuccessInlineKeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.business.label'),
                    '',
                    config('telegram.commands.business.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function paginationInlinekeyboard(int $len, int $current, bool $isCancel = false)
    {
        $rowArr = [];

        if($len > 0) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.first.label'),
                '',
                config('telegram.commands.first.name')
            );
            array_push($rowArr, $button);
        }

        if($current > $len - 1) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.prev.label'),
                '',
                config('telegram.commands.prev.name')
            );
            array_push($rowArr, $button);
        }

        if($isCancel) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.stop.label'),
                '',
                config('telegram.commands.stop.name')
            );
            array_push($rowArr, $button);
        }

        if($len - 1 > $current) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.next.label'),
                '',
                config('telegram.commands.next.name')
            );
            array_push($rowArr, $button);
        }

        if($len > $current) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.last.label'),
                '',
                config('telegram.commands.last.name')
            );
            array_push($rowArr, $button);
        }

        $buttonArr = [
            $rowArr
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function paginationSingleViewInlinekeyboard(int $len, int $current, bool $isView = true, int $limit = 0)
    {
        $rowArr = [];

        $current = $current + $limit;
        $current = $current > $len? $len: $current;

        if($len > 0) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.first.label'),
                '',
                config('telegram.commands.first.name')
            );
            array_push($rowArr, $button);
        }

        if($current <= $len && $current !== 1) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.prev.label'),
                '',
                config('telegram.commands.prev.name')
            );
            array_push($rowArr, $button);
        }

        if($current < $len) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.next.label'),
                '',
                config('telegram.commands.next.name')
            );
            array_push($rowArr, $button);
        }

        if($len > $current) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.last.label'),
                '',
                config('telegram.commands.last.name')
            );
            array_push($rowArr, $button);
        }

        $buttonArr = [
            $rowArr
        ];

        if($isView) {
            $button = app('telegram_bot')->buildInlineKeyboardButton(
                config('telegram.commands.view.label'),
                '',
                config('telegram.commands.view.name')
            );

            $buttonArr = [
                [
                    $button
                ],
                $rowArr
            ];
        }

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }

    public function viewInlinekeyboard()
    {
        $buttonArr = [
            [
                app('telegram_bot')->buildInlineKeyboardButton(
                    config('telegram.commands.view.label'),
                    '',
                    config('telegram.commands.view.name')
                )
            ],
        ];

        $builder = app('telegram_bot')->buildInlineKeyBoard($buttonArr);

        return $builder;
    }
}
