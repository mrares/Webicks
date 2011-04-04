var	http = require('http'),
	url = require('url'),
	redisClient = require('./libs/redis-client.js'),
	redis = redisClient.createClient(redisClient.DEFAULT_PORT, redisClient.DEFAULT_HOST, { maxReconnectionAttempts: 0 }),
	formidable = require('formidable'),
	Script = process.binding('evals').Script,
	cachedScripts = [],
	stats = {hits:0,misses:0};
var document = require('./libs/document.js');

setInterval(function(){
    console.log(stats);
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
//		if(cachedScripts[urlObj.pathname] != undefined) {
//			stats.hits+=1;
//			var sandbox = {
//				console:console,
//                redis: redis,
//                response: response,
//                result:{},
//                onSuccess:function(value){
//                    response.write(value);
//                    response.end();
//                },
//                onFail:function(){
//                    response.write(value);
//                    response.end();
//                }
//		    };
//            //script = new Script(value);
//            cachedScripts[urlObj.pathname].runInNewContext(sandbox);
//		} else {
//    		stats.misses+=1;
//    		redis.get(
//    			urlObj.pathname,
//    			function( err, value ){
//        			if(err != null) {
//        				consle.log(err);
//        				response.write(500);
//        				response.end();
//        			}
//    				if(value == null) {
//    					response.write("404\n");
//    					response.end();
//    				} else {
//    					var sandbox = { 
//    						console:console, 
//    						redis: redis,
//    						response: response, 
//    						result:{}, 
//    						onSuccess:function(value){
//    							response.write(value);
//    							response.end();
//    						}, 
//    						onFail:function(){
//    							response.write(value);
//                                response.end();
//    						}
//    					};
//
//    					console.log(value.toString());
//    					try{
//        					cachedScripts[urlObj.pathname] = new Script(value);
//        					cachedScripts[urlObj.pathname].runInNewContext(sandbox);
//    					} catch (e) {
//                            // TODO: handle exception
//    					    response.write("500\n");
//    					    response.end();
//                        }
//    					setTimeout(function(){delete cachedScripts[urlObj.pathname];},1000);
//    				}
//    			}
//    		);
//		}
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
