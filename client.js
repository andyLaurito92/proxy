var Http = require('http');

var req = Http.request({
    host: '127.0.0.1',
    // proxy IP
    port: 9003,
    // proxy port
    method: 'GET',
    path: 'http://www.google.com.ar'
}, function (res) {
    res.on('data', function (data) {
        console.log(data.toString());
    });
});

req.end();