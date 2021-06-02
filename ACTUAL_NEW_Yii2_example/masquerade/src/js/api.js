import jQuery from "jquery";

export default class Api {

    constructor() {
        this.url    = 'http://'+window.location.host+'/api/parsing'
        this.droids = [
            '10.250.17.120',
            '10.250.17.121',
            '10.250.17.122',
            '10.250.17.123',
            '10.250.17.124',
            '10.250.17.125',
            '10.241.70.56',
            '10.241.70.57',
            '10.241.70.58',
            '10.241.70.59',
        ];
        this.droidIndex = 0;
    }

    async masks({id = null, q = null, page = 1, perPage = 10}) {
        return new Promise((resolve => {
            jQuery.ajax({
                url: this.url + '/masks',
                data: {
                    id: id,
                    q: q,
                    page: page,
                    perPage: perPage,
                },
                type: 'get',
                dataType: 'json',
                success: (json) => {
                    resolve(json);
                }
            })
        }));
    }

    async masksDelete(id) {
        return new Promise((resolve => {
            jQuery.ajax({
                url: this.url + '/masks?id=' + id,
                type: 'delete',
                dataType: 'json',
                success: (json) => {
                    if (json.errors) {
                        resolve(false);
                    } else {
                        resolve(json);
                    }
                },
                error: () => {
                    resolve(false);
                }
            })
        }));
    }

    async masksAdd(id, projectId, record) {
        projectId = projectId || null;
        return new Promise((resolve => {
            jQuery.ajax({
                url: this.url + '/masks?id=' + id,
                data: {
                    Masks: record,
                    bindToProject: projectId,
                },
                type: 'post',
                dataType: 'json',
                success: (json) => {
                    if (json.errors) {
                        resolve(false);
                    } else {
                        resolve(json);
                    }
                },
                error: () => {
                    resolve(false);
                }
            })
        }));
    }

    async projects({id = null, q = null, masksId = null, page = 1, perPage = 10}) {
        return new Promise((resolve => {
            jQuery.ajax({
                url: this.url + '/projects',
                data: {
                    id,
                    q,
                    masksId,
                    page,
                    perPage
                },
                type: 'get',
                dataType: 'json',
                success: (json) => {
                    resolve(json);
                }
            })
        }));
    }


    async test({settings, queue, noProxy = false, html = false, debug = false}) {
        let randomDroid = this.droids[this.droidIndex % (this.droids.length+1)];
        this.droidIndex++;
        return new Promise((resolve => {
            jQuery.ajax({
                url: 'http://' + randomDroid + ':4000/test',
                data: JSON.stringify({
                    queue,
                    settings,
                    noProxy,
                    html,
                    debug
                }),
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                processData: false,
                success: (json) => {
                    resolve(json);
                }
            })
        }));
    }


    dummyRecord() {
        return {
            "id": null,
            "name": null,
            "domain": null,
            "masks": this.dummyMasks(),
            "test_urls": "",
            "test_cookies": ""
        }
    }

    dummyMasks() {
        return {
            "name": null,
            "domain": null,
            "bounds": [],
            "http404": [],
            "fields": {},
            "replaces": [],
            "stock": [],
            "clicks": [],
            "clicksSoft": [],
            "hrefs": [],
            "dateParse": {
                "dateFormat": [],
                "replace": [],
                "match": []
            },
            "wait": {
                "beforeStart":null,
                "untilState": null,
                "forElement": null,
                "scroll": null
            },
            "removeUtm": [],
            "strip": {
                "tags": false,
                "returns": false,
                "js": false,
                "spaces": false
            },
            "captcha": {
                enabled: false,
                imgSelector: null,
                inputSelector: null,
                buttonSelector: null
            },
            "checkUrls": false,
            "scripts": []
        }
    }

