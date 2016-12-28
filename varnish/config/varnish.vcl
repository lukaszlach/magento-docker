backend docker_web {
    .host = "nginx";
    .port = "80";
}


sub vcl_recv {
    set req.backend_hint = docker_web;
    set req.http.Host = "magento.local";
}