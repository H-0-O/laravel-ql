
.PHONY: test-f

test-f:
	rm -f storage/logs/QL.log && vendor/bin/phpunit --testsuite=Feature


