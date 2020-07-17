# Conceito
> 
   - MVCS
        - View
        - Controller
        - ~~Service~~ (Repository)  _// vamos abstrair a camada de serviço somente com o Repository Pattern_
        - Model
>

## Instalação

Instale as dependências do framework:

`composer install`

Copie o arquivo de exemplo de configuração `.env.example` para `.env` e edite o que for necessário:  

`cp .env.example .env `

Gere uma nova chave para aplicação:

`php artisan key:generate`

Faça a migração e popule o seu banco de dados:

`php artisan migrate --seed`

Gere o token do JWT:

`php artisan jwt:secret`

