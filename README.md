# API RESTful - Gerenciamento de Parcelas de Carnê

API Restfull de gerenciamento de carnês.

## Sobre o projeto e seu desenvolvimento

Aplicação de administração de parcelas de carnês. 📝
<br>O projeto é um desafio técnico, parte do processo seletivo para desenvolvedor da TENEX, e tem uma única função: demonstrar um conhecimento sólido e avançado em PHP OOP e criação de API's RESTful.

### Tecnologias utilizadas

<table>
  <tr>
    <td>PHP</td>
    <td>MySQL</td>
  </tr>
  
  <tr>
    <td>8.2.18</td>
    <td>8.3.0</td>
  </tr>
</table>

Nenhum Framework ou biblioteca foram utilizados. 🔥

### Padrões do projeto
- Arquitetura MVC
- PSRs
- API RESTful

## Instruções para a execução do projeto

O projeto já está hospedado, devidamente configurado e pronto para uso no seguinte link:<br>
https://www.heronboares.com.br/tenex.api
<br>
<br>Ele pode ser acessado e testado diretamente por lá através de alguma ferramenta de testes de APIs, como o Postman ou o Insomnia.
<br>São válidas URLs GET ou POST como https://heronboares.com.br/tenex.api/carne
<br>Confira ao final desde documento a lista completa dos endpoints.
<br>
<br>Caso deseje executar o projeto localmente, siga as instruções abaixo.

### Para instalar e rodar localmente

É necessário ter instalado o PHP (versão 8.0 ou superior, necessariamente) e o MySQL.

1) Baixe todo o conteúdo e coloque em uma pasta.

3) Rode todas as querys do arquivo banco.sql e atualize o arquivo Config.php com as credenciais de acesso corretas.
<br>  -> Note que o comando para criar o banco já está no script SQL, com o mesmo nome que está configurado por padrão no Config.php 

2) Acesse a pasta /public via CLI e rode o seguinte comando 
~~~
PHP -S localhost:8000
~~~

4) Teste o projeto 😎
<br>

⚠️ Atenção ⚠️ 
<br>Qualquer problema com o autoload pode ser resolvido com o seguinte comando:
~~~
composer dump
~~~

## Lista dos endpoints e requisições

<br>Por questões de simplificação, todos os endpoints abaixo terão como base o endereço tenex.api/
<br> Esse endereço pode variar de acordo com o local onde você está testando o projeto, substitua "tenex.api/" por "http://localhost:8000/" se você estiver rodando no seu ambiente local de acordo com as instruções de instalação acima ou por "www.heronboares.com.br/tenex.api/" se estiver testando pelo deploy supracitado.
<br>
<br>

### GET

* tenex.api/
<br>Home request da API
~~~
Retorno: 200 (OK)
{
    "Nome": "API restfull, para gerenciamento de parcelas de carnês.",
    "Instrução": "Acesse a documentação para descobrir os endpoints disponíveis",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~
<br>

* tenex.api/carne
<br>Home request do controller de carnês
~~~
Retorno: 200 (OK)
{
    "Instrução": "Inclua o id do carnê buscado para receber as suas, informações ou acesse a documentação para descobrir os endpoints disponíveis",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~
<br>

* tenex.api/carne/{id}
<br>Para buscar por um carnê específico, substitua {id} pelo id do carnê
~~~
Retorno: 200 (OK)
{
    "data_vencimento": "2024-08-22",
    "valor": 200,
    "numero": "parcela = 1",
    "entrada": true
},
{
    "data_vencimento": "2024-09-06",
    "valor": 266.67,
    "numero": "parcela = 2",
    "entrada": false
},
{
    "data_vencimento": "2024-09-13",
    "valor": 266.67,
    "numero": "parcela = 3",
    "entrada": false
},
{
    "data_vencimento": "2024-09-20",
    "valor": 266.66,
    "numero": "parcela = 4",
    "entrada": false
}
~~~
Caso não haja nenhum carnê cadastrado com o id solicitado:
~~~
Retorno: 400 (Bad Request)
{
    "Erro": "Carnê não encontrado"
}
~~~

### POST

* tenex.api/carne <br>
Para a criação de um novo carnê
~~~
Requisição:
{
    "valor_total": 1000.00,
    "qtd_parcelas": 4,
    "data_primeiro_vencimento": "2024/08/30",
    "periodicidade": "semanal",
    "valor_entrada": 200
}
~~~
~~~
Retorno: 200 (OK)
{
    "total": 1000,
    "valor_entrada": 200,
    "parcelas": [
        {
            "data_vencimento": "2024-08-22",
            "valor": 200,
            "numero": "parcela = 1",
            "entrada": true
        },
        {
            "data_vencimento": "2024-09-06",
            "valor": 266.67,
            "numero": "parcela = 2",
            "entrada": false
        },
        {
            "data_vencimento": "2024-09-13",
            "valor": 266.67,
            "numero": "parcela = 3",
            "entrada": false
        },
        {
            "data_vencimento": "2024-09-20",
            "valor": 266.66,
            "numero": "parcela = 4",
            "entrada": false
        }
    ]
}
~~~
O argumento valor_entrada é opcional, pode ser omitido, mas todos os outros parâmetros são obrigatórios e devem ser enviados exatamente como estão descritos.

⚠️ Atenção ⚠️
<br>Qualquer requisição inválida ou que infringir alguma regra de negócio receberá uma resposta com status code 400 (Bad Request) contendo o motivo da requisição não ser válida e instruções do que fazer.
<br>Exemplos:
~~~
Retorno: 400 (Bad Request)
{
    "Erro": "O valor total do carnê não pode ser igual ou menor que zero. A entrada, se existir, precisa ser maior que zero",
    "Instruções": "Consulte a documentação para entender o formato exato dos parâmetros de entrada",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~
~~~
Retorno: 400 (Bad Request)
{
    "Erro": "Não é possível dar entrada em um carnê de uma única parcela",
    "Instruções": "Consulte a documentação para entender o formato exato dos parâmetros de entrada",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~
~~~
Retorno: 400 (Bad Request)
{
    "Erro": "O valor da entrada não pode ser igual ou maior que o valor total do carnê",
    "Instruções": "Consulte a documentação para entender o formato exato dos parâmetros de entrada",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~
~~~
Retorno: 400 (Bad Request)
{
    "Erro": "A data de vencimento especificada não existe. É possível que tenha sido inserido um dia 31 em um mês que tem apenas 30 dias",
    "Instruções": "Consulte a documentação para entender o formato exato dos parâmetros de entrada",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~
~~~
Retorno: 400 (Bad Request)
{
    "Erro": "Data de vencimento em formato incorreto, são válidos: 'yyyy-mm-dd' e 'yyyy/mm/dd'",
    "Instruções": "Consulte a documentação para entender o formato exato dos parâmetros de entrada",
    "Documentação": "https://github.com/HeronPBV/Carnes-restfull-API"
}
~~~

Entre outras. Há diversas validações para garantir que só sejam processadas requisições que façam sentido.
