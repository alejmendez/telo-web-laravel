# Gestión de servicios (sin nginx — FrankenPHP maneja todo)
alias stop_services="sudo systemctl stop octane queue-worker"
alias start_services="sudo systemctl start valkey octane queue-worker"
alias restart_services="stop_services && start_services"
alias status_services="sudo systemctl status octane queue-worker valkey"

# Actualizaciones — los assets JS los sube GitHub Actions, no se compilan aquí
alias update_app="stop_services && git pull && composer install --optimize-autoloader --no-dev && composer dump-autoload && php artisan optimize && start_services"
alias update_app_with_migrations="stop_services && git pull && composer install --optimize-autoloader --no-dev && php artisan migrate --force && php artisan optimize && start_services"

# Logs
alias logs_octane="sudo journalctl -u octane -f"
alias logs_queue="sudo journalctl -u queue-worker -f"
alias logs_caddy="sudo journalctl -u octane -f --grep='caddy'"