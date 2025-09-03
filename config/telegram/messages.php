<?php

$botName = env('TELEGRAM_BOT_NAME');
$botCreatorName = AUTHOR_NAME;
$botCreatorNickname = AUTHOR_NICKNAME;
$botCreatorEmail = AUTHOR_EMAIL;
$botReleaseDate = env('TELEGRAM_BOT_RELEASE_DATE');

return [
    'offline' => "<b>OFF FOR MAINTENANCE</b>\n
<blockquote>I $botName  will miss your company at this time.
It is crucial I take a little Bot nap.</blockquote>

Still want to reach me? Send an email to $botCreatorEmail.
    ",
    'error_main' => "<b>Oops!</b>\n
<blockquote>Something went wrong. You may report this bug to us by specifying the exact action(s) that prompted it.</blockquote>

Report this bug by sending us a message through /contacts.
    ",
    'error_slides' => "<b>Oops!</b>\n
<blockquote>Something went wrong with the slides. You may report this bug to us by specifying the exact action(s) that prompted it.</blockquote>
    ",
    'error_pagination' => "<b>Oops!</b>\n
<blockquote>Something went wrong with the pagination. You may report this bug to us by specifying the exact action(s) that prompted it.</blockquote>
    ",
    'error_bot' => "<blockquote><b>You appear to be a 🤖Bot!</b></blockquote>
    <blockquote>I prefer interacting with real humans. Kindly excuse yourself.</blockquote>
    ",
    'stop' => "You cancelled previous operation. We hope you had a great user experience.\n
We will appreciate if you share some with us as a feedback. Use <b>Direct Message</b> through Contacts to send your message.",
    'main_menu' => "<b>Main Menu</b> for easy access.",
    'dm_success' => "We got your message. You will be notified when we respond.",
    'intro_for_new_user' => "I am <b>$botName</b>, the maiden Bot of <b>Creating Bot series</b>, developed by <b>$botCreatorName ($botCreatorNickname @MeetDatamanBot)</b>, and was released on $botReleaseDate.\n
<blockquote>I handle all your customers' needs, such as: displaying product images, sending broadcasts, replying enquiries, receiving feedbacks (text, image, audio, video), accepting payments, and setting up polls.\n
Clicking the Demo button will allow me take you through the awesome features I offer.</blockquote>",
    'products' => "Available <b>Products & Services</b>",
    'waitlist' => "We will notify all users who joined our <b>Wait List</b>, when we launch the stable versions of Bots in development.\n
Click this button to join.",
    'waitlist_success' => "You have been added to our <b>Waiting List</b>. The release of our Bot stable version will be announced soon.\n
If you have not attempted the pricing opinion poll, do so now.",
    'polls' => "Click on <b>Opinion Polls</b> to vote your preferred billing option.",
    'polls_success' => "Thank you you for participating in the <b>Billing Opinion Polls</b>. We will consider all registered options and integrate with the most fitting option(s).",
    'meet_dataman' => "Meet dataMan",
    'social_links' => "<b>Connect with me</b> on Social platforms\n
    <b><a href=\"https://web.facebook.com/metaversedataman\">🌐Facebook</a></b>\n
    <b><a href=\"https://www.youtube.com/@metaversedataman\">🌐YouTube</a></b>\n
    <b><a href=\"https://api.whatsapp.com/message/GSVFVB6O7JIPJ1\">🌐WhatsApp</a></b>\n
    <b><a href=\"https://www.instagram.com/metaversedataman/\">🌐Instagram</a></b>\n
    <b><a href=\"https://twitter.com/metaversedataman\">🌐Twitter</a></b>\n
    ",
    'send_email' => "Click to send me email: <b><a href=\"mailto:ndefocn@yahoo.com\">📧 ndefocn@yahoo.com</a></b>\n
You may visit <b><a href=\"https://creat.i.ng/dataman\">🌐 My Page</a></b>",
    'call_me' => "Click to call me: <b><a href=\"tel:+23408037097898\">📲 +23408037097898</a></b>"
];
