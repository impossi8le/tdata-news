# Этап сборки
FROM node:18 AS build

# 🧰 Устанавливаем необходимые пакеты для правильной работы нативных модулей
RUN apt-get update && apt-get install -y openssl python3

WORKDIR /app

COPY package*.json ./
RUN npm install
RUN npm install pinia
RUN npm install axios vue-router

COPY . .
RUN npm run build

# Этап запуска
FROM nginx:alpine AS runtime
COPY --from=build /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
