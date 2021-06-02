const ParsingConsumer      = require('./parsing-consumer');

const parsingConsumer = new ParsingConsumer({});

parsingConsumer.start();

// Обеспечить мягкий выход
async function exitHandler (options, err) {
    if (options.cleanup) {
        await parsingConsumer.destroy();
    }
    if (err) {
        console.error(err.stack);
    }
    if (options.exit) {
        process.exit(1);
    }
}

process.stdin.resume();
process.on('exit', exitHandler.bind(null,{cleanup:true}));
process.on('SIGINT', exitHandler.bind(null, {exit:true}));
process.on('uncaughtException', exitHandler.bind(null, {exit:true}));

