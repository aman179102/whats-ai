# 🤖 WhatsAI - WhatsApp AI Automation

WhatsApp automation with AI-powered auto-reply and scheduling. Multiple WhatsApp providers + multiple AI providers.

## 🚀 Quick Start

```bash
# 1. Install PHP dependencies
composer install

# 2. Start server
php -S localhost:8080 -t public
```

Open **http://localhost:8080** in browser → Settings → configure AI + WhatsApp.

## 📱 WhatsApp Setup (Easiest Way)

### Option 1: WhatsApp Web Bridge (Free, No API Key)
```bash
cd bridge
npm install
npm start
```
Scan QR code with phone → Done!

### Option 2: Meta Cloud API (Official)
Get credentials from [Meta Developer Console](https://developers.facebook.com/) → paste in Settings.

### Option 3: Twilio (Official)
Get credentials from [Twilio Console](https://console.twilio.com/) → paste in Settings.

## 🤖 AI Providers

| Provider | Setup |
|----------|-------|
| **Google Gemini** (free tier) | Get key from [AI Studio](https://aistudio.google.com/) |
| **OpenRouter** | Get key from [openrouter.ai](https://openrouter.ai/) |
| **Groq** (free tier) | Get key from [console.groq.com](https://console.groq.com/) |
| **Custom API** | Any OpenAI-compatible API |

## ✨ Features

- **AI Auto-Reply** — Incoming messages get AI-powered responses
- **Scheduled Messages** — Daily/weekly/monthly repeats
- **Dashboard** — Live status, send test messages
- **Dark/Light Theme** — Toggle from any page
- **Free Hosting Ready** — Works on Render, Railway

## 📂 Structure

```
├── bridge/          # WhatsApp Web bridge (Node.js)
├── public/          # Web UI (Tailwind CSS)
├── src/
│   ├── AI/          # AI providers (Gemini, OpenRouter, Groq, Custom)
│   ├── WhatsApp/    # WhatsApp providers
│   ├── Core/        # Core framework
│   └── Webhook/     # Message handlers
├── templates/       # HTML templates
├── cron/            # Scheduler runner
└── config/          # Configuration
```

## 🏠 Self Hosting

Deploy free on [Render](https://render.com/) or [Railway](https://railway.app/). See Setup Guide at `/setup` in the app.

## ⚠️ Warning

WhatsApp Web Bridge is unofficial. Use at your own risk.
