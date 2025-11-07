<?php
$token = '8526176589:AAF-Tj1tXNVv-7FHpukPx7rBOmMmpB5H73Q';
$url = 'https://yoursite.com/bot.php';
file_get_contents("https://api.telegram.org/bot{$token}/setWebhook?url={$url}");
echo "Webhook set successfully!";
?>
