FROM node:18 AS core

RUN apt-get update \
    && apt-get install -y \
            git \
            unzip \
            curl \
            sqlite3 \
    && apt-get clean

###############################################################################

FROM core AS dev-tools

##Update npm
RUN npm install -g npm
##Install nodemon
RUN npm install -g nodemon

###############################################################################

FROM dev-tools as development

ARG USER=www-data
WORKDIR /srv/app
USER $USER

CMD nodemon
