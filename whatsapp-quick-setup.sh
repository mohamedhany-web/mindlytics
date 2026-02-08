#!/bin/bash

echo "🚀 إعداد WhatsApp API المجاني للمنصة التعليمية"
echo "=================================================="

# إنشاء مجلد الخدمة
if [ ! -d "whatsapp-service" ]; then
    echo "📁 إنشاء مجلد خدمة الواتساب..."
    mkdir whatsapp-service
fi

cd whatsapp-service

# تثبيت المتطلبات
echo "📦 تثبيت المكتبات المطلوبة..."
npm init -y
npm install @wppconnect-team/wppconnect express cors

# إنشاء ملف الخدمة
echo "📝 إنشاء ملف الخدمة..."
cat > server.js << 'EOF'
const wpp = require('@wppconnect-team/wppconnect');
const express = require('express');
const cors = require('cors');

const app = express();
app.use(express.json());
app.use(cors());

let client;

// إنشاء اتصال WhatsApp
wpp
  .create({
    session: 'learning-platform',
    headless: false,
    devtools: false,
    debug: false,
    logQR: true,
    browserArgs: ['--no-sandbox', '--disable-setuid-sandbox']
  })
  .then((client_instance) => {
    client = client_instance;
    console.log('✅ WhatsApp متصل بنجاح!');
    console.log('🎯 يمكنك الآن إرسال الرسائل من Laravel');
  })
  .catch((error) => {
    console.error('❌ خطأ في الاتصال:', error);
  });

// API لإرسال الرسائل
app.post('/send-message', async (req, res) => {
    try {
        if (!client) {
            return res.json({ 
                success: false, 
                error: 'WhatsApp غير متصل' 
            });
        }

        const { phone, message } = req.body;
        
        if (!phone || !message) {
            return res.json({ 
                success: false, 
                error: 'رقم الهاتف والرسالة مطلوبان' 
            });
        }

        // تنسيق الرقم للأرقام المصرية
        let formattedPhone = phone.replace(/[^0-9]/g, '');
        if (formattedPhone.startsWith('0')) {
            formattedPhone = '2' + formattedPhone;
        }
        
        // إرسال الرسالة
        const result = await client.sendText(`${formattedPhone}@c.us`, message);
        
        console.log(`✅ رسالة مرسلة إلى: ${phone}`);
        
        res.json({ 
            success: true, 
            messageId: result.id,
            phone: formattedPhone
        });
        
    } catch (error) {
        console.error('❌ خطأ في الإرسال:', error);
        res.json({ 
            success: false, 
            error: error.message 
        });
    }
});

// التحقق من الحالة
app.get('/status', async (req, res) => {
    try {
        if (!client) {
            return res.json({ connected: false });
        }
        
        const state = await client.getConnectionState();
        res.json({ 
            connected: state === 'CONNECTED',
            state: state,
            timestamp: new Date()
        });
    } catch (error) {
        res.json({ 
            connected: false,
            error: error.message 
        });
    }
});

const PORT = 3001;
app.listen(PORT, () => {
    console.log(`🚀 WhatsApp API Server running on port ${PORT}`);
    console.log(`📱 للاختبار: http://localhost:${PORT}/status`);
    console.log(`📋 تأكد من مسح QR Code أولاً`);
});
EOF

echo "✅ تم إنشاء خدمة WhatsApp بنجاح!"
echo ""
echo "🎯 الخطوات التالية:"
echo "1. node server.js"
echo "2. امسح QR Code بواتساب هاتفك"
echo "3. في ملف .env أضف: WHATSAPP_TYPE=local"
echo "4. php artisan config:clear"
echo ""
echo "🎊 بعدها يمكنك إرسال الرسائل مجاناً!"
EOF

chmod +x whatsapp-quick-setup.sh

echo "✅ ملف الإعداد السريع جاهز!"
echo ""
echo "🚀 **لتشغيل الإعداد فوراً:**"
echo "1. ./whatsapp-quick-setup.sh"
echo "2. اتبع التعليمات التي ستظهر"
