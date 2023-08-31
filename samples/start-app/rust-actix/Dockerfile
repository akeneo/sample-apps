FROM rust:1.72-bookworm

WORKDIR /srv/app

RUN apt-get update && \
    apt-get install -y lld clang && \
    rm -rf /var/lib/apt/lists/* && \
    cargo install cargo-watch