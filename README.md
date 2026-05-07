# Sistema de Gestão de Hotel

Aplicação local em PHP com SQLite e Bootstrap para gerenciar quartos, hóspedes e reservas.

## Como usar

1. Coloque este projeto em um servidor local compatível com PHP, por exemplo XAMPP, MAMP ou PHP embutido.
2. Execute o script `init_db.php` para criar o banco de dados SQLite:
   - `http://localhost/hotel/init_db.php`
3. Acesse `index.php` para usar o sistema.

## Funcionalidades

- Cadastro e edição de quartos
- Cadastro e edição de hóspedes
- Criação de reservas
- Check-out e cancelamento de reservas
- Banco de dados local em `data/hotel.db`

## 🌐 Demonstração Online
Podes testar o sistema em tempo real aqui: http://hotels.wuaze.com/

## Estrutura

- `index.php` - Dashboard
- `quartos.php` - Gerencia quartos
- `hospedes.php` - Gerencia hóspedes
- `reservas.php` - Gerencia reservas
- `db.php` - Conexão SQLite e criação de tabelas
- `init_db.php` - Inicializa a base de dados
- `assets/css` - Estilos locais
- `assets/js` - JavaScript local
