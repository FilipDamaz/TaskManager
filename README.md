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
5. Run migrations: `Makefile.cmd migrate` or `make migrate`

**Import users from JSONPlaceholder**
1. Start containers: `Makefile.cmd up` or `make up`
2. Run migrations: `Makefile.cmd migrate` or `make migrate`
3. Import users: `docker compose exec app php bin/console app:users:import`

Imported users are stored with a local UUID (`id`) and the external JSONPlaceholder id (`external_id`).

**Tests**
1. Unit/fast tests: `Makefile.cmd test` or `make test`
2. Integration tests (separate DB): `Makefile.cmd long-test` or `make long-test`

**Login (JWT)**
1. Generate JWT keys (once):
   - `.\Makefile.cmd jwt-keys` or `make jwt-keys`
2. Import users: `Makefile.cmd import-users` or `make import-users`
3. Seed admin: `Makefile.cmd seed-admin` or `make seed-admin`
3. Login: `POST /login` with JSON body `{"email":"Sincere@april.biz","password":"<one-time-password>"}`
4. Current user: `GET /me` with header `Authorization: Bearer <token>`

One-time passwords are generated on import and should be emailed to users (not implemented yet).

**Tasks API (REST)**
1. List tasks for current user: `GET /tasks`
2. List all tasks (admin only): `GET /admin/tasks`
3. Task history (event store): `GET /tasks/{id}/history`

**Tasks API (GraphQL)**
Endpoint: `POST /graphql`
Example query:
```
{ tasks { id title status assigneeId } }
```
Admin-only query:
```
{ adminTasks { id title status assigneeId } }
```

**Stop**
1. `Makefile.cmd down` (Windows) or `make down`

**Logs**
1. `Makefile.cmd logs` (Windows) or `make logs`

**Create project (if repo is empty)**
1. `Makefile.cmd init` (Windows) or `make init`
