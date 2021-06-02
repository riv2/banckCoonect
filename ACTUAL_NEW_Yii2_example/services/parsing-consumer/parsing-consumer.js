const amqp                    = require('amqplib/callback_api');
const DB                      = require('./lib/db');
const argv                    = require('minimist')(process.argv.slice(2));

class ParsingConsumer {

    constructor() {
        this.processingParsings    = {};
        this.doneQueue             = process.env.RABBIT_QUEUE_DONE || 'done';
        this.captchaQueue          = process.env.RABBIT_QUEUE_CAPTCHA || 'captcha';

        this.rabbitUrl             = 'amqp://'+process.env.RABBIT_LOGIN +
            ':' + process.env.RABBIT_PASSWORD +
            '@' + process.env.RABBIT_HOST +
            ':' + process.env.RABBIT_PORT;

        this.DB                    = new DB({
            user:       process.env.POSTGRES_USER,
            database:   process.env.POSTGRES_DB,
            password:   process.env.POSTGRES_PASSWORD,
            host:       process.env.POSTGRES_VHOST,
            port:       process.env.POSTGRES_PORT
        });

        this.MATCHING_NEW           = process.env.PARSING_STATUS_MATCHING_NEW;
        this.COLLECTING_NEW         = process.env.PARSING_STATUS_COLLECTING_NEW;

        this.threadIndex            = 0;
    }

    async start() {
        await this.DB.connect();
        await this.connectRabbit(this.rabbitUrl);
    }

    async loadParsing(id) {
        if (!this.processingParsings[id]) {
            let parsing = await this.DB.getParsing(id);

            if (!parsing) {
                parsing = DB.dummyParsing(id);
                parsing.fake = true;
            }

            this.processingParsings[id] = parsing;
        }
        return this.processingParsings[id];
    }

    async finishParsing(parsingId) {

        let p =  await this.loadParsing(parsingId);

        if (argv.l) {
            console.log(p.parsing_status_id);
            console.log(p.parsing_status_id < 6);
        }
        if (p.parsing_status_id < 6) {
            await this.updateParsing(parsingId, {
                parsing_status_id:  6,
                parsed_count:       p.parsed_count,
                errors_count:       p.errors_count,
                in_stock_count:     p.in_stock_count,
                finished_at: DB.phpDateFormat()
            });
        } else {
            await this.updateParsing(parsingId, {
                parsing_status_id: 6,
            });
        }

    }
    async cancelParsing(parsingId, reason) {
        console.log('Cancel ' + parsingId + ' reason:' + reason);

        let reasonStatus = 7;
        if (reason === 'noProxies') {
            reasonStatus = 8;
        }

        await this.updateParsing(parsingId, {
            parsing_status_id: reasonStatus
        });

    }

    async updateParsing(parsingId, data) {
        let parsing = await this.loadParsing(parsingId);

        if (parsing) {

            for (let i in data) {
                if (data.hasOwnProperty(i)) {
                    this.processingParsings[parsingId][i] = data[i];
                }
            }

            await this.DB.updateParsing(parsingId, data);

            if (this.processingParsings[parsingId] && this.processingParsings[parsingId].parsing_status_id >= 6) {
                delete this.processingParsings[parsingId];
            }
        }

    }

    async storeParsedPrice(parsingId, items, url) {
       // console.log('ParsedPrice ' + parsingId + ' ' + url);
       // console.log(items);
        let parsing = await this.loadParsing(parsingId);

        if (parsing.is_test) {
            return;
        }
        for (let i = 0; i < items.length; i++) {
            parsing.parsed_count ++;

            if (parsing.parsing_type === 'collecting'){
                items[i].price_parsed_status_id = this.COLLECTING_NEW;
            }
            if (parsing.parsing_type === 'matching'){
                items[i].price_parsed_status_id = this.MATCHING_NEW;
            }
            items[i].url = url;
            if (!items[i].out_of_stock) {
                parsing.in_stock_count++;
            }
            if (this.threadIndex >= parseInt(process.env.PARSING_CONSUMER_THREADS,10)) {
                this.threadIndex = 0;
            }
            items[i].thread = this.threadIndex;
            this.threadIndex++;
            await this.DB.insertPrice(parsing, items[i]);
        }
        //await this.updateParsingCounts(parsing, items);
    }