    dummyParsing() {
        return {
            "id": "111",
            "maxThreads": 1,
            "rateLimit": 500,
            "retryTimeout": 2000,
            "timeout": 30000,
            "retries": 1,
            "antiCaptchaKey": "be102d02fbf790c44064350fe974a4e3",
            "proxies": ["r3p3081219:LkBof5ynkb@91.243.188.74:7951", "r3p3081219:LkBof5ynkb@185.106.95.28:7951", "r3p3081219:LkBof5ynkb@185.172.131.59:7951", "r3p3081219:LkBof5ynkb@185.117.117.169:7951", "r3p3081219:LkBof5ynkb@185.112.102.86:7951", "r3p3081219:LkBof5ynkb@185.126.84.225:7951", "r3p3081219:LkBof5ynkb@91.243.188.86:7951", "r3p3081219:LkBof5ynkb@185.106.95.27:7951", "r3p3081219:LkBof5ynkb@185.172.130.76:7951", "r3p3081219:LkBof5ynkb@176.119.140.188:7951", "r3p3081219:LkBof5ynkb@185.194.106.209 :7951", "r3p3081219:LkBof5ynkb@91.243.188.94:7951", "r3p3081219:LkBof5ynkb@81.22.44.31:7951", "r3p3081219:LkBof5ynkb@91.227.155.10:7951", "r3p3081219:LkBof5ynkb@185.147.128.147:7951", "r3p3081219:LkBof5ynkb@185.147.128.163 :7951", "r3p3081219:LkBof5ynkb@91.227.155.219:7951", "r3p3081219:LkBof5ynkb@185.112.102.84:7951", "r3p3081219:LkBof5ynkb@185.147.130.39 :7951", "r3p3081219:LkBof5ynkb@176.119.140.119:7951", "r3p3081219:LkBof5ynkb@91.243.188.110:7951", "r3p3081219:LkBof5ynkb@185.106.92.134:7951", "r3p3081219:LkBof5ynkb@176.119.140.200:7951", "r3p3081219:LkBof5ynkb@176.119.140.251:7951", "r3p3081219:LkBof5ynkb@185.112.101.140:7951", "r3p3081219:LkBof5ynkb@185.128.213.17:7951", "r3p3081219:LkBof5ynkb@176.119.140.101:7951", "r3p3081219:LkBof5ynkb@185.112.102.85:7951", "r3p3081219:LkBof5ynkb@91.243.188.225:7951", "r3p3081219:LkBof5ynkb@185.80.148.90 :7951", "r3p3081219:LkBof5ynkb@176.119.140.45:7951", "r3p3081219:LkBof5ynkb@81.22.44.57:7951", "r3p3081219:LkBof5ynkb@185.172.131.63:7951", "r3p3081219:LkBof5ynkb@185.103.253.133:7951", "r3p3081219:LkBof5ynkb@185.148.24.45 :7951", "r3p3081219:LkBof5ynkb@91.243.188.182:7951", "r3p3081219:LkBof5ynkb@185.106.94.52:7951", "r3p3081219:LkBof5ynkb@185.80.151.124 :7951", "r3p3081219:LkBof5ynkb@91.227.155.251:7951", "r3p3081219:LkBof5ynkb@91.227.155.252:7951", "r3p3081219:LkBof5ynkb@84.252.75.29:7951", "r3p3081219:LkBof5ynkb@185.66.15.23:7951", "r3p3081219:LkBof5ynkb@81.177.13.254 :7951", "r3p3081219:LkBof5ynkb@176.119.140.118:7951", "r3p3081219:LkBof5ynkb@185.103.252.24:7951", "r3p3081219:LkBof5ynkb@91.227.155.67:7951", "r3p3081219:LkBof5ynkb@91.243.188.92:7951", "r3p3081219:LkBof5ynkb@185.106.94.54:7951", "r3p3081219:LkBof5ynkb@91.227.155.216:7951", "r3p3081219:LkBof5ynkb@91.227.155.28:7951", "r3p3081219:LkBof5ynkb@91.243.188.24:7951", "r3p3081219:LkBof5ynkb@91.243.188.32:7951", "r3p3081219:LkBof5ynkb@176.119.140.197:7951", "r3p3081219:LkBof5ynkb@185.172.130.77:7951", "r3p3081219:LkBof5ynkb@185.172.131.62:7951", "r3p3081219:LkBof5ynkb@185.104.251.58:7951", "r3p3081219:LkBof5ynkb@185.80.149.29:7951", "r3p3081219:LkBof5ynkb@176.119.140.26:7951", "r3p3081219:LkBof5ynkb@185.128.215.116:7951", "r3p3081219:LkBof5ynkb@185.172.130.75:7951", "r3p3081219:LkBof5ynkb@81.177.3.186 :7951", "r3p3081219:LkBof5ynkb@81.22.44.206:7951", "r3p3081219:LkBof5ynkb@185.106.95.24:7951", "r3p3081219:LkBof5ynkb@81.22.44.28:7951", "r3p3081219:LkBof5ynkb@91.243.188.177:7951", "r3p3081219:LkBof5ynkb@185.102.137.114:7951", "r3p3081219:LkBof5ynkb@185.192.110.163 :7951", "r3p3081219:LkBof5ynkb@91.227.155.201:7951", "r3p3081219:LkBof5ynkb@185.128.214.12:7951", "r3p3081219:LkBof5ynkb@185.80.150.145 :7951", "r3p3081219:LkBof5ynkb@81.22.44.241:7951", "r3p3081219:LkBof5ynkb@176.119.140.104:7951", "r3p3081219:LkBof5ynkb@91.243.188.253:7951", "r3p3081219:LkBof5ynkb@81.22.44.224:7951", "r3p3081219:LkBof5ynkb@91.243.188.218:7951", "r3p3081219:LkBof5ynkb@84.252.75.26:7951", "r3p3081219:LkBof5ynkb@185.192.109.60:7951", "r3p3081219:LkBof5ynkb@81.177.23.187 :7951", "r3p3081219:LkBof5ynkb@81.22.44.190:7951", "r3p3081219:LkBof5ynkb@185.128.212.13:7951", "r3p3081219:LkBof5ynkb@185.147.129.1 :7951", "r3p3081219:LkBof5ynkb@91.227.155.164:7951", "r3p3081219:LkBof5ynkb@91.227.155.40:7951", "r3p3081219:LkBof5ynkb@185.106.92.137:7951", "r3p3081219:LkBof5ynkb@185.106.94.55:7951", "r3p3081219:LkBof5ynkb@91.227.155.6:7951", "r3p3081219:LkBof5ynkb@84.252.75.31:7951", "r3p3081219:LkBof5ynkb@91.227.155.105:7951", "r3p3081219:LkBof5ynkb@91.243.188.155:7951", "r3p3081219:LkBof5ynkb@185.103.253.138:7951", "r3p3081219:LkBof5ynkb@81.22.44.244:7951", "r3p3081219:LkBof5ynkb@176.119.140.9:7951", "r3p3081219:LkBof5ynkb@185.172.130.78:7951", "r3p3081219:LkBof5ynkb@185.103.252.27:7951", "r3p3081219:LkBof5ynkb@185.192.110.2 :7951", "r3p3081219:LkBof5ynkb@81.22.44.243:7951", "r3p3081219:LkBof5ynkb@91.243.188.58:7951", "r3p3081219:LkBof5ynkb@91.227.155.125:7951", "r3p3081219:LkBof5ynkb@91.243.188.181:7951", "r3p3081219:LkBof5ynkb@185.103.252.26:7951", "r3p3081219:LkBof5ynkb@185.106.94.56:7951", "r3p3081219:LkBof5ynkb@176.119.140.168:7951", "r3p3081219:LkBof5ynkb@185.106.92.136:7951", "r3p3081219:LkBof5ynkb@81.22.44.91:7951", "r3p3081219:LkBof5ynkb@185.112.101.141:7951"],
            "userAgents": ["Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.47 Safari/537.36"],
            "vpn": null,
            "blockedDomains": ["mc.yandex.ru", "g.doubleclick.net", "www.googleadservices.com", "hit.acstat.com", "code.acstat.com", "statad.ru", "rockcnt.com", "api.flocktory.com", "tracking.retailrocket.net", "rockcnt.com", "dsp.retailrocket.net", "tracking.retailrocket.net", "cdn.retailrocket.net", "www.googletagmanager.com", "yastatic.net", "www.youtube.com", "staticxx.facebook.com", "browser-updater.yandex.net", "connect.facebook.net", "counter.yadro.ru", "gstatic.com", "static.220-volt.ru"],
            "parsing_project_id": "d49a33d7-bb98-41cf-ab53-57402e1c6fe5",
            "ping_url": null,
            "projectMasks": {},
            "cookies": "",
            "cookies_domain": "",
            "browser": "chrome",
            "restart_browser": 0,
            "parsing_type": "normal",
            "prepare_pages": false,
            "droid_type": "v-droid",
            "skip": null,
        };
    }
}