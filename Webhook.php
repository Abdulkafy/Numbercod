<?php
// ملف set_webhook.php
$token = '8526176589:AAF-Tj1tXNVv-7FHpukPx7rBOmMmpB5H73Q';
$webhook_url = 'https://yourdomain.com/bot.php';

// تعيين Webhook
$result = file_get_contents("https://api.telegram.org/bot{$token}/setWebhook?url={$webhook_url}");
echo "Result: " . $result;

// التحقق من حالة Webhook
$info = file_get_contents("https://api.telegram.org/bot{$token}/getWebhookInfo");
echo "Webhook Info: " . $info;
?>