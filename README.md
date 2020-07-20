# Controle Financeiro

Esta api foi desenvolvida em lumen um framework laravel mais enxuto. O projeto consiste em realizar operações financeiras: Debito, Credito, Transferencia, Saldo e Extrato.

## Tecnologias Utilizadas

- [PHP 7.2](https://www.php.net)
- [Lumen 7.0](https://laravel.com)
- [MySQL](https://www.mysql.com)

## Configurações

### Código Fonte
git clone https://github.com/ValdirJunior/seller-project.git

### Banco de Dados
Após configurado o arquivo .env, entrar na pasta raiz do projeto via terminal e executar o comando: php artisan migrate.

### Servidor Local
Entrar na pasta raiz do projeto via terminal, e executar o comando: php -S localhost:8000 -t public.

## Rotas

### Criar Conta

* **URL**
  `api/conta/criar`

* **Method**
  `POST`

*  **Parâmetros via body**


   | Atributo     | Tipo do dado        | Descrição                                    | Obrigatório     | Valor padrão     | Exemplo     |
   |----------    |--------------       |------------------------------------------    |-------------    |--------------    |------------ |
   | cpf          | alfanumérico        | cpf da conta                                 | sim             | -                | 22037117019 |
   | saldo        | numerico            | Saldo da conta                               | não             | - 0.00           | 350.00      |

* **Retorno**
  
  **Status Code:** 201
  
    ```json
    {
        "cpf": "22037117019",
        "id": 4
    }
    ```

### Consultar Todas as Contas

* **URL**
  `api/conta/saldo`

* **Method**
  `GET`

* **Retorno**
  
  **Status Code:** 200
  
    ```json
    {
        {
            "id": 1,
            "cpf": "94120833003",
            "saldo": 100
        },
        {
            "id": 3,
            "cpf": "22037117019",
            "saldo": 0
        }
    }
    ```    

### Consultar Conta Específica

* **URL**
  `api/conta/saldo/{cpf}`

* **Method**
  `GET`

*  **Parâmetros via url**


   | Atributo     | Tipo do dado        | Descrição                                    | Obrigatório     | Valor padrão     | Exemplo     |
   |----------    |--------------       |------------------------------------------    |-------------    |--------------    |------------ |
   | cpf          | alfanumérico        | cpf da conta                                 | sim             | -                | 22037117019 |   

* **Retorno**
  
  **Status Code:** 200
  
    ```json
    {
        "id": 3,
        "cpf": "22037117019",
        "saldo": 0
    }
    ```

### Debito

* **URL**
  `api/conta/debito

* **Method**
  `POST`

*  **Parâmetros via body**


   | Atributo     | Tipo do dado        | Descrição                                    | Obrigatório     | Valor padrão     | Exemplo     |
   |----------    |--------------       |------------------------------------------    |-------------    |--------------    |------------ |
   | cpf          | alfanumérico        | cpf da conta                                 | sim             | -                | 22037117019 |  
   | valor        | numerico            | valor do debito                              | sim             | -                | 350.00      |    

* **Retorno**
  
  **Status Code:** 200
  
    ```json
    {
        "sucess": true,
        "message": "Debito realizado com sucesso"
    }
    ```

### Credito

* **URL**
  `api/conta/credito

* **Method**
  `POST`

*  **Parâmetros via body**


   | Atributo     | Tipo do dado        | Descrição                                    | Obrigatório     | Valor padrão     | Exemplo     |
   |----------    |--------------       |------------------------------------------    |-------------    |--------------    |------------ |
   | cpf          | alfanumérico        | cpf da conta                                 | sim             | -                | 22037117019 |  
   | valor        | numerico            | valor do credito                             | sim             | -                | 350.00      |    

* **Retorno**
  
  **Status Code:** 200
  
    ```json
    {
        "sucess": true,
        "message": "Credito realizado com sucesso"
    }
    ```    

### Tranferencia

* **URL**
  `api/conta/tranferencia

* **Method**
  `POST`

*  **Parâmetros via body**


   | Atributo     | Tipo do dado        | Descrição                                    | Obrigatório     | Valor padrão     | Exemplo     |
   |----------    |--------------       |------------------------------------------    |-------------    |--------------    |------------ |
   | cpf_origem   | alfanumérico        | cpf da conta                                 | sim             | -                | 22037117019 |
   | cpf_destino  | alfanumérico        | cpf da conta                                 | sim             | -                | 94120833003 |    
   | valor        | numerico            | valor do tranferencia                        | sim             | -                | 50.00       |    

* **Retorno**
  
  **Status Code:** 200
  
    ```json
    {
        "sucess": true,
        "message": "Transferencia realizada com sucesso"
    }
    ```       

### Extrato

* **URL**
  `api/conta/extrato/{cpf}

* **Method**
  `GET`

*  **Parâmetros via body**


   | Atributo     | Tipo do dado        | Descrição                                    | Obrigatório     | Valor padrão     | Exemplo     |
   |----------    |--------------       |------------------------------------------    |-------------    |--------------    |------------ |
   | cpf          | alfanumérico        | cpf da conta                                 | sim             | -                | 22037117019 |

* **Retorno**
  
  **Status Code:** 200
  
    ```json
    {
      {
          "id": 14,
          "cpf": "22037117019",
          "tipo": "Credito",
          "valor": 50,
          "data": "2020-07-20",
          "observacao": ""
      },
      {
          "id": 15,
          "cpf": "22037117019",
          "tipo": "Debito",
          "valor": 50,
          "data": "2020-07-20",
          "observacao": "Transferecia realizada para 94120833003 no valor 50"
      }
    }
    ```         
