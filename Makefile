migrate:
	@docker compose exec php bin/console doctrine:migrations:migrate -n

migrate_prev:
	@docker compose exec php bin/console doctrine:migration:migrate -n prev

run-fixtures: migrate
	@docker compose exec php bin/console doctrine:fixture:load -n

schema-validate: migrate
	@SQL=$$(docker compose exec php bin/console doctrine:schema:update --dump-sql); \
	if [ "$$SQL" != "DROP TABLE doctrine_migration_versions;" ]; then \
		docker compose exec php bin/console doctrine:schema:validate -v; \
	fi

test:
	@docker compose exec php vendor/bin/paratest
test-coverage:
	@docker compose exec php vendor/bin/paratest -p 4 --runner=WrapperRunner --coverage-html=COVERAGE-HTML --log-junit=junit.xml --coverage-cobertura=cobertura.xml --coverage-clover=coverage-clover.xml
