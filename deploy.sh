#!/bin/sh

set -eu

COMPOSE_FILE="docker-compose.prod.yml"
SERVICE="api"
CONTAINER_NAME="myfinance_prod_api"
ENV_FILE=".env"

HEALTH_URL="${HEALTH_URL:-http://127.0.0.1/up}"
HEALTH_ATTEMPTS="${HEALTH_ATTEMPTS:-10}"
HEALTH_INTERVAL="${HEALTH_INTERVAL:-3}"

NEW_TAG="${1:-}"

set_image_tag() {
    tag="$1"

    if grep -q '^API_IMAGE_TAG=' "$ENV_FILE"; then
        sed -i "s|^API_IMAGE_TAG=.*|API_IMAGE_TAG=$tag|" "$ENV_FILE"
    else
        printf '\nAPI_IMAGE_TAG=%s\n' "$tag" >> "$ENV_FILE"
    fi
}

check_health() {
    attempt=1

    while [ "$attempt" -le "$HEALTH_ATTEMPTS" ]; do
        echo "Healthcheck $attempt/$HEALTH_ATTEMPTS..."

        if curl \
            --fail \
            --silent \
            --show-error \
            --max-time 5 \
            "$HEALTH_URL" >/dev/null; then
            echo "Aplicação respondeu com sucesso."
            return 0
        fi

        attempt=$((attempt + 1))
        sleep "$HEALTH_INTERVAL"
    done

    return 1
}

rollback() {
    echo ""
    echo "Iniciando rollback para a tag $CURRENT_TAG..."

    set_image_tag "$CURRENT_TAG"

    API_IMAGE_TAG="$CURRENT_TAG" \
        docker compose -f "$COMPOSE_FILE" pull "$SERVICE" || true

    API_IMAGE_TAG="$CURRENT_TAG" \
        docker compose -f "$COMPOSE_FILE" up \
        -d \
        --no-deps \
        --force-recreate \
        "$SERVICE"

    echo "Versão anterior recriada."

    if check_health; then
        echo "Rollback concluído com sucesso."
    else
        echo "Atenção: a aplicação não respondeu após o rollback."
        echo ""

        docker compose -f "$COMPOSE_FILE" logs --tail=150 "$SERVICE"
    fi
}

if [ -z "$NEW_TAG" ]; then
    echo "Erro: informe a tag da imagem."
    echo ""
    echo "Uso:"
    echo "  ./deploy.sh <tag>"
    echo ""
    echo "Exemplo:"
    echo "  ./deploy.sh 09f5192f50ae84419b789e10a49159ef6ce8359d"
    exit 1
fi

if [ ! -f "$COMPOSE_FILE" ]; then
    echo "Erro: arquivo $COMPOSE_FILE não encontrado."
    exit 1
fi

if [ ! -f "$ENV_FILE" ]; then
    echo "Erro: arquivo $ENV_FILE não encontrado."
    exit 1
fi

if ! command -v curl >/dev/null 2>&1; then
    echo "Erro: curl não está instalado na VPS."
    exit 1
fi

CURRENT_TAG="$(
    sed -n 's/^API_IMAGE_TAG=//p' "$ENV_FILE" |
    tail -n 1
)"

if [ -z "$CURRENT_TAG" ]; then
    CURRENT_TAG="latest"
fi

echo "Iniciando deploy da API"
echo "Tag atual: $CURRENT_TAG"
echo "Nova tag:  $NEW_TAG"
echo "Health URL: $HEALTH_URL"
echo ""

if [ "$CURRENT_TAG" = "$NEW_TAG" ]; then
    echo "A versão $NEW_TAG já está configurada."
    exit 0
fi

echo "Baixando imagem ghcr.io/matheusprb/myfinance-api:$NEW_TAG..."

API_IMAGE_TAG="$NEW_TAG" \
    docker compose -f "$COMPOSE_FILE" pull "$SERVICE"

echo ""
echo "Executando migrations..."

API_IMAGE_TAG="$NEW_TAG" \
    docker compose -f "$COMPOSE_FILE" run --rm \
    -e RUN_ARTISAN_OPTIMIZE=0 \
    -e RUN_MIGRATIONS_ON_START=0 \
    "$SERVICE" \
    php artisan migrate --force

echo ""
echo "Atualizando API_IMAGE_TAG no $ENV_FILE..."

set_image_tag "$NEW_TAG"

echo "Recriando container da API..."

API_IMAGE_TAG="$NEW_TAG" \
    docker compose -f "$COMPOSE_FILE" up \
    -d \
    --no-deps \
    "$SERVICE"

echo ""
echo "Verificando saúde da aplicação..."

if ! check_health; then
    echo ""
    echo "Erro: a nova versão não passou no healthcheck."
    echo ""

    docker compose -f "$COMPOSE_FILE" logs --tail=150 "$SERVICE"

    rollback

    exit 1
fi

DEPLOYED_IMAGE="$(
    docker inspect \
        --format '{{.Config.Image}}' \
        "$CONTAINER_NAME"
)"

echo ""
echo "Deploy concluído."
echo "Imagem em execução: $DEPLOYED_IMAGE"
