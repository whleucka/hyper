# Welcome to Nebula: Your PHP Framework for Modern Web Magic!

[![Discord Community](https://discordapp.com/api/guilds/1139362100821626890/widget.png?style=shield)](https://discord.gg/RMhUmHmNak)
[![Powered by PHP Composer](https://github.com/libra-php/nebula/actions/workflows/php.yml/badge.svg?branch=main)](https://github.com/libra-php/nebula/actions/workflows/php.yml)

🚀 **Unleash the Power of Nebula**: Your Passport to Effortless Web Development!

🔥 **Supercharge Your Website**: Experience Hyperspeed with HTMX - Faster, Smoother, Cooler.

✅ **Craft Your Dreams**: Nebula is Your Creative Canvas - Design, Build, and Shape Your Web Apps with Confidence.

👷 **Work in Progress**: We're Busy Polishing Nebula, So Hang Tight for the Grand Launch!

❌ **A Sneak Peek**: While Nebula is Cosmic, It's Not Ready for Galactic Adventures Just Yet.

## Effortless Launch with Docker

If You Love Docker, Nebula Loves You Back! Launch the Nebula Universe in a Snap! 🚀 

#### Ready-to-Use Containers:
- **nebula-app** (PHP 8.2)
- **nebula-nginx** (Nginx Web Server)
- **nebula-redis** (Redis Store)
- **nebula-mysql** (MySQL 8.0)

Ready to Blast Off:
```bash
docker-compose up --build -d
```

Time to Land:
```bash
docker-compose down
```

## Quick Start Guide

Begin Your Nebula Journey with These Simple Steps:

1. **Installation**: Get Started by Cloning the Nebula Repository and Installing Dependencies Using Composer.
```bash
git clone https://github.com/libra-php/nebula.git
cd nebula
composer install
```

2. **Configuration**: Customize Nebula to Suit Your Needs. Adjust Database Settings, Configurations - Your Web Universe, Your Rules!
```bash
cp .env.example .env
```

3. **Dependencies**: Nebula Relies on Key Helpers:
    - Redis: Boost Speed (Off by Default).
    - MySQL: For Storing Your Galactic Data (On by Default).

4. **NOTE**: To Avoid Glitches, Align Permissions for Logs & Cache, e.g., `chown -R www-data:www-data logs/`.

5. **Embark on the Cosmic Voyage**: Develop Controllers, Views, Models in the `src` Universe. Test Locally with `./nebula -s`.

### Discover the Admin Backend

Coming Soon: Nebula's Cosmic Admin Backstage Pass! Accessible via `/admin/sign-in`. Control User Registration with Ease - Update the `.env` Console with `ADMIN_REGISTER_ENABLED=true`. More to come soon! 

### Explore the Documentation Hub

- [Configuration Guide](docs/CONFIG.md)
- [Command Console Manual](docs/CONSOLE.md)
- [Helpers Handbook](docs/HELPERS.md)
- [Database Journey](docs/DATABASE.md)
    - [Migrations Guide](docs/MIGRATIONS.md)
- [Routing Roadmap](docs/ROUTING.md)
- [Middleware Insights](docs/MIDDLEWARE.md)
- [Controllers Demystified](docs/CONTROLLERS.md)
    - [Validation 101](docs/VALIDATION.md)
- [Views Vision](docs/VIEWS.md)
- [Models Unveiled](docs/MODELS.md)
    - [Factory Insights](docs/FACTORY.md)

**More Insights Coming Soon!**

**Note**: This AI-Powered Documentation Might Have Hiccups. Give Us a Shout for Corrections!

### Measure the Impact

We're Tweaking Nebula for Peak Performance. Stay Tuned!

- *Try It: `siege -b -c 10 -t 1s $APP_URL`*

### Join the Nebula Crew

We Welcome Contributions! If You Find Glitches or Have Ideas, Raise an Issue or Share a Pull Request.

### License to Shine

This Project Is Under the <a href='https://github.com/libra-php/nebula/blob/main/LICENSE'>MIT License</a>.

### Cheers to Cosmic Allies

Thanks to Symfony, Slim Framework, Leaf, and Laravel for Guiding Nebula's Path!

### Reach Out to Us

Questions? Reach Us at william.hleucka@gmail.com.

🇨🇦 Crafted in Canada
