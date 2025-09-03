<?php
$inProduction = env('APP_ENV') === 'production';
$telegramToken= env('TELEGRAM_BOT_TOKEN');
$telegramBotPath = "https://api.telegram.org/bot$telegramToken";
$telegramFilePath = "https://api.telegram.org/file/bot$telegramToken";
$webhookUrlBase = $inProduction?env('APP_URL'):env('NGROK_URL');
$endpoint = env('TELEGRAM_BOT_ENDPOINT');
$telegramWebhookPath = "$telegramBotPath/setWebhook?url=$webhookUrlBase/$endpoint";

return [
    'in_production' => $inProduction,
    'telegram_webhook_path' => $telegramWebhookPath,
    'telegram_bot_path' => $telegramBotPath,
    'telegram_file_path' => $telegramFilePath,
    'telegram_gallery_photos_max' => 10,
    'bot_wait_time' => 1,
    'bot_wait_time_long' => 2,
    'business_per_view' => 20,
    'users_per_view' => 20,
    'business_owner_products_max' => 1,
    'business_owner_uploads_max' => 5,
    'user_roles' => [
        'admin' => 'admin',
        'user' => 'user'
    ],
    'discs' => [
        'products' => 'products',
        'site' => 'sites'
    ],
    'input_types' => [
        'text' => 'text',
        'image' => 'image',
        'video' => 'video',
        'audio' => 'audio',
        'file' => 'file'
    ],
    'cache_prefix' => [
        'cache' => 'cache_',
        'admin' => 'admin_',
        'user' => 'user_id_',
        'callback' => 'callback_',
        'slide' => 'slide_',
        'paginator' => 'paginator_',
        'view' => 'view_',
        'gallery' => 'gallery_',
        'biz' => 'biz_'
    ],
    'admin_commands' => [
        'user_view' => 'user.view',
        'biz_view' => 'biz.view',
        'biz_delete' => 'biz.delete',
        'dm_read' => 'dm.read',
        'dm_unread' => 'dm.unread',
        'dm_reply' => 'dm.reply',
        'dm_delete' => 'dm.delete',
        'stats_view' => 'stats.view',
        'review_view' => 'review.view',
        'review_activate' => 'review.activate',
        'review_deactivate' => 'review.deactivate',
        'review_delete' => 'review.delete',
        'poll_view' => 'poll.view',
        'poll_new' => 'poll.new',
        'poll_edit' => 'poll.edit',
        'poll_delete' => 'poll.delete',
        'broadcast_view' => 'broadcast.view',
        'broadcast_new' => 'broadcast.new',
        'broadcast_edit' => 'broadcast.edit',
        'broadcast_delete' => 'broadcast.delete',
        'waitlist_view' => 'waitlist.view',
        'waitlist_delete' => 'waitlist.delete'
    ],
    'waitlist_types' => [
        'bot_releases' => 'bot-releases'
    ]
];
