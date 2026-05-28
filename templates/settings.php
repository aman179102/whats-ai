<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - WhatsAI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-xl font-bold text-gray-900">WhatsAI</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Dashboard</a>
                        <a href="/settings" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">Settings</a>
                        <a href="/scheduler" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Scheduler</a>
                        <a href="/messages" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Messages</a>
                        <a href="/contacts" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Contacts</a>
                        <a href="/setup" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Setup Guide</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <?php if (isset($_GET['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">Settings saved successfully!</div>
        <?php endif; ?>

        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm text-blue-700">
                💡 <strong>Note:</strong> Settings are saved to the database. On production (Render/Railway), set your API keys as
                <strong>Environment Variables</strong> in the hosting dashboard instead — they take priority over DB settings.
                See <a href="/setup" class="underline">Setup Guide</a> for details.
            </p>
        </div>

        <form method="POST" action="/settings/save" class="space-y-8">
            <!-- WhatsApp Provider -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">📱 WhatsApp Provider</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Provider</label>
                    <select name="whatsapp_provider" class="w-full border rounded-lg px-3 py-2" onchange="toggleWhatsAppProvider(this.value)">
                        <option value="meta" <?= $config['whatsapp']['provider'] === 'meta' ? 'selected' : '' ?>>Meta Cloud API (Recommended)</option>
                        <option value="twilio" <?= $config['whatsapp']['provider'] === 'twilio' ? 'selected' : '' ?>>Twilio API</option>
                        <option value="webscraper" <?= $config['whatsapp']['provider'] === 'webscraper' ? 'selected' : '' ?>>Web Scraper (Unofficial - ⚠️ Risky)</option>
                    </select>
                </div>

                <!-- Web Scraper Warning -->
                <div id="webscraperWarning" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg <?= $config['whatsapp']['provider'] === 'webscraper' ? '' : 'hidden' ?>">
                    <p class="text-sm text-red-700 font-semibold">⚠️ WARNING: Unofficial WhatsApp automation violates Meta's Terms of Service.</p>
                    <p class="text-sm text-red-600 mt-1">Your phone number can be PERMANENTLY BANNED. WhatsApp may also block your device. This method is for educational purposes only and may break at any time.</p>
                    <label class="flex items-center mt-2">
                        <input type="checkbox" id="webscraperConfirm" class="mr-2" onchange="document.getElementById('webscraperSubmit').disabled = !this.checked">
                        <span class="text-sm text-red-700">I understand the risks and want to proceed anyway</span>
                    </label>
                </div>

                <!-- Meta Cloud API Fields -->
                <div id="metaFields" class="space-y-4 <?= $config['whatsapp']['provider'] === 'meta' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number ID</label>
                        <input type="text" name="meta_phone_number_id" value="<?= htmlspecialchars($config['whatsapp']['meta']['phone_number_id']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="From Meta Developer Console">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Access Token</label>
                        <input type="password" name="meta_access_token" value="<?= htmlspecialchars($config['whatsapp']['meta']['access_token']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="Permanent or temporary token">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">App Secret</label>
                        <input type="password" name="meta_app_secret" value="<?= htmlspecialchars($config['whatsapp']['meta']['app_secret']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="For webhook signature verification">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Verify Token</label>
                        <input type="text" name="meta_verify_token" value="<?= htmlspecialchars($config['whatsapp']['meta']['verify_token']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="Your custom verify token">
                    </div>
                </div>

                <!-- Twilio Fields -->
                <div id="twilioFields" class="space-y-4 <?= $config['whatsapp']['provider'] === 'twilio' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account SID</label>
                        <input type="text" name="twilio_account_sid" value="<?= htmlspecialchars($config['whatsapp']['twilio']['account_sid']) ?>" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Auth Token</label>
                        <input type="password" name="twilio_auth_token" value="<?= htmlspecialchars($config['whatsapp']['twilio']['auth_token']) ?>" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">From Number</label>
                        <input type="text" name="twilio_from_number" value="<?= htmlspecialchars($config['whatsapp']['twilio']['from_number']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="e.g., 14155238886">
                    </div>
                </div>

                <!-- Web Scraper Fields -->
                <div id="webscraperFields" class="space-y-4 <?= $config['whatsapp']['provider'] === 'webscraper' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API URL</label>
                        <input type="text" name="web_scraper_api_url" value="<?= htmlspecialchars($config['whatsapp']['webscraper']['api_url'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2" placeholder="http://localhost:3000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Key</label>
                        <input type="password" name="web_scraper_api_key" value="<?= htmlspecialchars($config['whatsapp']['webscraper']['api_key'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>

            <!-- AI Provider -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">🤖 AI Provider</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Provider</label>
                    <select name="ai_provider" class="w-full border rounded-lg px-3 py-2" onchange="toggleAIProvider(this.value)">
                        <option value="openrouter" <?= $config['ai']['provider'] === 'openrouter' ? 'selected' : '' ?>>OpenRouter (200+ Models)</option>
                        <option value="groq" <?= $config['ai']['provider'] === 'groq' ? 'selected' : '' ?>>Groq (Fast Inference)</option>
                        <option value="gemini" <?= $config['ai']['provider'] === 'gemini' ? 'selected' : '' ?>>Google Gemini</option>
                        <option value="custom" <?= $config['ai']['provider'] === 'custom' ? 'selected' : '' ?>>Custom API (OpenAI-compatible)</option>
                    </select>
                </div>

                <!-- Common AI Settings -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model</label>
                        <div class="flex gap-2">
                            <select id="modelSelect" class="flex-1 border rounded-lg px-3 py-2" onchange="document.getElementById('aiModel').value=this.value; document.getElementById('aiModel').dataset.fromSelect='true'">
                                <option value="">Custom model...</option>
                            </select>
                            <input type="text" id="aiModel" name="ai_model" value="<?= htmlspecialchars($config['ai']['model']) ?>" class="flex-1 border rounded-lg px-3 py-2" placeholder="Or type any model name" onfocus="if(this.dataset.fromSelect==='true'){this.value='';this.dataset.fromSelect=''}">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Select from list or type any model name manually</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Temperature</label>
                            <input type="number" name="ai_temperature" value="<?= $config['ai']['temperature'] ?>" step="0.1" min="0" max="2" class="w-full border rounded-lg px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Max Tokens</label>
                            <input type="number" name="ai_max_tokens" value="<?= $config['ai']['max_tokens'] ?>" step="1" min="1" max="32000" class="w-full border rounded-lg px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">System Prompt</label>
                        <textarea name="ai_system_prompt" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="You are a helpful WhatsApp assistant..."><?= htmlspecialchars($config['ai']['system_prompt']) ?></textarea>
                    </div>
                </div>

                <!-- OpenRouter -->
                <div id="openrouterFields" class="space-y-4 <?= $config['ai']['provider'] === 'openrouter' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Key</label>
                        <input type="password" name="openrouter_api_key" value="<?= htmlspecialchars($config['ai']['openrouter']['api_key']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="sk-or-...">
                    </div>
                </div>

                <!-- Groq -->
                <div id="groqFields" class="space-y-4 <?= $config['ai']['provider'] === 'groq' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Key</label>
                        <input type="password" name="groq_api_key" value="<?= htmlspecialchars($config['ai']['groq']['api_key']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="gsk_...">
                    </div>
                </div>

                <!-- Gemini -->
                <div id="geminiFields" class="space-y-4 <?= $config['ai']['provider'] === 'gemini' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Key</label>
                        <input type="password" name="gemini_api_key" value="<?= htmlspecialchars($config['ai']['gemini']['api_key']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="AIza...">
                    </div>
                </div>

                <!-- Custom -->
                <div id="customFields" class="space-y-4 <?= $config['ai']['provider'] === 'custom' ? '' : 'hidden' ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Base URL</label>
                        <input type="text" name="custom_base_url" value="<?= htmlspecialchars($config['ai']['custom']['base_url']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="https://api.openai.com/v1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">API Key</label>
                        <input type="password" name="custom_api_key" value="<?= htmlspecialchars($config['ai']['custom']['api_key']) ?>" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" name="custom_model" value="<?= htmlspecialchars($config['ai']['custom']['model']) ?>" class="w-full border rounded-lg px-3 py-2" placeholder="gpt-4o">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6" style="background-color:var(--bg-card)">
                <h2 class="text-lg font-semibold mb-4" style="color:var(--text-primary)">🧪 Test AI Configuration</h2>
                <p class="text-sm mb-4" style="color:var(--text-secondary)">Verify your AI settings are working before saving.</p>
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <select id="testProvider" class="border rounded-lg px-3 py-2 text-sm flex-1 min-w-[140px]" style="background-color:var(--input-bg);color:var(--text-primary);border-color:var(--border)">
                        <option value="openrouter">OpenRouter</option>
                        <option value="groq">Groq</option>
                        <option value="gemini">Gemini</option>
                        <option value="custom">Custom</option>
                    </select>
                    <input type="text" id="testModel" value="<?= htmlspecialchars($config['ai']['model']) ?>" placeholder="Model name" class="border rounded-lg px-3 py-2 text-sm flex-1 min-w-[140px]" style="background-color:var(--input-bg);color:var(--text-primary);border-color:var(--border)">
                    <button type="button" onclick="testConfig()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-700 transition whitespace-nowrap">
                        Test Now
                    </button>
                    <button type="button" onclick="document.getElementById('testResult').classList.add('hidden')" class="px-3 py-2 rounded-lg text-sm font-medium hover:opacity-80" style="color:var(--text-secondary)">Clear</button>
                </div>
                <div id="testResult" class="hidden p-4 rounded-lg text-sm border"></div>
            </div>

            <button type="submit" id="webscraperSubmit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                Save Settings
            </button>
        </form>
    </main>

    <script>
        // Model presets for each provider
        const modelPresets = {
            openrouter: ['gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo', 'claude-3.5-sonnet', 'claude-3-haiku', 'llama-3.1-70b-instruct', 'llama-3.1-8b-instruct', 'mistral-large', 'mixtral-8x7b-instruct', 'deepseek-chat', 'qwen-2-72b'],
            groq: ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'mixtral-8x7b-32768', 'gemma2-9b-it', 'llama3-70b-8192', 'llama3-8b-8192'],
            gemini: ['gemini-2.0-flash', 'gemini-2.0-flash-lite', 'gemini-1.5-flash', 'gemini-1.5-flash-8b', 'gemini-1.5-pro'],
            custom: ['custom']
        };

        function toggleWhatsAppProvider(provider) {
            document.getElementById('metaFields').classList.toggle('hidden', provider !== 'meta');
            document.getElementById('twilioFields').classList.toggle('hidden', provider !== 'twilio');
            document.getElementById('webscraperFields').classList.toggle('hidden', provider !== 'webscraper');
            document.getElementById('webscraperWarning').classList.toggle('hidden', provider !== 'webscraper');

            const submitBtn = document.getElementById('webscraperSubmit');
            if (provider === 'webscraper') {
                submitBtn.disabled = !document.getElementById('webscraperConfirm').checked;
            } else {
                submitBtn.disabled = false;
            }
        }

        function toggleAIProvider(provider) {
            document.getElementById('openrouterFields').classList.toggle('hidden', provider !== 'openrouter');
            document.getElementById('groqFields').classList.toggle('hidden', provider !== 'groq');
            document.getElementById('geminiFields').classList.toggle('hidden', provider !== 'gemini');
            document.getElementById('customFields').classList.toggle('hidden', provider !== 'custom');

            // Update model presets
            const select = document.getElementById('modelSelect');
            const models = modelPresets[provider] || [];
            select.innerHTML = '<option value="">Custom model...</option>';
            models.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m;
                opt.textContent = m;
                select.appendChild(opt);
            });
        }

        // Initialize model presets on load
        document.addEventListener('DOMContentLoaded', function() {
            const aiProvider = document.getElementById('<?= $config['ai']['provider'] ?>');
            if (aiProvider) {
                const select = document.getElementById('modelSelect');
                const models = modelPresets['<?= $config['ai']['provider'] ?>'] || [];
                select.innerHTML = '<option value="">Custom model...</option>';
                models.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m;
                    opt.textContent = m;
                    if (m === '<?= $config['ai']['model'] ?>') opt.selected = true;
                    select.appendChild(opt);
        });

        async function testConfig() {
            const provider = document.getElementById('testProvider').value;
            const model = document.getElementById('testModel').value;
            const resultDiv = document.getElementById('testResult');
            resultDiv.className = 'p-4 rounded-lg text-sm border';
            resultDiv.style.backgroundColor = 'var(--bg-card)';
            resultDiv.style.borderColor = 'var(--border)';
            resultDiv.style.color = 'var(--text-primary)';
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '⏳ Testing AI connection...';

            const fd = new FormData();
            fd.append('provider', provider);
            fd.append('model', model);

            try {
                const res = await fetch('/api/test-ai', { method: 'POST', body: fd });
                const data = await res.json();
                if (data.success) {
                    resultDiv.style.backgroundColor = 'var(--success-bg)';
                    resultDiv.style.borderColor = 'var(--success-border)';
                    resultDiv.style.color = 'var(--success-text)';
                    resultDiv.innerHTML = '<strong>✅ Configuration Working!</strong><br>Response: ' + data.content + '<br><span style="opacity:0.75;font-size:0.75rem">Model: ' + (data.model || model) + '</span>';
                } else {
                    resultDiv.style.backgroundColor = 'var(--error-bg)';
                    resultDiv.style.borderColor = 'var(--error-border)';
                    resultDiv.style.color = 'var(--error-text)';
                    resultDiv.innerHTML = '<strong>❌ Configuration Failed</strong><br>' + (data.error || 'Check your API key and model name.');
                }
            } catch (e) {
                resultDiv.style.backgroundColor = 'var(--error-bg)';
                resultDiv.style.borderColor = 'var(--error-border)';
                resultDiv.style.color = 'var(--error-text)';
                resultDiv.innerHTML = '<strong>❌ Network Error</strong><br>' + e.message;
            }
        }
            }
        });
    </script>
</body>
</html>