    async storeParsingError(parsingId, error, url) {
        let parsing = await this.loadParsing(parsingId);
        parsing.errors_count++;
        if (parsing.is_test) {
            return;
        }
        await this.DB.insertParsingError(parsing, error, url);
    }

    async storeCaptcha(parsingId, data) {
        let parsing = await this.loadParsing(parsingId);
        console.log('Captcha ' + parsingId);
        await this.DB.insertCaptcha(parsing, data);
    }

    async addParsingErrorCount(parsingId) {
        if (!parsingId) return;
        let parsing = await this.loadParsing(parsingId);

        if (parsing && parsing.parsing_status_id < 6) {
            parsing.errors_count++;
            if (parsing.parsing_status_id < 4) {
                parsing.parsing_status_id = 4;
                parsing.started_at = DB.phpDateFormat();
                await this.DB.updateParsing(parsing.id, parsing);
            }
            else if (parsing.errors_count % 3 === 3) {
                await this.DB.updateParsing(parsing.id, parsing);
            }
        }
    }

    // Присоедениться к RabbitMQ и читать очередь
    connectRabbit(rabbitUrl) {
        amqp.connect(rabbitUrl, (err, connection) => {
            if (err !== null) {
                process.exit(1);
                return;
            }
            // Чтение очереди парсинга
            connection.createChannel((err, channel) => {
                if (err) {
                    process.exit(1);
                    return;
                }

                channel.prefetch(1);
                channel.assertQueue(this.doneQueue, {durable: true});
                for (let i = 0; i < 2; i++) {
                    channel.consume(this.doneQueue, async (msg) => {
                        if (msg && msg !== null) {
                            let parsingId = null;

                            try {
                                let data = JSON.parse(msg.content.toString());
                                parsingId = data.parsingId;
                                if (data.action && data.action === 'cancel') {
                                    await this.cancelParsing(parsingId, data.reason);
                                    try {
                                        channel.deleteQueue('p_' + parsingId, {ifEmpty: false});
                                    } catch (e) {

                                    }
                                }
                                if (data.action && data.action === 'parsed') {
                                    await this.storeParsedPrice(parsingId, data.items, data.url);
                                }
                                if (data.action && data.action === 'error') {
                                    await this.storeParsingError(parsingId, data.error, data.url);
                                }
                                if (data.action && data.action === 'finish') {
                                    if (argv.l) {
                                        console.log(data);
                                    }
                                    await this.finishParsing(parsingId);
                                    try {
                                        channel.deleteQueue('p_' + parsingId, {ifEmpty: false});
                                    } catch (e) {

                                    }
                                }
                                channel.ack(msg);
                            } catch (e) {
                                channel.ack(msg);
                                if (e) {
                                    e.rabbitMessage = msg.content.toString();
                                    console.error(e);
                                }
                                // await this.storeError(parsingId, e);
                            }

                        }
                    });
                }
                channel.on('error', () => {
                    process.exit(1);
                });
            });

            // Чтение очереди ответов антикапчи
            connection.createChannel((err, channel) => {
                channel.assertQueue(this.captchaQueue, {durable: true});
                channel.prefetch(1);
                channel.consume(this.captchaQueue, async (msg) => {
                    let parsingId = null;
                    if (msg !== null) {
                        try {
                            let data  = JSON.parse(msg.content.toString());
                            parsingId = data.parsingId;
                            await this.storeCaptcha(parsingId, data.captcha);
                        } catch (e) {
                            e.rabbitMessage = msg.content.toString();
                            console.error(e);
                            await storeError(parsingId, e);
                        }
                        channel.ack(msg);
                    }
                });
            });
        });
    }

    async destroy() {
        if (this.DB) {
            await this.DB.end();
        }
    }

}

module.exports = ParsingConsumer;