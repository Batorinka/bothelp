CMD = docker-compose
PM_EXEC := $(CMD) exec php

up:
	$(CMD) up -d

generate:
	$(PM_EXEC) php artisan message:generate

consume:
	$(PM_EXEC) php artisan message:consume

shell:
	$(PM_EXEC) bash
