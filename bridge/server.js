const { Client, LocalAuth } = require('whatsapp-web.js');
const express = require('express');
const QRCode = require('qrcode');
const app = express();
const PORT = 3001;

app.use(express.json());

let client = null;
let qrCodeData = null;
let isReady = false;
let lastError = null;

// ─── WhatsApp Client ──────────────────────────────────────────
function startClient() {
    client = new Client({
        authStrategy: new LocalAuth({ clientId: 'whatsai' }),
        puppeteer: {
            headless: true,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--single-process',
            ],
        }
    });

    client.on('qr', async (qr) => {
        try {
            qrCodeData = await QRCode.toDataURL(qr);
        } catch (e) {
            qrCodeData = qr;
        }
        isReady = false;
        lastError = null;
        console.log('\n========================================');
        console.log('  📱 SCAN THIS QR CODE IN WHATSAPP');
        console.log('  Open WhatsApp → Linked Devices →');
        console.log('  Link a Device → Scan the QR below');
        console.log('========================================\n');
    });

    client.on('ready', () => {
        isReady = true;
        qrCodeData = null;
        console.log('\n✅ WhatsApp Connected! You can now send messages.\n');
    });

    client.on('disconnected', (reason) => {
        isReady = false;
        lastError = `Disconnected: ${reason}`;
        console.log(`\n⚠️ WhatsApp Disconnected: ${reason}\n`);
    });

    client.on('auth_failure', (msg) => {
        lastError = `Auth failed: ${msg}`;
        console.log(`\n❌ Auth Failed: ${msg}\n`);
    });

    client.on('message', async (msg) => {
        try {
            await fetch(`http://localhost:8080/webhook?provider=webscraper`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    from: msg.from.replace('@c.us', ''),
                    body: msg.body,
                    id: msg.id._serialized
                })
            }).catch(() => {});
        } catch (e) {}
    });

    client.initialize().catch(e => {
        lastError = e.message;
        console.log(`\n❌ Error: ${e.message}\n`);
    });
}

// ─── API Routes ───────────────────────────────────────────────

app.get('/health', (req, res) => {
    res.json({ status: 'ok' });
});

app.get('/status', (req, res) => {
    res.json({
        connected: isReady,
        qr: qrCodeData,
        error: lastError
    });
});

app.post('/send', async (req, res) => {
    const { to, message } = req.body;
    if (!to || !message) {
        return res.json({ success: false, error: 'Missing "to" or "message"' });
    }
    if (!isReady) {
        return res.json({ success: false, error: 'WhatsApp not connected. Scan QR code first.' });
    }
    try {
        const number = to.includes('@c.us') ? to : `${to}@c.us`;
        const response = await client.sendMessage(number, message);
        res.json({ success: true, id: response.id._serialized });
    } catch (e) {
        res.json({ success: false, error: e.message });
    }
});

// ─── Start ────────────────────────────────────────────────────

startClient();

app.listen(PORT, '0.0.0.0', () => {
    console.log('\n╔═══════════════════════════════════════════╗');
    console.log('║      🤖 WhatsAI Bridge Service           ║');
    console.log('╠═══════════════════════════════════════════╣');
    console.log(`║  Server: http://localhost:${PORT}           ║`);
    console.log('║  Status: http://localhost:3001/status     ║');
    console.log('║                                           ║');
    console.log('║  📱 Open WhatsApp → Linked Devices        ║');
    console.log('║     → Link a Device → Scan QR Code        ║');
    console.log('╚═══════════════════════════════════════════╝\n');
});
