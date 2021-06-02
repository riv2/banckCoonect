if (phantom.args.length !== 2) {
    console.log('Usage: p.js URL filename');
    phantom.exit();
} else {
    var address = phantom.args[0];
    var page = require('webpage').create();
    page.open(address, function() {
        page.render(phantom.args[1]);
        phantom.exit();
    });
}
