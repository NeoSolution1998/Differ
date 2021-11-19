validate:
	composer validate;
autoload:
	composer dump-autoload;
diff:
	./bin/gendiff;
lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin;
test:
	composer exec --verbose phpunit tests;
update:
	composer update;

