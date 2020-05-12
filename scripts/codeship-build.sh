#!/bin/bash

#Creates a .env.local file
if [ "$CI_BRANCH" = "master" ]
then
  environment='prod'
else
  environment='dev'
fi

printf "%s\n" "APP_ENV=$environment" "DATABASE_URL=$DATABASE_URL" > .env.local


#Changes the name of the appspec file depending on the branch
if [ "$CI_BRANCH" = "test" ]
then
  mv appspec.yml.test appspec.yml
fi
if [ "$CI_BRANCH" = "master" ]
then
  mv appspec.yml.prod appspec.yml
fi