var vm = require('vm');

var script = vm.createScript('console.log("here!");');
script.runInNewContext({console:console});