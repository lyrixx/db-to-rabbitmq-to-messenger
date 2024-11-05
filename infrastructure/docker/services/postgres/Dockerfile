FROM postgres:16

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        build-essential \
        ca-certificates \
        postgresql-server-dev-16 \
        python3 \
        python3-dev \
        python3-pip \
        python3-setuptools \
        unzip \
        wget \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# https://github.com/pgsql-io/multicorn2
RUN wget https://github.com/pgsql-io/multicorn2/archive/refs/tags/v3.0.tar.gz -O /tmp/multicorn2.tar.gz \
    && tar -xzf /tmp/multicorn2.tar.gz -C /tmp \
    && cd /tmp/multicorn2-3.0 \
    && make \
    && make install \
    && rm -rf /tmp/multicorn2.tar.gz /tmp/multicorn2-3.0

# https://github.com/OleksandrBesan/rabbitmq_fdw?tab=readme-ov-file
RUN wget https://github.com/AlexandrBesan/rabbitmq_fdw/archive/refs/heads/main.zip -O /tmp/rabbitmq_fdw.zip \
    && unzip /tmp/rabbitmq_fdw.zip -d /tmp \
    && cd /tmp/rabbitmq_fdw-main \
    && python3 setup.py install \
    && rm -rf /tmp/rabbitmq_fdw.zip /tmp/rabbitmq_fdw-main

