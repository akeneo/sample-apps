FROM rust:1.72-bookworm

WORKDIR /srv/app

RUN apt-get update && apt-get install -y \
    apt-get install lld clang \
    && rm -rf /var/lib/apt/lists/* \
    && cargo install cargo-watch