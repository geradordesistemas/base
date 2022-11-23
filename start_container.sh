#! /bin/bash

# Para execução do container
docker compose down

# Inicia execução do container e faz o build das imagens
docker compose up -d --build

./start_project.sh
