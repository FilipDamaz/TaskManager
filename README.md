# TaskManager

Minimal Symfony 7.4 skeleton running in Docker.

**Start**
1. `Makefile.cmd up` (Windows) or `make up` (Linux/macOS)
2. Open `http://localhost:8000/health` — you should see a JSON status

**Database**
1. PostgreSQL runs on `localhost:5432`
2. DB name: `taskmanager`
3. User: `app`
4. Password: `secret`

**Stop**
1. `Makefile.cmd down` (Windows) or `make down`

**Logs**
1. `Makefile.cmd logs` (Windows) or `make logs`

**Create project (if repo is empty)**
1. `Makefile.cmd init` (Windows) or `make init`
