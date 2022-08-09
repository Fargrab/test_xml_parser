ARG IMAGE_NAME=nginx

FROM ${IMAGE_NAME}:1.21
COPY default.conf /etc/nginx/conf.d/default.conf
COPY nginx.conf /etc/nginx/nginx.conf
COPY localhost.crt /etc/nginx/cert/localhost.crt
COPY localhost.key /etc/nginx/cert/localhost.key

WORKDIR /var/www

EXPOSE 80
