(window.webpackJsonp=window.webpackJsonp||[]).push([[2],{"+fAT":function(t,e,n){var r={"./components/ImportXlsx.vue":"EVlk"};function o(t){var e=a(t);return n(e)}function a(t){if(!n.o(r,t)){var e=new Error("Cannot find module '"+t+"'");throw e.code="MODULE_NOT_FOUND",e}return r[t]}o.keys=function(){return Object.keys(r)},o.resolve=a,t.exports=o,o.id="+fAT"},0:function(t,e){},1:function(t,e){},2:function(t,e){},EVlk:function(t,e,n){"use strict";n.r(e);var r=n("EUZL"),o=n.n(r),a=["xlsx","xls","csv"].map((function(t){return"."+t})).join(","),s={data:function(){return{data:[["tst-1234567890","10","4","6","10.00","44140"]],cols:[{name:"ID заказа",field:"order_id",key:0},{name:"Оффер",field:"offer_id",type:"number",key:1},{name:"Партнер",field:"partner_id",type:"number",key:2},{name:"ID ссылки",field:"link_id",type:"number",key:3},{name:"Сумма заказа",field:"gross_amount",key:4},{name:"Дата заказа",field:"datetime",type:"text",key:5}],SheetJSFT:a}},methods:{_suppress:function(t){t.stopPropagation(),t.preventDefault()},_drop:function(t){t.stopPropagation(),t.preventDefault();var e=t.dataTransfer.files;e&&e[0]&&this._file(e[0])},_change:function(t){var e=t.target.files;e&&e[0]&&this._file(e[0])},_parseDate:function(t){return new Date(86400*(t-25568)*1e3).toISOString().slice(0,16)},_file:function(t){var e=this,n=new FileReader;n.onload=function(t){var n=t.target.result,r=o.a.read(n,{type:"binary"}),a=r.SheetNames[0],s=r.Sheets[a],i=o.a.utils.sheet_to_json(s,{header:1});i.shift();console.log(i[1]),e.data=i},n.readAsBinaryString(t)}}},i=n("KHd+"),l=Object(i.a)(s,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{on:{drop:t._drop,dragenter:t._suppress,dragover:t._suppress}},[n("input",{staticClass:"form-control",attrs:{type:"file"},on:{change:t._change}}),t._v(" "),n("div",{staticClass:"table-responsive"},[n("table",{staticClass:"table table-striped"},[n("thead",[n("tr",t._l(t.cols,(function(e){return n("th",{key:e.key},[t._v(t._s(e.name))])})),0)]),t._v(" "),n("tbody",t._l(t.data,(function(e,r){return n("tr",{key:r},[n("td",[n("input",{staticClass:"form-control form-control-sm",attrs:{type:"text",name:"order["+r+"][order_id]"},domProps:{value:e[0]}})]),t._v(" "),n("td",[n("input",{staticClass:"form-control form-control-sm",attrs:{type:"number",min:"0",name:"order["+r+"][offer_id]"},domProps:{value:e[1]}})]),t._v(" "),n("td",[n("input",{staticClass:"form-control form-control-sm",attrs:{type:"number",min:"0",name:"order["+r+"][partner_id]"},domProps:{value:e[2]}})]),t._v(" "),n("td",[n("input",{staticClass:"form-control form-control-sm",attrs:{type:"number",min:"0",name:"order["+r+"][link_id]"},domProps:{value:e[3]}})]),t._v(" "),n("td",[n("input",{staticClass:"form-control form-control-sm",attrs:{type:"number",min:"0",step:"0.01",name:"order["+r+"][gross_amount]"},domProps:{value:e[4]}})]),t._v(" "),n("td",[n("input",{staticClass:"form-control form-control-sm",attrs:{type:"datetime-local",name:"order["+r+"][datetime]"},domProps:{value:t._parseDate(e[5])}})])])})),0)])]),t._v(" "),n("button",{staticClass:"btn btn-primary",attrs:{type:"submit"}},[t._v("ok")])])}),[],!1,null,null,null);e.default=l.exports},f2An:function(t,e,n){window.Vue=n("XuX8");var r=n("+fAT");r.keys().map((function(t){return Vue.component(t.split("/").pop().split(".")[0],r(t).default)}));var o=document.getElementById("app");if(o){Vue.config.productionTip=!1;new Vue({el:o})}}}]);