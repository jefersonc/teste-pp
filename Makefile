setup:
	- cp .env.example .env

up:
	- docker network create teste-network --internal || true
	- docker rm -f $(docker ps -aq) || true
	- docker-compose up -d --build
	- docker-compose exec dev composer install

spy:
	- tail -f log

test:
	- docker-compose exec dev composer run test

analyze:
	- docker-compose exec dev composer run stan-analysis
	- docker-compose exec dev composer run mess-report
	- docker-compose exec dev composer run sniffer-report

request:
	- curl -v -X POST http://localhost:8080/transaction -H 'Content-Type: application/json' -d '{"value" : 100.00,"payer" : 4,"payee" : 15 }'
