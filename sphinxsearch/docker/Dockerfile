FROM debian:jessie

RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get install -q -y \
    curl \
    libexpat1 libmysqlclient18 libodbc1 libpq5
RUN curl -o /tmp/sphinx.deb http://sphinxsearch.com/files/sphinxsearch_2.2.11-release-1~jessie_amd64.deb \
  && dpkg -i /tmp/sphinx.deb \
  && rm -f /tmp/sphinx.deb
RUN mkdir -p /usr/local/sphinxsearch/data

# webproc
RUN curl https://i.jpillora.com/webproc | bash

RUN apt-get remove -y curl \
  && apt-get autoremove -y \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists

COPY run.sh /run.sh
RUN chmod +x /run.sh

VOLUME ["/etc/sphinxsearch"]

EXPOSE 9312 9306

CMD /run.sh