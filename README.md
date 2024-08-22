# api-carnes
API Restfull de gerenciamento de carnês




# API RESTful - Gerenciamento de Parcelas de Carnê

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
<br>Ele pode ser acessado e testado diretamente por lá através de alguma ferramenta de testes de APIs, como o Postman ou o Insomnia
<br>São válidas URLs GET ou POST como https://heronboares.com.br/tenex.api/carne
<br>Confira ao final desde documento a lista completa dos endpoints.
<br>
<br>Caso deseje executar o projeto localmente, siga as instruções abaixo.

### Para instalar e rodar localmente

É necessário ter instalado o PHP (versão 8.0 ou superior, necessariamente) e o MySQL.

1) Baixe todo o conteúdo e coloque em uma pasta.
3) Rode todas as querys do arquivo banco.sql e atualize o arquivo Config.php com as credenciais de acesso corretas.
    -> Note que o comando para criar o banco já está no script SQL, com o mesmo nome que está configurado por padrão no Config.php 
2) Acesse a pasta /public via CLI e rode o seguinte comando 
~~~
PHP -S localhost:8000
~~~
    -> Caso esteja usando Apache e prefira, também é possível rodar o projeto criando um virtualhost e apontando para a pasta raiz
4) E
5) Teste o projeto 😎

⚠️ Atenção ⚠️ 
<br>Qualquer problema com o autoload pode ser resolvido com o seguinte comando:
~~~
composer dump
~~~

## Lista dos endpoints e requisições

