<?php
define('BOT_TOKEN', '8526176589:AAF-Tj1tXNVv-7FHpukPx7rBOmMmpB5H73Q');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
define('ADMIN_ID', 7700286311);

// معالجة البيانات الواردة من التلجرام
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    exit;
}

// استخراج المعلومات الأساسية
$message = $update['message'] ?? $update['callback_query']['message'] ?? null;
$chatId = $message['chat']['id'] ?? null;
$text = $message['text'] ?? '';
$callbackData = $update['callback_query']['data'] ?? '';

if ($callbackData) {
    handleCallbackQuery($update['callback_query']);
} else {
    handleMessage($chatId, $text, $message);
}

// دالة معالجة الرسائل النصية
function handleMessage($chatId, $text, $message) {
    switch ($text) {
        case '/start':
            sendWelcomeMessage($chatId);
            break;
        case '🛒 شراء حسابات':
            showAccountsMenu($chatId);
            break;
        case '📱 أرقام التفعيل':
            showPhoneNumbersMenu($chatId);
            break;
        case '📞 تواصل معنا':
            sendContactInfo($chatId);
            break;
        case 'ℹ️ معلومات عنا':
            sendAboutUs($chatId);
            break;
        default:
            if (strpos($text, 'شراء_') === 0) {
                processPurchase($chatId, $text);
            } else {
                sendMainMenu($chatId);
            }
    }
}

// دالة الترحيب
function sendWelcomeMessage($chatId) {
    $welcomeText = "مرحباً بك في بوت بيع وشراء الحسابات وأرقام التفعيل! 👋

🎯 *خدماتنا المتاحة:*
• بيع حسابات التواصل الاجتماعي
• بيع أرقام تفعيل لجميع البرامج
• شراء الحسابات المستعملة

اختر الخدمة التي تريدها من القائمة:";

    $keyboard = [
        ['🛒 شراء حسابات', '📱 أرقام التفعيل'],
        ['📞 تواصل معنا', 'ℹ️ معلومات عنا']
    ];

    sendMessage($chatId, $welcomeText, $keyboard);
}

// القائمة الرئيسية
function sendMainMenu($chatId) {
    $text = "القائمة الرئيسية - اختر الخدمة المطلوبة:";
    
    $keyboard = [
        ['🛒 شراء حسابات', '📱 أرقام التفعيل'],
        ['📞 تواصل معنا', 'ℹ️ معلومات عنا']
    ];

    sendMessage($chatId, $text, $keyboard);
}

// عرض قائمة الحسابات
function showAccountsMenu($chatId) {
    $text = "🛒 *قائمة الحسابات المتاحة للبيع:*

🔹 *فيسبوك*
• حساب فيسبوك عادي - 5$
• حساب فيسبوك بيع - 10$
• حساب فيسبوك قديم - 15$

🔹 *انستجرام*
• حساب انستجرام عادي - 8$
• حساب انستجرام متابعين - 12$

🔹 *تويتر*
• حساب تويتر عادي - 6$
• حساب تويتر مميز - 10$

🔹 *تيك توك*
• حساب تيك توك عادي - 7$
• حساب تيك توك نشط - 12$

انقر على نوع الحساب الذي تريد شراءه:";

    $keyboard = [
        [['text' => 'فيسبوك', 'callback_data' => 'buy_facebook']],
        [['text' => 'انستجرام', 'callback_data' => 'buy_instagram']],
        [['text' => 'تويتر', 'callback_data' => 'buy_twitter']],
        [['text' => 'تيك توك', 'callback_data' => 'buy_tiktok']],
        [['text' => '🔙 رجوع', 'callback_data' => 'back_main']]
    ];

    sendMessage($chatId, $text, $keyboard, true);
}

// عرض قائمة أرقام التفعيل
function showPhoneNumbersMenu($chatId) {
    $text = "📱 *أرقام التفعيل المتاحة:*

📞 *واتساب*
• رقم واتساب عادي - 3$
• رقم واتساب مميز - 5$

📞 *فيسبوك*
• رقم فيسبوك عادي - 2$
• رقم فيسبوك مميز - 4$

📞 *تليجرام*
• رقم تليجرام عادي - 2$
• رقم تليجرام مميز - 4$

📞 *جميع البرامج*
• باقة 5 أرقام - 10$
• باقة 10 أرقام - 18$

اختر نوع الرقم الذي تريده:";

    $keyboard = [
        [['text' => 'واتساب', 'callback_data' => 'buy_whatsapp']],
        [['text' => 'فيسبوك', 'callback_data' => 'buy_fb_number']],
        [['text' => 'تليجرام', 'callback_data' => 'buy_tg_number']],
        [['text' => 'باقة أرقام', 'callback_data' => 'buy_package']],
        [['text' => '🔙 رجوع', 'callback_data' => 'back_main']]
    ];

    sendMessage($chatId, $text, $keyboard, true);
}

