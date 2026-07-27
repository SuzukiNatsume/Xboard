# Xboard

<div align="center">

[![Telegram](https://img.shields.io/badge/Telegram-Channel-blue)](https://t.me/XboardOfficial)
![PHP](https://img.shields.io/badge/PHP-8.2+-green.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-blue.svg)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

</div>

## 📖 Introduction

Xboard is a modern panel system built on Laravel 12, focusing on providing a clean and efficient user experience.
This edition adds a community-owned resource model: users can contribute an
external subscription or connect a self-hosted server, while every contribution
channel is metered independently.

## ✨ Features

- 🚀 Built with Laravel 12 + Octane for significant performance gains
- 🎨 Redesigned admin interface (React + Shadcn UI)
- 📱 Modern user frontend (Vue3 + TypeScript)
- 🐳 Ready-to-use Docker deployment solution
- 🎯 Optimized system architecture for better maintainability
- 🤝 External subscription and self-hosted server contributions
- 🔐 User-owned nodes with independent report tokens and ownership isolation
- 📊 Per-channel monthly traffic quotas and statistics
- 🎓 Registration restricted to `@mails.ucas.ac.cn`

## 🚀 Quick Start (Linux)

The original Docker Compose command remains the recommended deployment method.
It runs on common `linux/amd64` and `linux/arm64` hosts. The host only needs Git,
Docker Engine and Docker Compose v2; PHP, Composer, Redis and the required PHP
extensions are already included in the image.

Before starting, verify:

```bash
git --version
docker --version
docker compose version
```

Then run the original installation command:

```bash
git clone -b compose --depth 1 https://github.com/cedar2025/Xboard && \
cd Xboard && \
docker compose run -it --rm \
    -e ENABLE_SQLITE=true \
    -e ENABLE_REDIS=true \
    -e ADMIN_ACCOUNT=admin@demo.com \
    xboard php artisan xboard:install && \
docker compose up -d
```

> After installation, visit: http://SERVER_IP:7001  
> ⚠️ Make sure to save the admin credentials shown during installation

The installer creates the environment file and runs every database migration
bundled in the selected image. Once this community edition has been published
as the `latest` image, that includes the contribution tables and node ownership
fields. Do not run Composer or install PHP directly on the Linux host when
using this method.

> The unchanged command above intentionally deploys
> `ghcr.io/cedar2025/xboard:latest`. If you maintain a fork, publish its Docker
> image first and replace the `image:` value in `compose.yaml`; cloning source
> code alone does not replace the code contained in a prebuilt image.

### Required scheduler configuration

Laravel's scheduler must run once per minute. It handles statistics, order
checks and the community quota update on the first day of each month. On the
Linux host, open the crontab with `crontab -e` and add the following line,
replacing `/opt/Xboard` with the absolute path where the repository was cloned:

```cron
* * * * * cd /opt/Xboard && /usr/bin/docker compose exec -T xboard php artisan schedule:run >/dev/null 2>&1
```

If Docker is not located at `/usr/bin/docker`, use the path returned by
`command -v docker`.

Verify the deployment:

```bash
docker compose ps
docker compose logs --tail=100 xboard
docker compose exec -T xboard php artisan migrate:status
docker compose exec -T xboard php artisan schedule:list
```

### Updating an existing Linux deployment

Back up the database and `.env` first, then keep using the original Compose
deployment:

```bash
cd /opt/Xboard
docker compose pull
docker compose up -d
```

The current container entrypoint automatically runs `php artisan xboard:update`
on startup. This applies new migrations and refreshes plugins, version caches
and themes. To run the update explicitly or diagnose a failed automatic update:

```bash
docker compose run --rm xboard php artisan xboard:update --no-interaction
docker compose up -d
```

> The all-in-one service in the current Compose template is named `xboard`.
> Older custom templates may use another service name; check it with
> `docker compose config --services` and substitute that name in the commands.

## 📖 Documentation

### 🔄 Upgrade Notice
> 🚨 **Important:** This version involves significant changes. Please strictly follow the upgrade documentation and backup your database before upgrading. Note that upgrading and migration are different processes, do not confuse them.

### Development Guides
- [Plugin Development Guide](./docs/en/development/plugin-development-guide.md) - Complete guide for developing XBoard plugins

### Deployment Guides
- [Deploy with 1Panel](./docs/en/installation/1panel.md)
- [Deploy with Docker Compose](./docs/en/installation/docker-compose.md)
- [Deploy with aaPanel](./docs/en/installation/aapanel.md)
- [Deploy with aaPanel + Docker](./docs/en/installation/aapanel-docker.md) (Recommended)

### Migration Guides
- [Migrate from v2board dev](./docs/en/migration/v2board-dev.md)
- [Migrate from v2board 1.7.4](./docs/en/migration/v2board-1.7.4.md)
- [Migrate from v2board 1.7.3](./docs/en/migration/v2board-1.7.3.md)

## 🛠️ Tech Stack

- Backend: Laravel 12 + Octane
- Admin Panel: React + Shadcn UI + TailwindCSS
- User Frontend: Vue3 + TypeScript + NaiveUI
- Deployment: Docker + Docker Compose
- Caching: Redis + Octane Cache

## 📷 Preview
![Admin Preview](./docs/images/admin.png)

![User Preview](./docs/images/user.png)

## ⚠️ Disclaimer

This project is for learning and communication purposes only. Users are responsible for any consequences of using this project.

## ❤️ Support The Project

If this project has helped you, donations are appreciated. They help support ongoing maintenance and would make me very happy.

TRC20: `TLypStEWsVrj6Wz9mCxbXffqgt5yz3Y4XB`

## 🌟 Maintenance Notice

This project is currently under light maintenance. We will:
- Fix critical bugs and security issues
- Review and merge important pull requests
- Provide necessary updates for compatibility

However, new feature development may be limited.

## 🔔 Important Notes

1. Restart required after modifying admin path:
```bash
docker compose restart
```

2. For aaPanel installations, restart the Octane daemon process

## 🤝 Contributing

Issues and Pull Requests are welcome to help improve the project.

## 📈 Star History

[![Stargazers over time](https://starchart.cc/cedar2025/Xboard.svg)](https://starchart.cc/cedar2025/Xboard)
