//var Script = process.binding('evals').Script;
var vm = require('vm');

var stats = {};

var document = module.exports = function(stats) {
    console.log('creating document');
    stats = stats;
};

document.prototype.response = {};
document.prototype.stats = {};
document.prototype.cache = [];
document.prototype.load = function(location) {
    var sandbox = {
    		console:console,
    		redis: this.redis,
    		response: this.response,
    		result:{},
    		onSuccess:function(value){
    			response.write(value);
    			response.end();
    		},
    		onFail:function(){
    			response.write(value);
    			response.end();
    		}
    };
    
    if(this.cache[location] != undefined) {
        stats.hits+=1;
        this.cache[location].runInNewContext(sandbox);
    } else {
        stats.misses+=1;
        this.redis.get(
            location,
            (function( err, value ){
                if(err != null) {
                    consle.log(err);
                    response.write(500);
                    response.end();
                }
                if(value == null) {
                    response.write("404\n");
                    response.end();
                } else {
                    try {
                        var scriptString = value.toString('utf8', 0, value.length - 1 );
                        this.cache[location] = vm.createScript(scriptString);
                        this.cache[location].runInNewContext(sandbox);
                    } catch (e) {
                    	consle.log(e.message);
                        // TODO: handle exception
                        this.response.write("500\n");
                        this.response.end();
                    }
                    setTimeout((function(){delete this.cache[location];}).bind(this),3600);
                }
            }).bind(this)
        );
    }

    
    
    
    
    
};
document.prototype.save = function(location, content) {
    return true;
};