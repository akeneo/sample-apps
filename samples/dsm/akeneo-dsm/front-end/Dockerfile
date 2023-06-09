FROM node:20-alpine AS core

###############################################################################

FROM core AS development

WORKDIR /srv/app/front

COPY ./ ./

RUN npm i

CMD ["npm", "start"]
