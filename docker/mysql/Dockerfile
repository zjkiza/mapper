FROM mysql/mysql-server:latest

COPY ./configuration/custom.cnf /etc/mysql/conf.d/custom.cnf
RUN chown -R mysql:mysql /var/lib/mysql

ARG SYSTEM_TIMEZONE

RUN ln -snf /usr/share/zoneinfo/${SYSTEM_TIMEZONE} /etc/localtime && echo ${SYSTEM_TIMEZONE} > /etc/timezone
ENV TZ="${SYSTEM_TIMEZONE}"

WORKDIR /