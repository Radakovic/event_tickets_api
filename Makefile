test:
	docker compose exec php vendor/bin/paratest
test-coverage:
	docker compose exec php vendor/bin/paratest -p 4 --runner=WrapperRunner --coverage-html=COVERAGE-HTML --log-junit=junit.xml --coverage-cobertura=cobertura.xml --coverage-clover=coverage-clover.xml
