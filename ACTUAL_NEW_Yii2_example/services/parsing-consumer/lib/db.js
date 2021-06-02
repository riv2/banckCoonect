const pg                      = require('pg').native;
const DEFAULT_GUID1            =  'aaa00001-36e5-4b35-bd74-cb971a8d9335';
const DEFAULT_GUID2            =  'aaa00002-36e5-4b35-bd74-cb971a8d9335';
const md5                     = require('md5');

class DB {

    constructor(options) {
        this.DB     = new pg.Client(options);
    }

    static dummyParsing(id) {
        return {
            id: id || DEFAULT_GUID2,
            parsed_count:0,
            errors_count:0,
            total_count:0,
            requests_count:0,
            connected_count:0,
            success_count: 0,
            unreached_count:0,
            in_stock_count:0,
            with_retries_count:0,
            parsing_status_id:0,
            is_test: false,
            started_at:null,
            finished_at:null,
            parsing_project_id:DEFAULT_GUID1,
            parsing_type: 'normal',
            regions:null,
            region_id:null,
            source_id:null,
            robot_id:0,
        };
    }

    static dummyPrice(parsing) {
        parsing = parsing || {};

        return {
            'item_id'                : null,
            'competitor_shop_name'   : null,
            'competitor_shop_domain' : null,
            'competitor_item_name'   : null,
            'competitor_item_sku'    : null,
            'competitor_item_url'    : null,
            'competitor_item_count'  : null,
            'competitor_item_rubric1'  : null,
            'competitor_item_rubric2'  : null,
            'competitor_item_brand'  : null,
            'competitor_item_seller' : 'default',
            'original_url'           : null,
            'out_of_stock'           : false,
            'delivery'               : null,
            'delivery_days'          : null,
            'price'                  : 0,
            'competitor_id'          : null,
            'thread'                 : 0,
            'url'                    : null,
            'error_message'          : null,
            'price_parsed_status_id' : 0,
            'http404'                 : false,
            'parsing_project_id'     : parsing.parsing_project_id || DEFAULT_GUID1,
            'parsing_id'             : parsing.id || DEFAULT_GUID2 ,
            'source_id'              : parsing.source_id || 1,
            'regions'                : parsing.regions ?  parsing.regions.split(',').map(function(item) {
                return parseInt(item, 10);
            }) : null,
        };
    }

    async connect() {
        return new Promise((resolve, reject) => {
            this.DB.connect(function (err) {
                if (err) {
                    reject(err);
                    throw err;
                }
                resolve(this);
            });
        });
    }

    async query(str, params) {
        return new Promise((resolve, reject) => {
            this.DB.query(str, params,  (err, result) => {
                if (err) {
                    reject(err);
                } else {
                    if (result.rows) {
                        resolve(result.rows);
                    } else {
                        resolve(null);
                    }
                }
            });
        });
    }

    async queryScalar(str, params) {
        return new Promise((resolve, reject) => {
            this.DB.query(str, params,  (err, result) => {
                if (err) {
                    reject(err);
                } else {
                    if (Array.isArray(result.rows) && result.rows.length > 0) {
                        for (let i in result.rows[0]) {
                            if (result.rows[0].hasOwnProperty(i)) {
                                resolve(result.rows[0][i]);
                            }
                        }
                    } else {
                        resolve(null);
                    }
                }
            });
        });
    }

    async getParsing(id) {
        let table = 'prc_parsing';
        let fields = DB.objectToSelect(DB.dummyParsing());
        let rows = await this.query('SELECT '+fields+' FROM '+table+' WHERE id = $1::uuid', [id]);
        if (Array.isArray(rows) && rows.length > 0) {
            return rows[0];
        } else {
            return null;
        }
    }

    async insertParsingError(parsing, error, url) {
        let date = DB.phpDateFormat();
        let insert = {
            'message'               : error.message ? error.message : JSON.stringify(error),
            'url'                   : url ? url : (error.info && error.info.url ? error.info.url : null),
            'hash1'                 : md5(error.message + parsing.id),
            'hash2'                 : md5(error.message + parsing.id),
            'parsing_project_id'    : parsing.parsing_project_id || DEFAULT_GUID1,
            'parsing_id'            : parsing.id || DEFAULT_GUID2,
            'regions'               : parsing.regions || null,
            'masks_id'              : null,
            'item_id'               : null,
            'competitor_id'         : null,
            'proxy'                 : error.info && error.info.proxy ? error.info.proxy : null,
            'item'                  : null,
            'info'                  : JSON.stringify(error.info),
            'type'                  : null,
            'created_at'            : date
        };
        await this.insert('prc_parsing_error', insert);
    }

