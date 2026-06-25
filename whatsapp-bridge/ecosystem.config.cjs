/**
 * PM2 — تشغيل معزول بدون التأثير على خدمات أخرى
 * الاستخدام على VPS:
 *   cd /opt/mindlytics-whatsapp-bridge
 *   pm2 start ecosystem.config.cjs
 *   pm2 save
 */
module.exports = {
    apps: [
        {
            name: 'mindlytics-whatsapp',
            script: 'server.js',
            cwd: __dirname,
            instances: 1,
            exec_mode: 'fork',
            autorestart: true,
            max_memory_restart: '768M',
            env: {
                NODE_ENV: 'production',
                PORT: 3001,
            },
            error_file: './logs/err.log',
            out_file: './logs/out.log',
            merge_logs: true,
            time: true,
        },
    ],
};
