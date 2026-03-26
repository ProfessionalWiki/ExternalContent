.PHONY: ci test cs phpunit phpcs stan psalm parser parser-mw143

ci: test cs
test: phpunit parser
cs: phpcs stan psalm

phpunit:
	php ../../tests/phpunit/phpunit.php -c phpunit.xml.dist

phpcs:
	cd ../.. && vendor/bin/phpcs -p -s --standard=$(shell pwd)/phpcs.xml

stan:
	../../vendor/bin/phpstan analyse --configuration=phpstan.neon --memory-limit=2G

psalm:
	../../vendor/bin/psalm --config=psalm.xml

parser:
	php ../../tests/parser/parserTests.php --file=tests/parser/parserTests.txt

parser-mw143:
	php ../../tests/parser/parserTests.php --file=tests/parser/parserTests-mw143.txt