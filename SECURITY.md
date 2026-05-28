# Security Policy

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.0.x   | ✅ Active |

## Reporting a Vulnerability

We take the security of WhatsAI seriously. If you discover a security vulnerability, please follow these steps:

1. **Do not** disclose the vulnerability publicly
2. Email the details to security@whatsai.app
3. Include a description of the vulnerability and steps to reproduce
4. Allow 48 hours for an initial response

## Security Best Practices

- Never commit `.env` files to version control
- Use environment variables in production instead of `.env`
- Rotate API keys regularly
- Use HTTPS for webhook endpoints
- Verify webhook signatures in production
- Store sensitive data in database settings with proper permissions
- Keep dependencies updated via `composer update`
