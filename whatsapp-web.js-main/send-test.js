const { Client, LocalAuth } = require('./index');

const TARGET = '201044610510@c.us';
const MESSAGE = 'مرحباً! هذه رسالة تجريبية من whatsapp-web.js';

const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: { headless: false },
});

client.on('qr', () => {
    console.log('\n>>> امسح رمز QR من هاتفك: واتساب > الأجهزة المرتبطة > ربط جهاز\n');
});

client.on('authenticated', () => {
    console.log('تمت المصادقة بنجاح...');
});

client.on('auth_failure', (msg) => {
    console.error('فشل المصادقة:', msg);
    process.exit(1);
});

client.on('ready', async () => {
    console.log('العميل جاهز! جاري إرسال الرسالة إلى', TARGET);

    try {
        const numberId = await client.getNumberId('201044610510');
        if (!numberId) {
            console.error('الرقم غير مسجل على واتساب:', TARGET);
            await client.destroy();
            process.exit(1);
        }

        const msg = await client.sendMessage(numberId._serialized, MESSAGE);
        console.log('\nتم إرسال الرسالة بنجاح!');
        console.log('معرف الرسالة:', msg?.id?._serialized);
        console.log('المحتوى:', MESSAGE);
        console.log('\nيمكنك إغلاق النافذة أو الضغط Ctrl+C للخروج.\n');
    } catch (err) {
        console.error('خطأ أثناء الإرسال:', err.message);
        await client.destroy();
        process.exit(1);
    }
});

console.log('جاري تشغيل WhatsApp Web...');
client.initialize();
