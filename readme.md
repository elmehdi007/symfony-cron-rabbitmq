

# composer:
composer i

# npm:
npm i

npm run dev

# migration
bin/console make:migration

bin/console doctrine:migrations:migrate

# start app
symfony server:start


# route to insert two users, one with user_role and other with admin_role also this route will insert into article table for demo
/auth/register-user-demo

# login page, 
UsernameAdmin@Passwrord.com is the admin and UsernameUser@user.com is the moderator, they have the same password Passwrord@123
/login

# list article:
/admin/articles

when you open this page and you are you not logged you will redirected to login page(access_control & firewalls configured in config/packages/security.yaml )

only admin can see and run delete action(configured in config/packages/security.yaml in access_control section)

listing support pagination with the abilite to choose items per page to show; by defaut 10 items per page



# run script cron parse_task:
symfony console app:news:parse_task

this cron open connexion to RABBITMQ and push task


# run script cron parse_worker:
symfony console app:news:parse_worker

this cron open connexion to RABBITMQ and listening for new task


# docker-compose
docker-compose up -d

the RABBITMQ params are configured in config/services.yaml and they are automatically loaded from docker by Symfony, so use should docker-compose up -d to up docker
