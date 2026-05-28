# Contributing to WhatsAI

First off, thank you for considering contributing! We welcome all contributions.

## Development Setup

1. Fork the repository
2. Clone your fork
3. Run `composer install`
4. Copy `.env.example` to `.env` and configure your settings
5. Run `php -S localhost:8080 -t public` to start development server

## Coding Standards

- Follow PSR-12 coding style
- Run `php-cs-fixer fix` before committing
- Write PHPUnit tests for new features
- Keep methods focused and small

## Pull Request Process

1. Create a feature branch from `main`
2. Make your changes with descriptive commit messages
3. Run `phpunit` to ensure tests pass
4. Update CHANGELOG.md if applicable
5. Submit the pull request

## Commit Messages

Follow conventional commits: `type(scope): description`

Types: feat, fix, docs, style, refactor, test, chore, ci

## Questions?

Open an issue or start a discussion.
