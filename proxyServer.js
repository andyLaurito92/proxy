// File /srv/server.js
var http = require('http');
var httpProxy = require('http-proxy');
var url = require('url');

function server(ip, port)
{
        var http = require('http');

        //Create a server
        var server = http.createServer(function (request,response){

            response.writeHeader(200, {"Content-Type": "text/html"});
            response.write('It worked !' + request.url);
            response.end();

        }).listen(port,ip);

        console.log('Server running at http://'+ip+':'+port+'/');


        /*http.createServer(function (request, response) {

            if(request.method =='GET') {

                //We are going to assume that every request income is going to be to api.mercadolibre
                var urlObj = url.parse(request.url);

                console.log('urlObject: '+urlObj);
                console.log('reqHost: '+request.headers['host']);
                console.log('reqURL: '+request.headers['url']);
                console.log('urlObj.domain: '+urlObj.domain);

                request.headers['url'] = urlObj.href;

                console.log('reqHost: '+request.headers['host']);
                console.log('reqURL: '+request.headers['url']);

            }

        }).listen(port, ip);
        console.log('Server running at http://'+ip+':'+port+'/');*/

    /*httpProxy.createServer(function(req, res, proxy) {

        var urlObj = url.parse(req.url);

        req.headers['host'] = urlObj.host;
        req.headers['url'] = urlObj.href;

        proxy.proxyRequest(req, res, {
            host: 'google.com.ar',
            port: 80,
            enable : { xforward: true }
        });
    }).listen(ip,port);
    console.log('Server running at http://'+ip+':'+port+'/');
    */
}

// Create three servers for
// the load balancer, listening on any
// network on the following three ports
server('127.0.0.1', 9000);
server('127.0.0.1', 9001);
server('127.0.0.1', 9002);
