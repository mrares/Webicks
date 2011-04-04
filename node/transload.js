var formidable = require('formidable'),
    http = require('http'),

    sys = require('sys');

var httpServer = http.createServer(function(req, res) {
  if (req.url == '/upload' && req.method.toLowerCase() == 'post') {
	console.log("new upload received");
    // parse a file upload
    var form = new formidable.IncomingForm();
    //form.parse(req, function(err, fields, files) {
      res.writeHead(200, {'content-type': 'text/plain'});
	form.onPart = function(part) {
		if(!part.filename){
		form.handlePart(part);
		return true;
		}
		
		console.log(sys.inspect(part,true));
	}

	form.on('fileBegin', function(name,file){
		console.log('File begin!');
		console.log(name);
		console.log(sys.inspect(file));
	});

	form.on('end', function(){res.end('bye!')});	

	form.parse(req);
      //res.write('received upload:\n\n');
      //res.end(sys.inspect({fields: fields, files: files}));
    //});

    return;
  }

  // show a file upload form
  res.writeHead(200, {'content-type': 'text/html'});
  res.end(
    '<form action="/upload" enctype="multipart/form-data" method="post">'+
    '<input type="text" name="title"><br>'+
    '<input type="file" name="upload" multiple="multiple"><br>'+
    '<input type="submit" value="Upload">'+
    '</form>'
  );
});

httpServer.listen(80);
