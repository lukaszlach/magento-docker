#!/usr/bin/env bash
/usr/bin/indexer --all || true
exec /webproc --config /etc/sphinxsearch/sphinx.conf -- /usr/bin/searchd --nodetach