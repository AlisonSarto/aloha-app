ARG BASE_IMAGE
FROM ${BASE_IMAGE}

COPY docker/rollback-entrypoint.sh /usr/local/bin/aloha-entrypoint
RUN chmod +x /usr/local/bin/aloha-entrypoint
