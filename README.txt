
# 🚀 **Aplicação de Gerenciamento de Cursos e Alunos**

Esta aplicação foi desenvolvida para gerenciar **cursos**, **alunos**, **matrículas** e **turmas**. Além disso, implementa práticas de segurança para proteger os dados dos usuários e garantir a integridade do sistema.

---

## 💻 **Tecnologias Utilizadas**
- PHP >= 7.4
- MySQL
- HTML / CSS

---

## 🔒 **Proteções de Segurança Implementadas**

- **SQL Injection**:  
  Utilização de *prepared statements* com parâmetros vinculados para evitar injeções de SQL.

- **XSS (Cross-Site Scripting)**:  
  Todos os dados de entrada e saída são sanitizados usando `htmlspecialchars()`.

- **CSRF (Cross-Site Request Forgery)**:  
  Implementação de tokens CSRF para garantir que as requisições POST sejam legítimas.

- **Proteção de Sessões**:  
  Uso de `session_regenerate_id()` e configuração de cookies seguros.

- **Cabeçalhos de Segurança**:  
  Cabeçalhos HTTP como `Strict-Transport-Security`, `X-Content-Type-Options` e `X-Frame-Options` configurados para melhorar a segurança.

---

### 🔐 **Cabeçalhos de Segurança Específicos**:
- **Strict-Transport-Security**: Força o uso de HTTPS.
- **X-Content-Type-Options**: Previne o navegador de interpretar o conteúdo como algo diferente do tipo MIME declarado.
- **X-Frame-Options**: Previne ataques de clickjacking, garantindo que o conteúdo não possa ser incorporado em frames de outros sites.

---
### Ambiente de Desenvolvimento (Opcional)
Você pode usar o [XAMPP](https://www.apachefriends.org/index.html) para configurar rapidamente o servidor Apache e o MySQL em seu computador. Ele já vem com o PHP e o MySQL configurados, o que facilita o processo de desenvolvimento local.

Após instalar o XAMPP, basta seguir os passos abaixo para rodar a aplicação:
1. Inicie o Apache e o MySQL no painel de controle do XAMPP.
2. Coloque os arquivos da aplicação na pasta `htdocs` do XAMPP.
3. Acesse a aplicação no navegador em `http://localhost/seu-repositorio/`.

**Se preferir, você pode configurar manualmente o Apache, o MySQL e o PHP.**
---

## 🚀 **Como Rodar a Aplicação Localmente**

1. **Clone o repositório**:
   ```bash
   git clone https://github.com/Cammy92/FIAP_Challenge.git
   ```

2. **Crie o banco de dados MySQL** com `utf8_general_ci`:
   - Abra o MySQL e crie o banco de dados:
     ```sql
     CREATE DATABASE fiap CHARACTER SET utf8 COLLATE utf8_general_ci;
     ```

3. **Importe o dump.sql**:
   - Execute o script SQL no banco de dados para criar as tabelas e registros iniciais:
     ```bash
     mysql -u root -p fiap < dump.sql
     ```

4. **Configure o Apache (caso não esteja usando o XAMPP):**
   - Certifique-se de ter o Apache e o PHP configurados no seu sistema.
   - Configure o Apache para usar HTTPS (se necessário).

5. **Abra o navegador** e acesse `http://localhost/seu-repositorio/`.

---

## 🤝 **Contribuições**

Sinta-se à vontade para enviar **pull requests**! Qualquer contribuição será bem-vinda!  
🚀 **Faça parte dessa jornada!**

---

### 🌟 **Licença**  
Este projeto é licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para mais detalhes.