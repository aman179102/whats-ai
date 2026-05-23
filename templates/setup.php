<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Guide - WhatsAI</title>
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
                        <a href="/settings" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Settings</a>
                        <a href="/scheduler" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Scheduler</a>
                        <a href="/messages" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Messages</a>
                        <a href="/contacts" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Contacts</a>
                        <a href="/setup" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">Setup Guide</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-6">📖 Step-by-Step Setup Guide</h2>

        <?php
        $whatsappGuides = \App\Guide\SetupGuide::getWhatsAppGuides();
        $aiGuides = \App\Guide\SetupGuide::getAIGuides();
        $hostingGuide = \App\Guide\SetupGuide::getHostingGuide();
        ?>

        <!-- WhatsApp Provider Guides -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">1. 📱 WhatsApp Provider Setup</h3>

            <div class="space-y-4">
                <!-- Meta Guide -->
                <details class="bg-white rounded-lg shadow" <?= $config['whatsapp']['provider'] === 'meta' ? 'open' : '' ?>>
                    <summary class="px-6 py-4 cursor-pointer font-semibold hover:bg-gray-50">Meta Cloud API (Recommended) ⭐</summary>
                    <div class="px-6 pb-4 space-y-4">
                    <?php foreach ($whatsappGuides['meta']['steps'] as $step): ?>
                        <div class="flex gap-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm"><?= $step['step'] ?></span>
                            <div>
                                <h4 class="font-medium"><?= $step['title'] ?></h4>
                                <p class="text-sm text-gray-600"><?= $step['description'] ?></p>
                                <?php if (isset($step['link'])): ?>
                                    <a href="<?= $step['link'] ?>" target="_blank" class="text-sm text-blue-600 underline">Open →</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </details>

                <!-- Twilio Guide -->
                <details class="bg-white rounded-lg shadow" <?= $config['whatsapp']['provider'] === 'twilio' ? 'open' : '' ?>>
                    <summary class="px-6 py-4 cursor-pointer font-semibold hover:bg-gray-50">Twilio API</summary>
                    <div class="px-6 pb-4 space-y-4">
                    <?php foreach ($whatsappGuides['twilio']['steps'] as $step): ?>
                        <div class="flex gap-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-sm"><?= $step['step'] ?></span>
                            <div>
                                <h4 class="font-medium"><?= $step['title'] ?></h4>
                                <p class="text-sm text-gray-600"><?= $step['description'] ?></p>
                                <?php if (isset($step['link'])): ?>
                                    <a href="<?= $step['link'] ?>" target="_blank" class="text-sm text-blue-600 underline">Open →</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </details>

                <!-- Web Scraper Guide -->
                <details class="bg-white rounded-lg shadow border-red-200" <?= $config['whatsapp']['provider'] === 'webscraper' ? 'open' : '' ?>>
                    <summary class="px-6 py-4 cursor-pointer font-semibold hover:bg-gray-50 text-red-700">Web Scraper (Unofficial) ⚠️</summary>
                    <div class="px-6 pb-4">
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg mb-4">
                            <p class="text-sm text-red-700 font-semibold">⚠️ WARNING: This method violates WhatsApp's Terms of Service.</p>
                            <p class="text-sm text-red-600 mt-1">Your phone number can be permanently banned. Use only for educational purposes.</p>
                        </div>
                        <div class="space-y-4">
                        <?php foreach ($whatsappGuides['webscraper']['steps'] as $step): ?>
                            <div class="flex gap-4">
                                <span class="flex-shrink-0 w-8 h-8 bg-red-100 text-red-700 rounded-full flex items-center justify-center font-bold text-sm"><?= $step['step'] ?></span>
                                <div>
                                    <h4 class="font-medium"><?= $step['title'] ?></h4>
                                    <p class="text-sm text-gray-600"><?= $step['description'] ?></p>
                                    <?php if (isset($step['link'])): ?>
                                        <a href="<?= $step['link'] ?>" target="_blank" class="text-sm text-blue-600 underline">Open →</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    </div>
                </details>
            </div>
        </div>

        <!-- AI Provider Guides -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">2. 🤖 AI Provider Setup</h3>

            <div class="space-y-4">
                <?php foreach ($aiGuides as $key => $guide): ?>
                <details class="bg-white rounded-lg shadow" <?= $config['ai']['provider'] === $key ? 'open' : '' ?>>
                    <summary class="px-6 py-4 cursor-pointer font-semibold hover:bg-gray-50"><?= $guide['title'] ?></summary>
                    <div class="px-6 pb-4 space-y-4">
                    <?php foreach ($guide['steps'] as $step): ?>
                        <div class="flex gap-4">
                            <span class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-bold text-sm"><?= $step['step'] ?></span>
                            <div>
                                <h4 class="font-medium"><?= $step['title'] ?></h4>
                                <p class="text-sm text-gray-600"><?= $step['description'] ?></p>
                                <?php if (isset($step['link'])): ?>
                                    <a href="<?= $step['link'] ?>" target="_blank" class="text-sm text-blue-600 underline">Open →</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </details>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Hosting Guide -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4">3. 🚀 Free Hosting Setup</h3>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 space-y-4">
                <?php foreach ($hostingGuide['steps'] as $step): ?>
                    <div class="flex gap-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center font-bold text-sm"><?= $step['step'] ?></span>
                        <div>
                            <h4 class="font-medium"><?= $step['title'] ?></h4>
                            <p class="text-sm text-gray-600"><?= $step['description'] ?></p>
                            <?php if (isset($step['link'])): ?>
                                <a href="<?= $step['link'] ?>" target="_blank" class="text-sm text-blue-600 underline">Open →</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