// معالجة عملية الشراء
function processPurchase($chatId, $product) {
    $products = [
        'facebook_normal' => ['name' => 'حساب فيسبوك عادي', 'price' => 5],
        'facebook_business' => ['name' => 'حساب فيسبوك بيع', 'price' => 10],
        'instagram_normal' => ['name' => 'حساب انستجرام عادي', 'price' => 8],
        'whatsapp_normal' => ['name' => 'رقم واتساب عادي', 'price' => 3]
    ];

    $productKey = str_replace('شراء_', '', $product);
    
    if (isset($products[$productKey])) {
        $productInfo = $products[$productKey];
        $text = "🛒 *تفاصيل الطلب:*
        
📦 المنتج: {$productInfo['name']}
💰 السعر: {$productInfo['price']}$
        
لإكمال عملية الشراء، يرجى التواصل مع الإدارة:
@your_username

أو ارسال رسالة مباشرة إلى المسؤول";

        // إشعار للمسؤول
        $adminText = "🛎 *طلب جديد!*
        
👤 المستخدم: {$chatId}
📦 المنتج: {$productInfo['name']}
💰 السعر: {$productInfo['price']}$";

        sendMessage(ADMIN_ID, $adminText, null, true);
        
        sendMessage($chatId, $text);
    }
}

// معلومات التواصل
function sendContactInfo($chatId) {
    $text = "📞 *معلومات التواصل:*
    
👤 المسؤول: @your_username
📧 الإيميل: admin@example.com
🕒 أوقات العمل: 24/7

للاستفسارات أو المشاكل التقنية، لا تتردد في التواصل معنا";

    sendMessage($chatId, $text);
}

// معلومات عن البوت
function sendAboutUs($chatId) {
    $text = "ℹ️ *معلومات عنا:*
    
نحن متخصصون في بيع وشراء:
• حسابات التواصل الاجتماعي
• أرقام التفعيل لجميع البرامج
• حسابات الألعاب

🔒 *ضماناتنا:*
• أرقام وحسابات أصلية 100%
• دعم فني مستمر
• أسعار منافسة";

    sendMessage($chatId, $text);
}

// معالجة Callback Queries
function handleCallbackQuery($callback) {
    $chatId = $callback['message']['chat']['id'];
    $data = $callback['data'];
    
    switch ($data) {
        case 'back_main':
            sendMainMenu($chatId);
            break;
        case 'buy_facebook':
            showFacebookProducts($chatId);
            break;
        case 'buy_instagram':
            showInstagramProducts($chatId);
            break;
        case 'buy_whatsapp':
            showWhatsappNumbers($chatId);
            break;
        default:
            if (strpos($data, 'purchase_') === 0) {
                $product = str_replace('purchase_', '', $data);
                processPurchase($chatId, $product);
            }
    }
    
    // الإجابة على callback query
    answerCallbackQuery($callback['id']);
}

// دالة إرسال الرسائل
function sendMessage($chatId, $text, $keyboard = null, $parseMarkdown = false) {
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => $parseMarkdown ? 'Markdown' : 'HTML'
    ];
    
    if ($keyboard) {
        if (isset($keyboard[0][0]['text'])) {
            // Inline keyboard
            $data['reply_markup'] = json_encode(['inline_keyboard' => $keyboard]);
        } else {
            // Reply keyboard
            $data['reply_markup'] = json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true
            ]);
        }
    }
    
    $url = API_URL . 'sendMessage?' . http_build_query($data);
    file_get_contents($url);
}

// دالة الإجابة على Callback Query
function answerCallbackQuery($callbackId) {
    $data = [
        'callback_query_id' => $callbackId
    ];
    
    $url = API_URL . 'answerCallbackQuery?' . http_build_query($data);
    file_get_contents($url);
}

// منتجات فيسبوك
function showFacebookProducts($chatId) {
    $text = "🔹 *حسابات فيسبوك:*
    
• حساب فيسبوك عادي - 5$
• حساب فيسبوك بيع - 10$
• حساب فيسبوك قديم - 15$

اختر النوع الذي تريده:";

    $keyboard = [
        [['text' => 'شراء حساب عادي - 5$', 'callback_data' => 'purchase_facebook_normal']],
        [['text' => 'شرح حساب بيع - 10$', 'callback_data' => 'purchase_facebook_business']],
        [['text' => '🔙 رجوع', 'callback_data' => 'back_accounts']]
    ];

    sendMessage($chatId, $text, $keyboard, true);
}

// منتجات انستجرام
function showInstagramProducts($chatId) {
    $text = "🔹 *حسابات انستجرام:*
    
• حساب انستجرام عادي - 8$
• حساب انستجرام متابعين - 12$

اختر النوع الذي تريده:";

    $keyboard = [
        [['text' => 'شراء حساب عادي - 8$', 'callback_data' => 'purchase_instagram_normal']],
        [['text' => '🔙 رجوع', 'callback_data' => 'back_accounts']]
    ];

    sendMessage($chatId, $text, $keyboard, true);
}

// أرقام واتساب
function showWhatsappNumbers($chatId) {
    $text = "📞 *أرقام واتساب:*
    
• رقم واتساب عادي - 3$
• رقم واتساب مميز - 5$

اختر النوع الذي تريده:";

    $keyboard = [
        [['text' => 'شراء رقم عادي - 3$', 'callback_data' => 'purchase_whatsapp_normal']],
        [['text' => '🔙 رجوع', 'callback_data' => 'back_numbers']]
    ];

    sendMessage($chatId, $text, $keyboard, true);
}
?>