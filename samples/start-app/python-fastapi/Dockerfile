FROM python:3.9


WORKDIR /srv/app

# set env variables
ENV PYTHONDONTWRITEBYTECODE 1
ENV PYTHONUNBUFFERED 1
ENV TZ=Europe/Paris 
 
COPY ./requirements.txt /app/requirements.txt

RUN pip install --upgrade pip
RUN pip install --no-cache-dir --upgrade -r /app/requirements.txt
 
COPY ./app .
