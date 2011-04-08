var	http = require('http'),
	url = require('url'),
	redisClient = require('./libs/redis-client.js'),
	redis = redisClient.createClient(redisClient.DEFAULT_PORT, redisClient.DEFAULT_HOST, { maxReconnectionAttempts: 0 }),
	formidable = require('formidable'),
	Script = process.binding('evals').Script,
	cachedScripts = [],
	stats = {hits:0,misses:0};
var util = require('util');
var document = require('./libs/document.js');

setInterval(function(){
    console.log(stats);
    console.log(util.inspect(process.memoryUsage()));
    stats={hits:0,misses:0};}
,1000);

myDoc = new document(stats);
myDoc.redis = redis;


http.createServer(function(request, response) {
    response.writeHead(200, {"Content-Type":"text/plain"});
    var urlObj = url.parse(request.url, true);
	if(request.method == 'GET') {
	    myDoc.response = response;
	    myDoc.load(urlObj.pathname);
	} else if (request.method == 'POST') {
		var form = new formidable.IncomingForm();
		form.parse(request, function(err, fields, files) {
			if(fields.publish!=undefined && fields.content != undefined) {
				redis.set( request.url, fields.content, function(){});
			}
		});
		response.end();
	}
}
).listen(8080);
