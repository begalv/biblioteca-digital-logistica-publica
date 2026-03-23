.PHONY: up down logs shell migrate migrate-dry validate enrich test backup restore clean

# Ambiente de desenvolvimento
up:
	docker compose --env-file .env -f docker/docker-compose.yml up -d

down:
	docker compose --env-file .env -f docker/docker-compose.yml down

logs:
	docker compose --env-file .env -f docker/docker-compose.yml logs -f

shell:
	docker compose --env-file .env -f docker/docker-compose.yml exec portal python manage.py shell

# Migração de dados (uso: make migrate FILE=/caminho/planilha.xlsx)
migrate:
	docker compose --env-file .env -f docker/docker-compose.yml exec portal python manage.py migrate_spreadsheet $(FILE)

# Migração dry-run
migrate-dry:
	docker compose --env-file .env -f docker/docker-compose.yml exec portal python manage.py migrate_spreadsheet $(FILE) --dry-run

# Validação pós-importação
validate:
	docker compose --env-file .env -f docker/docker-compose.yml exec portal python manage.py validate_import

# Enriquecimento de metadados via IA
enrich:
	docker compose --env-file .env -f docker/docker-compose.yml exec portal python manage.py enrich_metadata

# Testes
test:
	docker compose --env-file .env -f docker/docker-compose.yml exec portal python -m pytest

# Backup do banco de dados
backup:
	docker compose --env-file .env -f docker/docker-compose.yml exec postgres pg_dump -U php nourau > backup_$$(date +%Y%m%d_%H%M%S).sql

# Restaurar banco de dados
restore:
	@echo "Uso: cat backup.sql | docker compose --env-file .env -f docker/docker-compose.yml exec -T postgres psql -U php nourau"

# Limpar volumes e containers
clean:
	docker compose --env-file .env -f docker/docker-compose.yml down -v --remove-orphans
