.PHONY: ci test phpunit phpcs stan psalm parser

ci: test cs
test: phpunit parser
cs: phpcs stan psalm

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

phpcs:
	cd ../.. && vendor/bin/phpcs -p -s --standard=extensions/ExternalContent/phpcs.xml

stan:
	../../vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G

psalm:
	../../vendor/bin/psalm --config=psalm.xml

parser:
	php ../../tests/parser/parserTests.php --file=tests/parser/parserTests.txt
