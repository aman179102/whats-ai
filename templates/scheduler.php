<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scheduler - WhatsAI</title>
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
                        <a href="/scheduler" class="px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-blue-700">Scheduler</a>
                        <a href="/messages" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Messages</a>
                        <a href="/contacts" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Contacts</a>
                        <a href="/setup" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900">Setup Guide</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-4 py-8">
        <?php if (isset($_GET['created'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">Schedule created successfully!</div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold mb-4">Create New Schedule</h2>
            <form method="POST" action="/scheduler/create" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Schedule Name</label>
                    <input type="text" name="name" required class="w-full border rounded-lg px-3 py-2" placeholder="e.g., Morning Greeting">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Message</label>
                    <textarea name="message_template" rows="3" class="w-full border rounded-lg px-3 py-2" placeholder="Hello {name}, this is an automated message..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Use {name} for contact name. Leave empty if AI-generated.</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="ai_generated" value="1" id="aiGen" class="mr-2" onchange="document.getElementById('aiPrompt').classList.toggle('hidden', !this.checked)">
                    <label for="aiGen" class="text-sm font-medium text-gray-700">Generate message using AI</label>
                </div>

                <div id="aiPrompt" class="hidden">
                    <label class="block text-sm font-medium text-gray-700">AI Prompt</label>
                    <textarea name="ai_prompt" rows="2" class="w-full border rounded-lg px-3 py-2" placeholder="Write a friendly greeting message for {name}"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Schedule Date & Time</label>
                    <input type="datetime-local" name="scheduled_at" required class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Repeat</label>
                    <select name="repeat_type" class="w-full border rounded-lg px-3 py-2">
                        <option value="once">Once</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Select Contacts</label>
                    <div class="mt-2 max-h-40 overflow-y-auto border rounded-lg p-2 space-y-2">
                        <?php if (empty($contacts)): ?>
                            <p class="text-sm text-gray-500">No contacts yet. <a href="/contacts" class="text-blue-600 underline">Add contacts</a></p>
                        <?php else: ?>
                            <?php foreach ($contacts as $contact): ?>
                            <label class="flex items-center">
                                <input type="checkbox" name="contact_ids[]" value="<?= $contact['id'] ?>" class="mr-2">
                                <span class="text-sm"><?= htmlspecialchars($contact['name']) ?> (<?= htmlspecialchars($contact['phone']) ?>)</span>
                            </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Create Schedule
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Scheduled Messages</h2>
            <?php if (empty($schedules)): ?>
                <p class="text-gray-500">No schedules yet. Create your first schedule above.</p>
            <?php else: ?>
                <div class="space-y-4">
                <?php foreach ($schedules as $schedule): ?>
                    <div class="border rounded-lg p-4" id="schedule-<?= $schedule['id'] ?>">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-semibold"><?= htmlspecialchars($schedule['name']) ?></h3>
                                <p class="text-sm text-gray-600">
                                    <?= $schedule['ai_generated'] ? '🤖 AI Generated' : '📝 Manual Message' ?>
                                    | Repeat: <?= $schedule['repeat_type'] ?>
                                    | Status: <span class="font-medium status-badge"><?= $schedule['status'] ?></span>
                                </p>
                                <p class="text-xs text-gray-500">Scheduled: <?= $schedule['scheduled_at'] ?></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full status-badge
                                    <?= $schedule['status'] === 'active' ? 'bg-green-100 text-green-800' : '' ?>
                                    <?= $schedule['status'] === 'paused' ? 'bg-yellow-100 text-yellow-800' : '' ?>
                                    <?= $schedule['status'] === 'completed' ? 'bg-blue-100 text-blue-800' : '' ?>
                                    <?= $schedule['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : '' ?>
                                "><?= $schedule['status'] ?></span>
                                <?php if ($schedule['status'] === 'active'): ?>
                                    <button onclick="scheduleAction(<?= $schedule['id'] ?>, 'pause')" class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Pause</button>
                                    <button onclick="scheduleAction(<?= $schedule['id'] ?>, 'cancel')" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Cancel</button>
                                <?php elseif ($schedule['status'] === 'paused'): ?>
                                    <button onclick="scheduleAction(<?= $schedule['id'] ?>, 'resume')" class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">Resume</button>
                                    <button onclick="scheduleAction(<?= $schedule['id'] ?>, 'cancel')" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Cancel</button>
                                <?php endif; ?>
                                <button onclick="scheduleAction(<?= $schedule['id'] ?>, 'delete')" class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Delete</button>
                            </div>
                        </div>
                        <?php if ($schedule['message_template']): ?>
                            <p class="mt-2 text-sm text-gray-700"><?= htmlspecialchars(substr($schedule['message_template'], 0, 100)) ?>...</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        async function scheduleAction(id, action) {
            if (action === 'delete' && !confirm('Delete this schedule?')) return;
            if (action === 'cancel' && !confirm('Cancel this schedule?')) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('action', action);

            try {
                const res = await fetch('/api/schedule/action', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    if (action === 'delete') {
                        document.getElementById('schedule-' + id).remove();
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Failed: ' + (data.error || 'Unknown error'));
                }
            } catch (e) {
                alert('Network error: ' + e.message);
            }
        }
    </script>
</body>
</html>
