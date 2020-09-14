db = db.getSiblingDB('teste_pp');

db.createUser({
    user: 'teste_pp',
    pwd: 'dev',
    roles: [
        { role: 'dbOwner', db: 'teste_pp' }
    ]
});

db.createCollection('transaction');
db.createCollection('customer');

db.getCollection('customer').insertMany([
    {
        id: "93713836-709c-4285-b511-e96df72188ff",
        external_code: 4,
        name: "Jeferson Capobianco",
        document: {
            type: "CPF",
            number: "000.000.000-00"
        },
        user: {
            email: "jefersoncapobianco@gmail.com",
            password: "teste"
        }
    },
    {
        id: "517f9f76-d98f-4b19-94de-c9fa88e0a39b",
        external_code: 15,
        name: "Code Monster",
        document: {
            type: "CNPJ",
            number: "00.000.000/0000-00"
        },
        user: {
            email: "jefersoncapobianco@gmail.com",
            password: "teste"
        }
    }
]);

db.getCollection('transaction').insertOne({
    id: "c690a439-9dab-41df-ac5f-4aa18e380601",
    payer: "517f9f76-d98f-4b19-94de-c9fa88e0a39b",
    payee: "93713836-709c-4285-b511-e96df72188ff",
    value: 1000.0,
    date: "2020-09-14 01:58:51.426651"
})
