ARG IMAGE_NAME=postgres

FROM ${IMAGE_NAME}:13.5

RUN CONFIG_FILE=/usr/share/postgresql/postgresql.conf.sample \
    && sed -ie 's/^#shared_buffers = \w*/shared_buffers = 512MB/' ${CONFIG_FILE} \
    && sed -ie 's/^#work_mem = \w*/work_mem = 256MB/' ${CONFIG_FILE} \
    && sed -ie 's/^#maintenance_work_mem = \w*/maintenance_work_mem = 512MB/' ${CONFIG_FILE}

EXPOSE 5432
