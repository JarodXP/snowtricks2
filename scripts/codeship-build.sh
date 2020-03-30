#!/bin/bash

#Changes the name of the appspec file depending on the branch
if [ "$CI_BRANCH" = "test" ]
then
  mv appspec.yml.test appspec.yml
fi
if [ "$CI_BRANCH" = "master" ]
then
  mv appspec.yml.prod appspec.yml
fi