# pgBoard - A Lightweight PHP + PostgreSQL Messageboard

A minimalist forum system that supports threads, posts, private messages, member profiles, search functionality, and real-time chat.

## Requirements

* nginx
* PHP 8.5
* PostgreSQL 14.4x
* Sphinx 3.1.1

## Caveats

* Currently the board software does not support being installed in a subdirectory (e.g., http://www.mywebsite.com/board/). It must be installed at the domain root.

## Quick Start with Docker

For local development using Docker:

```bash
make init
```

This command will:
1. Copy configuration files from defaults to `./config/`
2. Create `.env` file from `.example.env`
3. Build and start Docker containers
4. Install Composer dependencies
5. Create the database schema
6. Seed the database with sample data

Access the application at http://localhost:8080

## Manual Installation

If not using the `make init` command, follow these steps:

1. **Create database in PostgreSQL**
   ```bash
   createdb board
   ```

2. **Run SQL creation scripts** in `/doc/` in order:
   - `1-Schema.sql`
   - `2-Functions.sql`
   - `3-Indexes-FKeys-Triggers.sql`
   - (Skip `0-Migrate.sql`)

3. **Copy and configure files:**
   - `config.default.php` → `config/config.php`
   - `lang/en.default.php` → `config/lang/en.php`
   - `lang/en_header.default.php` → `config/lang/en_header.php`
   - `lang/en_footer.default.php` → `config/lang/en_footer.php`
   - `class/Plugin.default.php` → `config/class/Plugin.php`

4. **Install dependencies**
   ```bash
   composer install
   ```

## Available Make Commands

### Container Management
- `make start` - Start Docker containers
- `make stop` - Stop Docker containers
- `make rebuild` - Stop, rebuild, and restart containers
- `make build-up` - Build and start containers

### Development Tools
- `make php-shell` - Open a shell in the web container
- `make db-shell` - Open a PostgreSQL shell
- `make phpstan` - Run static analysis
- `make phpunit` - Run tests
- `make xdebug-on` - Enable Xdebug
- `make xdebug-off` - Disable Xdebug

### Database Management
- `make db-drop` - Drop the database
- `make db-create` - Create database and run schema scripts
- `make db-refresh` - Drop and recreate database
- `make db-seed` - Seed database with sample data
- `make db-reseed` - Refresh database and reseed

## Docker Compose

The project includes two Docker Compose configurations:

- **`docker-compose.yml`** - Local development (port 8080)
- **`docker-compose.prod.yml`** - Production deployment (ports 80/443)

### Local Development
```bash
docker compose up -d
```

### Production Deployment
```bash
docker compose -f docker-compose.prod.yml up -d
```

## Testing

Run the test suite:
```bash
make phpunit
# or
vendor/bin/phpunit
```

Run static analysis:
```bash
make phpstan
# or
vendor/bin/phpstan analyse
```

## Architecture Overview

### Request Flow
1. Entry point: `index.php` loads `config.php` which includes `core.php`
2. URL routing: `core.php` parses URLs into command array (e.g., `/thread/view/123/`)
3. Command dispatch: `BoardCore::command_parse()` routes to module handlers
4. Module execution: Each module in `module/{name}/` contains GET/POST handlers
5. Output buffering: Content captured and rendered through `index.php` template

### Key Components
- **class/** - Core classes (DB, Security, Parse, etc.)
- **module/** - Feature modules (thread, member, message, search, admin, etc.)
- **config/** - Configuration files (not tracked in git)
- **doc/** - Database schema and SQL scripts

## License

See LICENSE file for details.