<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts - WhatsAI</title>
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
                        <a href="/" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Dashboard</a>
                        <a href="/settings" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Settings</a>
                        <a href="/scheduler" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Scheduler</a>
                        <a href="/messages" class="px-3 py-2 rounded-md text-sm font-medium" style="color:var(--text-secondary)">Messages</a>
                        <a href="/contacts" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">Contacts</a>
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

    <main class="max-w-4xl mx-auto px-4 py-8">
        <?php if (isset($_GET['added'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">Contact added successfully!</div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Add Contact Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Add Contact</h2>
                <form method="POST" action="/contacts/add" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2" placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" name="phone" required class="w-full border rounded-lg px-3 py-2" placeholder="919876543210">
                        <p class="text-xs text-gray-500 mt-1">Include country code without +. e.g., 919876543210</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Provider</label>
                        <select name="provider" class="w-full border rounded-lg px-3 py-2">
                            <option value="meta">Meta</option>
                            <option value="twilio">Twilio</option>
                            <option value="webscraper">Web Scraper</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Add Contact
                    </button>
                </form>
            </div>

            <!-- Contacts List -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Contacts (<?= count($contacts) ?>)</h2>
                <?php if (empty($contacts)): ?>
                    <p class="text-gray-500">No contacts yet. Add your first contact.</p>
                <?php else: ?>
                    <div class="space-y-2">
                    <?php foreach ($contacts as $contact): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($contact['name']) ?></p>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($contact['phone']) ?></p>
                            </div>
                            <span class="text-xs text-gray-400"><?= $contact['provider'] ?></span>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="/assets/js/app.js"></script>
</body>
</html>
