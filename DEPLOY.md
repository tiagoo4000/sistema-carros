# 🚀 Guia de Instalação e Deploy (VPS/Hospedagem)

Este guia cobre os passos necessários para instalar o sistema Leilo Master em um servidor VPS (Ubuntu 22.04/24.04 recomendado) ou ambiente similar.

---

## 📋 Pré-requisitos Gerais
*   Servidor VPS com acesso root (SSH).
*   Domínio apontado para o IP do servidor.
*   Mínimo recomendado: 2GB RAM, 2 vCPU.

---

## 🐳 Opção 1: Instalação via Docker (Recomendado)
Esta é a maneira mais fácil e robusta de rodar a aplicação, pois todo o ambiente (PHP, Banco de Dados, Redis, WebSockets) já está configurado.

### 1. Instalar Docker e Docker Compose
```bash
# Atualizar repositórios
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Adicionar usuário atual ao grupo docker (evita usar sudo)
sudo usermod -aG docker $USER
newgrp docker
```

### 2. Clonar o Projeto
```bash
git clone <URL_DO_SEU_REPOSITORIO> sistema-leilao
cd sistema-leilao
```

### 3. Configurar Ambiente
```bash
cp .env.example .env
nano .env
```
**Ajustes Críticos no `.env` para Produção:**
*   `APP_ENV=production`
*   `APP_DEBUG=false`
*   `APP_URL=https://seu-dominio.com`
*   `DB_PASSWORD=SuaSenhaSeguraMySQL`
*   `DB_USERNAME=leilao`
*   `DB_DATABASE=leilao`
*   **Importante:** Mantenha `DB_HOST=mysql` e `REDIS_HOST=redis` se usar Docker.

### 4. Subir os Containers
```bash
docker compose up -d --build
```

### 5. Configuração Final da Aplicação
Execute estes comandos dentro do container da aplicação:

```bash
# Gerar chave da aplicação
docker compose exec app php artisan key:generate

# Rodar migrações (cria as tabelas no banco)
docker compose exec app php artisan migrate --force

# (Opcional) Popular banco com dados iniciais
docker compose exec app php artisan db:seed

# Criar link simbólico para imagens
docker compose exec app php artisan storage:link

# Otimizar cache
docker compose exec app php artisan optimize
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
```

### 6. Configurar SSL (HTTPS) com Nginx Proxy Manager (Opcional mas Recomendado)
Recomenda-se rodar um Nginx reverso na máquina host ou usar o [Nginx Proxy Manager](https://nginxproxymanager.com/) para gerenciar SSL automaticamente.
Se for usar Nginx direto no host, configure o proxy pass para a porta `8001` (definida no docker-compose).

---

## 🔧 Opção 2: Instalação Manual (LEMP Stack)
Use esta opção se preferir não usar Docker ou estiver em um ambiente compartilhado que suporte Node.js/Redis.

### 1. Instalar Dependências do Sistema
```bash
sudo apt update
sudo apt install -y nginx mysql-server redis-server git curl unzip supervisor
sudo apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-curl php8.3-xml php8.3-mbstring php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd php8.3-redis
```

### 2. Instalar Node.js 20 (LTS)
```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 3. Instalar Soketi (WebSockets)
```bash
sudo npm install -g @soketi/soketi
```

### 4. Configurar Banco de Dados
```bash
sudo mysql -u root -p
```
```sql
CREATE DATABASE leilao;
CREATE USER 'leilao'@'localhost' IDENTIFIED BY 'SuaSenhaSegura';
GRANT ALL PRIVILEGES ON leilao.* TO 'leilao'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Instalar Aplicação
```bash
cd /var/www
git clone <URL_DO_REPOSITORIO> leilao
cd leilao

# Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# Instalar dependências Frontend e Compilar
npm install
npm run build

# Permissões
chown -R www-data:www-data /var/www/leilao
chmod -R 775 storage bootstrap/cache
```

### 6. Configurar .env
```bash
cp .env.example .env
nano .env
```
Ajuste as conexões de banco para `127.0.0.1` e credenciais criadas.

### 7. Comandos Finais
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

### 8. Configurar Supervisor (Filas e WebSockets)
Crie `/etc/supervisor/conf.d/leilao-worker.conf`:
```ini
[program:leilao-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/leilao/artisan horizon
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/leilao/storage/logs/worker.log
```

Crie `/etc/supervisor/conf.d/soketi.conf`:
```ini
[program:soketi]
command=soketi start
environment=PORT=6001
autostart=true
autorestart=true
user=root
```

Reinicie o supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### 9. Configurar Nginx (Host)
Crie `/etc/nginx/sites-available/leilao`:

```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /var/www/leilao/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Ative o site e instale SSL:
```bash
sudo ln -s /etc/nginx/sites-available/leilao /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d seu-dominio.com
```

---

## 🚨 Troubleshooting Comum

### Permissões de Pasta
Se houver erro 500 ou "Permission denied":
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### WebSockets não conectam
Certifique-se que a porta `6001` (Soketi) está aberta no firewall da VPS:
```bash
sudo ufw allow 6001/tcp
```
E que o `.env` no frontend está apontando para o domínio correto (WSS/HTTPS).

### Build do Frontend (Vite)
Se os estilos não carregarem, verifique se rodou `npm run build` e se o arquivo `public/hot` foi removido.
