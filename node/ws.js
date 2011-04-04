var	http = require('http'), 
	url = require('url'), 
	redisClient = require('./redis-client.js'),
	redis = redisClient.createClient(redisClient.DEFAULT_PORT, redisClient.DEFAULT_HOST, { maxReconnectionAttempts: 0 }),
	formidable = require('formidable'),
	Script = process.binding('evals').Script;
	    sandbox = {
		console:console,
		redis: redis,
	    };

http.createServer(function(request, response) {
console.log(" ===== ===== ===== ===== NEW REQUEST ===== ===== ===== ===== ");
    //request.on('data',function(data){console.log(data.toString());});
//    console.log(request);
    response.writeHead(200, {"Content-Type":"text/plain"});

    var urlObj = url.parse(request.url, true);
    console.log(urlObj);

	if(request.method == 'GET') {
		console.log("Getting " + urlObj.pathname);
		redis.get(
			urlObj.pathname,
			function( err, value ){
			if(err != null) {
				consle.log(err);
				response.write(500);
				response.end();
			}
			console.log(value.toString());
				if(value == null) {
					response.write("404\n");
					response.end();
				} else {
					//response.write(value);
					//Script.runInNewContext(value, sandbox, 'user-code.js');
					var sandbox = { 
						console:console, 
						redis: redis,
						response: response, 
						result:{}, 
						onSuccess:function(value){
							console.log('OK');
							console.log(value);
							response.write(value);
							response.end();
						}, 
						onFail:function(){
							console.log('FAIL!');
							response.write(value);
                                                        response.end();
						}
					};
					script = new Script(value);
					script.runInNewContext(sandbox);
	//				console.log(sandbox.result.toString());
					setTimeout(function(){console.log(sandbox.result); response.end()},1000);
					//response.end();
				}
			}
		);
	} else if (request.method == 'POST') {
	        console.log("POST REQUEST DETECTED");





//		request.on(
//			'data',
//			function(data){
//				console.log(data.toString());
//			}
//		);







var form = new formidable.IncomingForm();
    form.parse(request, function(err, fields, files) {
      console.log({fields: fields, files: files});
	if(fields.publish!=undefined && fields.content != undefined) {
	console.log("Saving content to repository...");
	console.log("Saving to " + request.url);
	console.log("Content: ");
	console.log(fields.content);
		redis.set(request.url, fields.content, function(result){console.log(result);});
	}
    });






		response.end();
	}
}
).listen(8080);
