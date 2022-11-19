#! /bin/bash

# Executa comandos dentro do container
input=$*

clear
docker compose exec app $input