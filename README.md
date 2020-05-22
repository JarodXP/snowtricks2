#Welcome to the Snowtricks Project

This project is a demo of a community platform to share snowboard tricks.

These are the main constraints for this project:
 - Use the Symfony Framework
 - Use only official Symfony extensions
 - Respect the given specifications (you can find it in the folder /specs)
 - Respect all the Symfony Best Practices
 - Respect the PHP standards PSR-1, PSR-12 and PSR-4
 
##Get the project
 You can directly clone it on GitHub:
 https://github.com/JarodXP/snowtricks2
 
##Requirements for the project
To be able to use all the features you must at least:
- have a web server with PHP 7.4 or above
- have Composer installed (https://getcomposer.org/download/)
- have Yarn installed (https://classic.yarnpkg.com/en/docs/install/)
- have a MySQL database
- have an SMTP server

##Setup your environment  
1. Rename your .env file into .env.local and fill in the required environment variables  
2. Rename either /config/packages/swiftmailer.yaml.local.template (for local development environment) or   
/config/packages/swiftmailer.yaml.dist (for production environment) into swiftmailer.yaml
3. Complete the swiftmailer config with your environment variables

##Project initialization
###With scripts
Once cloned, you can launch a script to initialize all automatically: 
scripts\init-project.sh (for local development environment)

This script includes the following ones that you can also run step by step
- scripts\init-dependencies.sh
- scripts\init-database.sh
- scripts\init-medias.sh

###Manually
If the scripts don't work or if you prefer setting up the project manually, follow these instructions step by step:
1. Install dependencies  
`composer install --no-interaction`  
`yarn install --no-interaction`  
2. Build assets  
`yarn encore dev`  
3. Reset the potential database you could have  
`php bin/console doctrine:query:sql "DROP DATABASE IF EXISTS snowtricks"`  
`php bin/console doctrine:database:create`
4. Reset the Doctrine migrations files  
`rm -r src/Migrations/*`  
`php bin/console make:migration --no-interaction`  
5. Prepare the database with Doctrine  
`php bin/console doctrine:migrations:migrate --no-interaction`
6. Load the Fixtures to fill in your database with some samples  
`php bin/console doctrine:fixtures:load --no-interaction`
7. Reset the media directory  
`rm -R -d public/media/avatars`  
`rm -R -d public/media/tricks_media`
8. Move the media samples to the media directory  
`cp -p -r public/media/sampleFiles/avatars public/media/avatars`
`cp -p -r public/media/sampleFiles/tricks_media public/media/tricks_media`





