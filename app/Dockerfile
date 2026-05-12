FROM oven/bun:latest

WORKDIR /app

COPY ./index.ts /app/index.ts
COPY ./package.json /app/package.json

RUN bun install

ENTRYPOINT ["bun", "run", "index.ts"]
