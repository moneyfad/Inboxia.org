# Inboxia Mail

A modern email service platform with Mailcow integration, built with PHP and SquirrelMail.

## Features

- User registration with email account creation
- Cloudflare Turnstile captcha integration
- Password requirements (8-32 characters)
- Recovery email for password reset
- Mailcow API integration for mailbox management
- SquirrelMail webmail interface
- Multi-domain support

## Requirements

- PHP 7.4+
- MySQL/MariaDB
- Mailcow server with API access
- Cloudflare Turnstile account

## Installation

1. Clone this repository
2. Copy `config.example.php` to `config.php`
3. Update `config.php` with your database and API credentials
4. Run `setup.php` to create database tables
5. Configure your web server to serve the application
6. Delete `setup.php` for security

## Configuration

Edit `config.php` to set:
- Database credentials
- Mailcow API URL and key
- Cloudflare Turnstile keys
- Email domains
- IMAP/SMTP server settings

## Security Notes

- Never commit `config.php` with real credentials
- Delete `setup.php` after installation
- Ensure proper file permissions
- Use HTTPS in production

## License

This project is proprietary software. All rights reserved.