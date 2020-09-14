# Sobre
Este projeto foi desenvolvido como parte de um processo seletivo.

## Arquitetura
O caminho que tentei trilhar no decorrer do projeto é simples: Utilizar uma arquitetura de portas e adapters para 
que seja simples mutações futuras e que suporte novas features de maneira natural.

#### Slim
Utilizei o Slim pelo fato de estar mais compilance com Psr's a nível de modularização,
isso me permitiu baixo ou nenhum acoplamento ao framework.
Usei também pelo "modularismo" dele, que me permitiu usar só o que eu precisaria usar, sem recursos desnecessários.

## Infraestrutura
#### Banco de dados
Utilizei MongoDB para desenvolver a camanda de persitência com algumas observações:
* O banco é totalmente desacoplado, as chaves primárias não referenciam o identificador de entidade

### Cache / Algoritmo de bloqueio de saldo
Utilizei Redis para fazer uma implementação de lock para impedir que sejam feitas várias transações ao mesmo tempo.

### Mensageria / processamento assíncrono
Queria utilizar algo como RabbitMQ para enfileirar processamento assincrono.

### Log
A camada de log está relativamente completa. Está gravando em um arquivo por ora.

### Desenvolvimento local
Utilizando docker multistage, montei uma pequena "herança" onde a imagem que possivelmente iria para produção sofre
alguns incrementos em um estágio de desenvolvimento. Isso facilita a manutenção
do Dockerfile e acho muito mais "semantico" e intuitivo.

### Compose
Utilizei docker-compose para prover todo o ambiente local.

# Como rodar a bagaça
```sh
// não se preocupe em gerar dados, o container é iniciado com uma migration
só pra possibilitar a request solicitada no teste

// criar o env da aplicação com base no exemplo
make setup

// subir
make up

// quer ver logs?
make spy

// rodar os testes
make test

// rodar análise estática
make analyze

// caso não queira abrir o postman, o curl tá salvo em
make request
```

# O que mais eu queria ter feito
Tá cheio de todo espalhado pelo código :/

Basicamente, o que queria ter melhorado mas não deu tempo é:
* mensageria para parte de notificação assincrona
* retentativa de notificação
* aplicar cqrs e event sourcing :/
* melhorar como são instanciadas as actions (de fato seguir um padrão de comando)
* aumentar o coverage
* implementar tudo de novo em GoLang :p
