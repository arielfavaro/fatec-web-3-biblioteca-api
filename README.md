# Aula 07/11 – Desenvolvimento Web 3

Projeto da disciplina de **Desenvolvimento Web 3** (Fatec), feito em Laravel. Ele cadastra **autores** e **livros** com relacionamento 1:N (um autor tem vários livros) e usa migrations, models Eloquent, factories e seeders.

## Como executar

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
```

O comando `migrate:fresh --seed` recria as tabelas `autors` e `livros` e as popula com 5 autores, cada um com 1 a 3 livros gerados pelo Faker.

## Estrutura do projeto

| Arquivo | Descrição |
| --- | --- |
| `app/Models/Autor.php` | Model do autor (`hasMany` livros) |
| `app/Models/Livro.php` | Model do livro (`belongsTo` autor) |
| `database/migrations/..._create_autors_table.php` | Tabela `autors` (nome, email único, nascimento) |
| `database/migrations/..._create_livros_table.php` | Tabela `livros` (titulo, isbn único, ano_publicacao, autor_id) |
| `database/factories/AutorFactory.php` | Gera autores fictícios |
| `database/factories/LivroFactory.php` | Gera livros fictícios |
| `database/seeders/AutorSeeder.php` | Cria 5 autores com 1 a 3 livros cada |

---

## Perguntas e respostas

### 1. Arquitetura do Laravel

**Qual padrão arquitetural o Laravel utiliza como base para organizar o código da aplicação e qual é o papel de cada uma de suas camadas?**

O Laravel segue o padrão **MVC (Model-View-Controller)**:

- **Model:** representa os dados e as regras de negócio. Cada model se comunica com uma tabela do banco por meio do Eloquent ORM (ex.: `Autor`, `Livro`).
- **View:** é a camada de apresentação, ou seja, o que o usuário vê. No Laravel, as views costumam ser escritas com o Blade.
- **Controller:** recebe a requisição (vinda das rotas), chama os models para buscar ou salvar dados e devolve uma resposta, que pode ser uma view, um JSON ou um redirecionamento.

### 2. Estrutura de Diretórios

**Em uma instalação padrão do Laravel, onde ficam localizados os Models, os Controllers e os arquivos de Migrations?**

- **Models:** `app/Models`
- **Controllers:** `app/Http/Controllers`
- **Migrations:** `database/migrations`

### 3. Conceito de Migrations

**O que são as Migrations no Laravel e qual problema elas resolvem no desenvolvimento em equipe?**

Migrations são arquivos PHP que descrevem a estrutura do banco de dados: criar tabelas, adicionar colunas, índices, chaves estrangeiras etc. Funcionam como um **controle de versão do banco**.

Em equipe, elas evitam que cada desenvolvedor tenha que alterar o banco manualmente ou trocar scripts SQL. Como as migrations ficam no repositório junto com o código, basta rodar um comando para que todos tenham exatamente a mesma estrutura. Além disso, cada alteração pode ser desfeita.

### 4. Execução de Migrations

**Qual comando do Artisan é utilizado para aplicar as migrations pendentes no banco de dados e qual comando desfaz a última operação de migração?**

- Aplicar as migrations pendentes:

  ```bash
  php artisan migrate
  ```

- Desfazer o último lote (batch) de migrations executado:

  ```bash
  php artisan migrate:rollback
  ```

### 5. Eloquent ORM e Models

**O que é o Eloquent ORM e como ele relaciona o nome de uma classe Model (por exemplo, Member ou Book) com a tabela correspondente no banco de dados?**

O **Eloquent** é o ORM (*Object-Relational Mapping*) do Laravel. Ele permite trabalhar com o banco de dados por meio de objetos PHP, sem precisar escrever SQL: cada model representa uma tabela e cada instância do model representa um registro.

Por convenção, o nome da tabela é o nome da classe em **snake_case e no plural, em inglês**:

- `Member` → tabela `members`
- `Book` → tabela `books`

Quando a tabela não segue essa convenção, basta informar o nome na propriedade `$table`. Neste projeto, por exemplo, o model `Livro` declara `protected $table = 'livros';`.

### 6. Propriedade $fillable

**Para que serve a propriedade $fillable dentro de um Model e contra qual vulnerabilidade de segurança ela protege a aplicação?**

O `$fillable` lista os campos que podem ser preenchidos em massa, como em `Livro::create($request->all())`. Os campos que não estão na lista são ignorados.

Ele protege contra a vulnerabilidade de **Mass Assignment** (atribuição em massa). Nesse ataque, o usuário envia campos extras na requisição (por exemplo, `is_admin=1`) para alterar colunas que não deveria poder modificar.

```php
protected $fillable = ['titulo', 'isbn', 'ano_publicacao', 'autor_id'];
```

### 7. Propriedade $casts

**Qual é a finalidade da propriedade $casts em um Model do Laravel? Cite um exemplo prático de uso.**

O `$casts` converte automaticamente os atributos para um tipo específico quando eles são lidos ou gravados no banco, como `date`, `boolean`, `integer`, `array` ou `decimal:2`.

Exemplo do model `Autor` deste projeto:

```php
protected $casts = [
    'nascimento' => 'date',
];
```

Com isso, `$autor->nascimento` passa a ser um objeto `Carbon` em vez de uma string, e é possível fazer `$autor->nascimento->format('d/m/Y')` ou `$autor->nascimento->age`. Outro exemplo comum é `'ativo' => 'boolean'`, que transforma o `0`/`1` do banco em `false`/`true`.

### 8. Conceito de Seeders

**O que são os Seeders no Laravel e em quais situações eles são mais úteis durante o desenvolvimento de um sistema?**

Seeders são classes que **populam o banco de dados** com dados iniciais ou de teste. Eles ficam em `database/seeders` e são executados com `php artisan db:seed` (ou junto com `php artisan migrate:fresh --seed`).

São úteis para:

- ter dados de teste prontos durante o desenvolvimento, sem cadastrar tudo manualmente;
- criar dados obrigatórios do sistema, como um usuário administrador, categorias ou status padrão;
- garantir que toda a equipe trabalhe com o mesmo conjunto de dados;
- preparar o banco para testes automatizados.

Neste projeto, o `AutorSeeder` cria 5 autores, cada um com 1 a 3 livros.

### 9. Conceito de Factories

**Qual é a função de uma Model Factory e qual biblioteca ela utiliza por padrão para gerar dados fictícios?**

Uma **Model Factory** define um "molde" para criar instâncias de um model com dados fictícios. Ela permite gerar muitos registros de uma vez, como em `Autor::factory(5)->create()`, o que é bastante usado em seeders e testes.

Por padrão, ela usa a biblioteca **Faker** (`fakerphp/faker`), acessada pelo helper `fake()`:

```php
'titulo' => fake()->sentence(3),
'isbn'   => fake()->unique()->isbn13(),
```
