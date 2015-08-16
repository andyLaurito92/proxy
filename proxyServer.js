// File /srv/server.js
var http = require('http');
var https = require('https');
var url  = require('url');
var sys  = require('sys');

// revisar el siguiente link --> http://www.catonmat.net/http-proxy-in-nodejs/

// Error: connect ECONNREFUSED ???

function createProxyServerListeningIn(anIp, aPort)
{

    http.createServer(function(request, response) {

        var anUrlParsed = url.parse(request.url);

        sys.log(request.connection.remoteAddress + ": " + request.method + " " + anUrlParsed.host +' '+ anUrlParsed.path );

        var proxy_request = http.request({
            host:anUrlParsed.host,
            path: anUrlParsed.path,
            port: 80,
            accept: '*/*',
            method: request.method,

        }, function (proxy_response){
            proxy_response.addListener('data', function(chunk) {
                response.write(chunk, 'binary');
            });
            proxy_response.addListener('end', function() {
                response.end();
            });
            response.writeHead(proxy_response.statusCode, proxy_response.headers);
        })
        .on('error',function(e){
            console.log( e.stack );
        })
        .on('uncaughtException', function (err) {
            console.log(err);
        }).end();


    }).listen(aPort,anIp);

}

// Create three servers for the load balancer, listening on any
// network on the following three ports
createProxyServerListeningIn('127.0.0.1', 9000);
createProxyServerListeningIn('127.0.0.1', 9001);
createProxyServerListeningIn('127.0.0.1', 9002);
