setup:
	- cp .env.example .env

up:
	- docker network create teste-network --internal || true
	- docker rm -f $(docker ps -aq) || true
	- docker-compose up -d --build
	- docker-compose exec dev composer install

test:
	- docker-compose exec dev composer run test

analyze:
	- docker-compose exec dev composer run stan-analysis
	- docker-compose exec dev composer run mess-report
	- docker-compose exec dev composer run sniffer-report

sh:
	- docker-compose exec dev sh
