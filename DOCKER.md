# UoJ_AMS Docker Setup

This project can be run using Docker Compose with PHP, MySQL, and phpMyAdmin.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running

## Quick Start

1. **Start the containers:**
   ```bash
   docker-compose up -d
   ```

2. **Wait for MySQL to initialize** (first run takes ~30 seconds)

3. **Access the application:**
   - **Web App:** http://localhost:8080
   - **phpMyAdmin:** http://localhost:8081

4. **Create your first admin account:**
   - Go to http://localhost:8080
   - Click "Don't have an account?" and register
   - Open phpMyAdmin (http://localhost:8081)
   - Login with `root` / `uoj_secret_password`
   - Run this SQL to make your account an admin:
     ```sql
     USE uoj;
     UPDATE uoj_user SET user_status = 1, user_role = 0 WHERE user_id = 1;
     ```

## Services

| Service | URL | Description |
|---------|-----|-------------|
| Web App | http://localhost:8080 | Main PHP application |
| phpMyAdmin | http://localhost:8081 | Database management UI |
| MySQL | localhost:3306 | Database server |

## Default Credentials

### MySQL / phpMyAdmin
- **Username:** root
- **Password:** uoj_secret_password

### Application
- Create your own account through the registration page

## Commands

```bash
# Start containers in background
docker-compose up -d

# View logs
docker-compose logs -f

# Stop containers
docker-compose down

# Stop and remove volumes (DELETES DATABASE!)
docker-compose down -v

# Rebuild containers (after Dockerfile changes)
docker-compose up -d --build

# Enter PHP container shell
docker exec -it uoj_ams_web bash

# Enter MySQL shell
docker exec -it uoj_ams_db mysql -uroot -puoj_secret_password uoj
```

## Troubleshooting

### Database connection errors
Wait 30 seconds after `docker-compose up` for MySQL to fully initialize.

### Permission issues
If you get file permission errors, run:
```bash
docker exec -it uoj_ams_web chown -R www-data:www-data /var/www/html/UoJ_AMS/res
```

### Port conflicts
If ports 8080, 8081, or 3306 are in use, edit `docker-compose.yml` to change the port mappings.

## Development

The project directory is mounted as a volume, so any changes you make to PHP files are immediately reflected in the container.

### Database Persistence
MySQL data is stored in a Docker volume (`uoj_mysql_data`). Your data persists between container restarts. Use `docker-compose down -v` to completely reset the database.
