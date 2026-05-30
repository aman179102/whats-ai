<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $config['app']['name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class'}</script>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-app">
    <nav class="bg-white shadow-sm border-b nav-bg" style="border-color:var(--border)">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-xl font-bold" style="color:var(--text-primary)">WhatsAI</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">Dashboard</a>
                        <a href="/settings" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Settings</a>
                        <a href="/scheduler" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Scheduler</a>
                        <a href="/messages" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Messages</a>
                        <a href="/contacts" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Contacts</a>
                        <a href="/setup" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Setup Guide</a>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button id="themeToggle" class="theme-toggle" title="Toggle theme">🌙</button>
                    <button id="menuToggle" class="md:hidden theme-toggle" title="Menu">☰</button>
                </div>
            </div>
            <div id="mobileMenu" class="mobile-menu md:hidden">
                <a href="/" class="text-sm font-medium">Dashboard</a>
                <a href="/settings" class="text-sm font-medium">Settings</a>
                <a href="/scheduler" class="text-sm font-medium">Scheduler</a>
                <a href="/messages" class="text-sm font-medium">Messages</a>
                <a href="/contacts" class="text-sm font-medium">Contacts</a>
                <a href="/setup" class="text-sm font-medium">Setup Guide</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        <?php
        $wpProvider = $config['whatsapp']['provider'];
        $aiProvider = $config['ai']['provider'];
        $wpConfigured = false;
        $aiConfigured = false;

        switch ($wpProvider) {
            case 'meta':
                $wpConfigured = !empty($config['whatsapp']['meta']['access_token']) && !empty($config['whatsapp']['meta']['phone_number_id']);
                break;
            case 'twilio':
                $wpConfigured = !empty($config['whatsapp']['twilio']['account_sid']) && !empty($config['whatsapp']['twilio']['auth_token']);
                break;
            case 'webscraper':
                $wpConfigured = !empty($config['whatsapp']['webscraper']['api_url']);
                break;
        }

        switch ($aiProvider) {
            case 'openrouter':
                $aiConfigured = !empty($config['ai']['openrouter']['api_key']);
                break;
            case 'groq':
                $aiConfigured = !empty($config['ai']['groq']['api_key']);
                break;
            case 'gemini':
                $aiConfigured = !empty($config['ai']['gemini']['api_key']);
                break;
            case 'custom':
                $aiConfigured = !empty($config['ai']['custom']['api_key']) && !empty($config['ai']['custom']['base_url']);
                break;
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Messages</p>
                        <p class="text-3xl font-bold"><?= $stats['total_messages'] ?></p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Contacts</p>
                        <p class="text-3xl font-bold"><?= $stats['total_contacts'] ?></p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Schedules</p>
                        <p class="text-3xl font-bold"><?= $stats['active_schedules'] ?></p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">WhatsApp Provider Status</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Current Provider</span>
                        <span class="font-medium"><?= ucfirst($wpProvider) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Configuration</span>
                        <?php if ($wpConfigured): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Configured</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Not Configured</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($wpProvider === 'webscraper'): ?>
                        <div id="bridgeStatus" class="mt-4 p-3 bg-gray-50 border rounded-lg">
                            <p class="text-sm text-gray-600">🔍 Checking bridge connection...</p>
                        </div>
                        <div id="bridgeQr" class="mt-4 hidden">
                            <p class="text-sm font-medium mb-2">📱 Scan this QR with WhatsApp:</p>
                            <p class="text-xs text-gray-500 mb-2">Open WhatsApp → Linked Devices → Link a Device</p>
                            <img id="qrImage" src="" alt="QR Code" class="border rounded-lg mx-auto" style="max-width: 256px;">
                        </div>
                        <script>
                        async function checkBridge() {
                            try {
                                const res = await fetch('/api/bridge/status');
                                const data = await res.json();
                                const div = document.getElementById('bridgeStatus');
                                if (data.running && data.connected) {
                                    div.className = 'mt-4 p-3 bg-green-50 border border-green-200 rounded-lg';
                                    div.innerHTML = '<p class="text-sm text-green-700">✅ Bridge connected & WhatsApp linked!</p>';
                                    document.getElementById('bridgeQr').classList.add('hidden');
                                } else if (data.running && data.qr) {
                                    div.className = 'mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg';
                                    div.innerHTML = '<p class="text-sm text-blue-700">📱 Bridge running — scan the QR code to link WhatsApp</p>';
                                    const qrDiv = document.getElementById('bridgeQr');
                                    qrDiv.classList.remove('hidden');
                                    document.getElementById('qrImage').src = data.qr;
                                } else if (data.running) {
                                    div.className = 'mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
                                    div.innerHTML = '<p class="text-sm text-yellow-700">⏳ Bridge running — waiting for QR code...</p>';
                                } else {
                                    div.className = 'mt-4 p-3 bg-red-50 border border-red-200 rounded-lg';
                                    div.innerHTML = '<p class="text-sm text-red-700">❌ Bridge not running. Start it: <code class="bg-red-100 px-1 rounded">cd bridge && npm install && npm start</code></p>';
                                }
                            } catch (e) {
                                document.getElementById('bridgeStatus').className = 'mt-4 p-3 bg-red-50 border border-red-200 rounded-lg';
                                document.getElementById('bridgeStatus').innerHTML = '<p class="text-sm text-red-700">❌ Bridge not running. Start it: <code class="bg-red-100 px-1 rounded">cd bridge && npm install && npm start</code></p>';
                            }
                        }
                        checkBridge();
                        setInterval(checkBridge, 5000);
                        </script>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">AI Provider Status</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Current Provider</span>
                        <span class="font-medium"><?= ucfirst($aiProvider) ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Model</span>
                        <span class="font-medium"><?= $config['ai']['model'] ?? 'Not set' ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Configuration</span>
                        <?php if ($aiConfigured): ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Configured</span>
                        <?php else: ?>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Not Configured</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$wpConfigured || !$aiConfigured): ?>
        <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">🚀 Quick Setup</h3>
            <p class="text-yellow-700 mb-4">Complete the setup to start using WhatsAI:</p>
            <div class="space-y-2">
                <?php if (!$wpConfigured): ?>
                    <p class="text-sm text-yellow-700">1. 📱 Configure your <a href="/settings" class="text-blue-600 underline">WhatsApp Provider</a></p>
                <?php endif; ?>
                <?php if (!$aiConfigured): ?>
                    <p class="text-sm text-yellow-700">2. 🤖 Configure your <a href="/settings" class="text-blue-600 underline">AI Provider</a></p>
                <?php endif; ?>
                <p class="text-sm text-yellow-700">3. 📖 Follow the <a href="/setup" class="text-blue-600 underline">Step-by-Step Setup Guide</a></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Test & Tools Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Test AI -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">🧪 Test AI Connection</h2>
                <p class="text-sm text-gray-500 mb-4">Verify your AI provider is working correctly.</p>
                <div class="space-y-3">
                    <select id="aiTestProvider" class="w-full border rounded-lg px-3 py-2 text-sm">
                        <option value="openrouter" <?= $config['ai']['provider'] === 'openrouter' ? 'selected' : '' ?>>OpenRouter</option>
                        <option value="groq" <?= $config['ai']['provider'] === 'groq' ? 'selected' : '' ?>>Groq</option>
                        <option value="gemini" <?= $config['ai']['provider'] === 'gemini' ? 'selected' : '' ?>>Gemini</option>
                        <option value="custom" <?= $config['ai']['provider'] === 'custom' ? 'selected' : '' ?>>Custom</option>
                    </select>
                    <input type="text" id="aiTestModel" value="<?= htmlspecialchars($config['ai']['model']) ?>" placeholder="Model name" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <button onclick="testAI()" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition text-sm">
                        Test AI Connection
                    </button>
                    <div id="aiTestResult" class="hidden p-3 rounded-lg text-sm"></div>
                </div>
            </div>

            <!-- Test WhatsApp -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">📱 Test WhatsApp</h2>
                <p class="text-sm text-gray-500 mb-4">Send a test message to verify WhatsApp connection.</p>
                <div class="space-y-3">
                    <input type="text" id="wpTestPhone" placeholder="Phone (e.g., 919876543210)" class="w-full border rounded-lg px-3 py-2 text-sm">
                    <button onclick="testWhatsApp()" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-green-700 transition text-sm">
                        Send Test Message
                    </button>
                    <div id="wpTestResult" class="hidden p-3 rounded-lg text-sm"></div>
                </div>
            </div>

            <!-- Manual Send -->
            <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
                <h2 class="text-lg font-semibold mb-4">✉️ Manual Send</h2>
                <p class="text-sm text-gray-500 mb-4">Compose and send a message to any contact.</p>
                <form id="manualSendForm" onsubmit="manualSend(event)" class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <select name="contact_id" id="manualContact" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">Select contact... (or enter phone below)</option>
                            <?php
                            $contacts = \App\Core\App::getInstance()->getDb()->getPdo()->query('SELECT id, name, phone FROM contacts ORDER BY name')->fetchAll();
                            foreach ($contacts as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['phone']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="manualPhone" placeholder="Or enter phone number directly" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <textarea id="manualMessage" rows="3" placeholder="Type your message here..." class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" id="manualUseAi" class="mr-2" onchange="document.getElementById('manualAiPrompt').classList.toggle('hidden', !this.checked)">
                            Generate with AI
                        </label>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-lg font-semibold hover:bg-blue-700 transition text-sm">
                            Send Message
                        </button>
                    </div>
                    <div id="manualAiPrompt" class="hidden">
                        <textarea rows="2" placeholder="AI prompt (optional)" class="w-full border rounded-lg px-3 py-2 text-sm">Write a friendly WhatsApp message</textarea>
                    </div>
                    <div id="manualSendResult" class="hidden p-3 rounded-lg text-sm"></div>
                </form>
            </div>
        </div>
    </main>

    <script>
        async function testAI() {
            const provider = document.getElementById('aiTestProvider').value;
            const model = document.getElementById('aiTestModel').value;
            const resultDiv = document.getElementById('aiTestResult');
            resultDiv.className = 'p-4 rounded-lg text-sm bg-gray-100';
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '⏳ Testing AI connection...';

            const formData = new FormData();
            formData.append('provider', provider);
            formData.append('model', model);

            try {
                const res = await fetch('/api/test-ai', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    resultDiv.className = 'p-4 rounded-lg text-sm bg-green-50 border border-green-200';
                    resultDiv.innerHTML = '✅ <strong>AI Connected!</strong><br>Response: ' + data.content;
                } else {
                    resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                    resultDiv.innerHTML = '❌ <strong>Failed:</strong> ' + (data.error || 'Unknown error');
                }
            } catch (e) {
                resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                resultDiv.innerHTML = '❌ Network error: ' + e.message;
            }
        }

        async function testWhatsApp() {
            const phone = document.getElementById('wpTestPhone').value;
            const resultDiv = document.getElementById('wpTestResult');
            resultDiv.className = 'p-4 rounded-lg text-sm bg-gray-100';
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '⏳ Sending test message...';

            if (!phone) {
                resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                resultDiv.innerHTML = '❌ Please enter a phone number';
                return;
            }

            const formData = new FormData();
            formData.append('phone', phone);

            try {
                const res = await fetch('/api/test-whatsapp', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    resultDiv.className = 'p-4 rounded-lg text-sm bg-green-50 border border-green-200';
                    resultDiv.innerHTML = '✅ <strong>Message sent!</strong> Check your WhatsApp.';
                } else {
                    resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                    resultDiv.innerHTML = '❌ <strong>Failed:</strong> ' + (data.error || 'Unknown error');
                }
            } catch (e) {
                resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                resultDiv.innerHTML = '❌ Network error: ' + e.message;
            }
        }

        async function manualSend(event) {
            event.preventDefault();
            const resultDiv = document.getElementById('manualSendResult');
            resultDiv.className = 'p-4 rounded-lg text-sm bg-gray-100';
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '⏳ Sending...';

            const formData = new FormData();
            const contactId = document.getElementById('manualContact').value;
            const phone = document.getElementById('manualPhone').value;
            const message = document.getElementById('manualMessage').value;
            const useAi = document.getElementById('manualUseAi').checked;
            const aiPrompt = document.querySelector('#manualAiPrompt textarea').value;

            if (contactId) formData.append('contact_id', contactId);
            if (phone) formData.append('phone', phone);
            if (message) formData.append('message', message);
            if (useAi) { formData.append('use_ai', '1'); formData.append('ai_prompt', aiPrompt); }

            try {
                const res = await fetch('/api/send-message', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    resultDiv.className = 'p-4 rounded-lg text-sm bg-green-50 border border-green-200';
                    resultDiv.innerHTML = '✅ <strong>Sent!</strong> ' + (data.content ? '<br>Content: ' + data.content : '');
                } else {
                    resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                    resultDiv.innerHTML = '❌ <strong>Failed:</strong> ' + (data.error || 'Unknown error');
                }
            } catch (e) {
                resultDiv.className = 'p-4 rounded-lg text-sm bg-red-50 border border-red-200';
                resultDiv.innerHTML = '❌ Network error: ' + e.message;
            }
        }
    </script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
