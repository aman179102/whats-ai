<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - WhatsAI</title>
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
                        <a href="/messages" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">Messages</a>
                        <a href="/contacts" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Contacts</a>
                        <a href="/setup" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Setup Guide</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Message History</h2>

            <?php if (empty($messages)): ?>
                <p class="text-gray-500">No messages yet. Configure WhatsApp provider and send your first message.</p>
            <?php else: ?>
                <div class="space-y-3">
                <?php foreach ($messages as $msg): ?>
                    <div class="flex <?= $msg['direction'] === 'out' ? 'justify-end' : 'justify-start' ?>">
                        <div class="max-w-lg <?= $msg['direction'] === 'out' ? 'bg-blue-100' : 'bg-gray-100' ?> rounded-lg px-4 py-2">
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                <span><?= htmlspecialchars($msg['contact_name'] ?: $msg['contact_phone']) ?></span>
                                <span>•</span>
                                <span><?= $msg['direction'] === 'out' ? 'Sent' : 'Received' ?></span>
                                <span>•</span>
                                <span><?= $msg['created_at'] ?></span>
                            </div>
                            <p class="text-sm"><?= nl2br(htmlspecialchars($msg['content'])) ?></p>
                            <?php if ($msg['ai_provider']): ?>
                                <p class="text-xs text-gray-400 mt-1">🤖 via <?= $msg['ai_provider'] ?> (<?= $msg['ai_model'] ?>)</p>
                            <?php endif; ?>
                            <p class="text-xs text-gray-400 mt-1">Status: <?= $msg['status'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