    async insertPrice (parsing, item) {
        let price = DB.dummyPrice(parsing);
        for (let i in item) {
            if (item.hasOwnProperty(i) && price.hasOwnProperty(i)) {
                price[i] = DB.filterPriceValue(i, item[i]);
            }
        }
        if (price.thread >= parseInt(process.env.PARSING_CONSUMER_THREADS,10)) {
            price.thread = 0;
        }
        let date = DB.phpDateFormat();

        if (price.regions && !Array.isArray(price.regions)) {
            price.regions = price.regions.split(',').map(function(item) {
                return parseInt(item, 10);
            });
        }
        price.delivery_days = isNaN(parseInt(price.delivery_days)) ? null : price.delivery_days;

        price.created_at    = date;
        price.extracted_at  = date;

        await this.insert('prc_price_parsed', price);

        return price;
    }
    async insertCaptcha(parsing, data) {
        let date = DB.phpDateFormat();
        let antiCaptchaRow = {
            'anti_captcha_task_id'      : data.taskId,
            'answer'                    : data.answer,
            'img_body'                  : data.imgData,
            'url'                       : data.url,
            'parsing_id'                : parsing.id || DEFAULT_GUID2,
            'parsing_project_id'        : parsing.parsing_project_id || DEFAULT_GUID1,
            'cost'                      : parseFloat(data.cost),
            'error'                     : data.error,
            'created_at'                : date,
            'updated_at'                : date
        };

        await this.insert('prc_anti_captcha_task', antiCaptchaRow);
    }
    async updateParsing(parsingId, data) {
        if (data.fake) {
            return;
        }
        return new Promise((resolve, reject) => {
            let table = 'prc_parsing';

            let set = DB.objToSet(data);

            this.DB.query('UPDATE public.' + table + ' SET ' + set.set + " WHERE id = '"+parsingId+"'::uuid ", set.values,  (err, result) => {
                if (err) {
                    console.error(err);
                    reject();
                } else {

                    resolve();
                }
            });
        });
    }

    async end() {
        return new Promise((resolve, reject) => {
            this.DB.end(resolve);
        });
    }

    async insert(table, object) {
        return new Promise((resolve, reject) => {
            let keys            = Object.keys(object);
            let values          = [];
            let placeholders    = [];

            let p = 0;
            for (let i = 0; i < keys.length; i++) {
                let val = object[keys[i]];
                if (val && Array.isArray(val)) {
                    placeholders.push("'"+JSON.stringify(val)+"'::jsonb");
                } else {
                    p++;
                    placeholders.push('$' + p);
                    values.push(val);
                }
            }

            this.DB.query('INSERT INTO public.' + table + ' (' + keys.join(',') + ') VALUES (' + placeholders.join(',') + ');', values,  (err, result) => {
                if (err) {
                    console.error(err);
                    reject();
                } else {

                    resolve();
                }
            });
        });
    }

    static filterPriceValue (k, value) {
        if (!value) {
            return value;
        }
        if (k === 'parsing_project_id') {
            if (value.length > 36) {
                return value.slice(-36);
            }
            return value;
        }

        if ((k === 'competitor_item_url' || k === 'url') )
        {
            value = value.toLowerCase();
            if (value.length > 1024) {
                return value.slice(-1024);
            }
            return value;
        }

        if (k === 'price') {
            if (value.length > 50) {
                return value.slice(-50);
            }
            return value;
        }
        if (k === 'regions') {
            return value.split(',');
        }

        if (k === 'competitor_shop_name' ||
            k === 'competitor_shop_domain' ||
            k === 'competitor_item_name' ||
            k === 'competitor_item_sku' ||
            k === 'competitor_rubric1' ||
            k === 'competitor_rubric2' ||
            k === 'competitor_brand') {
            if (value.length > 255) {
                return value.slice(-255);
            }
            return value;
        }
        return value;
    }

    static objToSet(obj) {
        let string = [];
        let keys = Object.keys(obj);
        let values = [];
        let p = 0;
        for (let i = 0; i < keys.length; i++) {
            let val = obj[keys[i]];
            if (Array.isArray(val)) {
                string.push(keys[i] + " = '"+JSON.stringify(val)+"'::jsonb");
            } else {
                p++;
                string.push(keys[i] + ' = $' + p);
                values.push(val);
            }
        }
        return {
            set: string.join(','),
            values: values,
            count: values.length
        };
    }

    static objectToSelect (obj) {
        return DB.arrayToSelect(Object.keys(obj));
    }

    static arrayToSelect (arr) {
        return arr.join(',')
    }

    /**
     * Получить дату в формате РНР
     * @param date
     * @returns {string}
     */
    static phpDateFormat(date) {
        let d;
        if (date !== undefined) {
            d = new Date(date);
        } else {
            d = new Date();
        }
        return [
                d.getFullYear(),
                ("00" + (d.getMonth() + 1)).slice(-2),
                ("00" + d.getDate()).slice(-2)
            ].join('-') +
            ' ' +
            [
                ("00" + d.getHours()).slice(-2),
                ("00" + d.getMinutes()).slice(-2),
                ("00" + d.getSeconds()).slice(-2)
            ].join(':');
    }
}

module.exports = DB;