(window.webpackJsonp=window.webpackJsonp||[]).push([[14],{"1Vt+":function(t,e,a){"use strict";a("Z5sz")},"22Sv":function(t,e,a){"use strict";a("Wha0")},AKXe:function(t,e,a){"use strict";var s={props:["field","row"],computed:{entry:function(){var t=_.split(this.field,".");return{key:t[0],field:t[1]}}}},i=(a("1Vt+"),a("KHd+")),n=Object(i.a)(s,(function(){var t=this._self._c;return t("div",[this.row[this.entry.key]?t("span",{staticClass:"badge badge-pill badge-azure"},[this._v("\n    "+this._s(this.row[this.entry.key][this.entry.field])+"\n  ")]):t("span",{staticClass:"badge badge-pill badge-orange"},[this._v("\n    Not Assigned\n  ")])])}),[],!1,null,"bb08c5e2",null);e.a=n.exports},IC54:function(t,e,a){var s=a("bMjp");"string"==typeof s&&(s=[[t.i,s,""]]);var i={hmr:!0,transform:void 0,insertInto:void 0};a("aET+")(s,i);s.locals&&(t.exports=s.locals)},U4JL:function(t,e,a){(t.exports=a("I1BE")(!1)).push([t.i,"input:disabled,textarea:disabled{cursor:auto}",""])},Wha0:function(t,e,a){var s=a("U4JL");"string"==typeof s&&(s=[[t.i,s,""]]);var i={hmr:!0,transform:void 0,insertInto:void 0};a("aET+")(s,i);s.locals&&(t.exports=s.locals)},Z5sz:function(t,e,a){var s=a("n1M/");"string"==typeof s&&(s=[[t.i,s,""]]);var i={hmr:!0,transform:void 0,insertInto:void 0};a("aET+")(s,i);s.locals&&(t.exports=s.locals)},augP:function(t,e,a){"use strict";var s={props:["field","row"]},i=a("KHd+"),n=Object(i.a)(s,(function(){return(0,this._self._c)("div",[this._v("\n  "+this._s(this.row["".concat(this.field,"_label")])+"\n")])}),[],!1,null,null,null);e.a=n.exports},bMjp:function(t,e,a){(t.exports=a("I1BE")(!1)).push([t.i,'.timeline{padding:0;position:relative;list-style:none;font-family:PingFangSC-light,Avenir,Helvetica,Arial,Hiragino Sans GB,Microsoft YaHei,sans-serif;-webkit-font-smoothing:antialiased;margin:10px 20px}.timeline:after{position:absolute;content:"";left:0;top:0;width:1px;height:100%;background-color:var(--timelineTheme)}.timeline-item{position:relative;margin:1.5em 0 0 28px;padding-bottom:1.5em;border-bottom:1px dotted var(--timelineTheme)}.timeline-item:last-child{border-bottom:none}.timeline-circle{position:absolute;top:.28em;left:-34px;width:10px;height:10px;border-radius:50%;border:1px solid var(--timelineTheme);background-color:var(--timelineTheme);z-index:1;box-sizing:content-box}.timeline-circle.hollow{background-color:var(--timelineBg)}.timeline-title{position:relative;display:inline-block;cursor:crosshair;margin:-.15em 0 15px 28px}.timeline-title:not(:first-child){margin-top:28px}.timeline-title-circle{left:-36px;top:.15em;width:16px;height:16px}.timeline-others{width:40px;height:auto;top:.2em;left:-48px;line-height:1;padding:2px 0;text-align:center;border:none;background-color:var(--timelineBg)}',""])},"n1M/":function(t,e,a){(t.exports=a("I1BE")(!1)).push([t.i,".badge[data-v-bb08c5e2]{font-size:.875rem;font-weight:500;text-transform:none}",""])},sRPa:function(t,e,a){"use strict";a.r(e);var s=a("VTBJ"),i=a("L2JU"),n=a("AKXe"),r=a("augP"),o=(a("IC54"),{components:{DatatableSingle:n.a,DatatableEnum:r.a},data:function(){return{xprops:{module:"MarketlistsSingle"}}},beforeDestroy:function(){this.resetState()},computed:Object(s.a)({},Object(i.c)("MarketlistsSingle",["entry","lists"])),watch:{"$route.params.id":{immediate:!0,handler:function(){this.resetState(),this.fetchShowData(this.$route.params.id)}}},methods:Object(s.a)(Object(s.a)({},Object(i.b)("MarketlistsSingle",["fetchShowData","loading","resetState","approveData","setItemId","updateMergedData","rejectData","setItemRequiredDate","setItemNotes","setItemQty","setItemSatuan","setItemApprovedQty","setItemApprovedDate","setChecked"])),{},{updateItemId:function(t,e){this.setItemId({index:t,value:e})},updateItemRequiredDate:function(t,e){console.log(e),this.setItemRequiredDate({index:t,value:e})},updateItemQty:function(t,e,a){a=e.target.value,this.setItemQty({index:t,val:a})},updateItemSatuan:function(t,e){this.setItemSatuan({index:t,val:e})},updateItemApprovedQty:function(t,e,a){a=e.target.value,this.setItemApprovedQty({index:t,val:a})},updateItemApprovedDate:function(t,e){this.setItemApprovedDate({index:t,val:e})},updateChecked:function(t){var e=t.target.checked;this.entry.isClosed=e},approve:function(t){var e=this;this.$swal({title:"Approve?",text:"Are you sure you want to approve this item?",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes"}).then((function(a){if(a.value){a.value;e.$store.dispatch(e.xprops.module+"/approveData",t).then((function(t){e.fetchShowData(e.$route.params.id),e.$eventHub.$emit("approve-success")}))}}))},rejectData:function(t){var e=this;this.$swal({title:"Reject?",text:"Masukkan Alasan",input:"text",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes"}).then((function(a){a.value?e.$store.dispatch(e.xprops.module+"/rejectData",{id:t.id,ket:a.value}).then((function(t){e.fetchShowData(e.$route.params.id),e.$eventHub.$emit("reject-success")})):e.$swal({icon:"error",title:"Failed",text:"Masukkan Alasan"})}))}})}),l=(a("22Sv"),a("KHd+")),d=Object(l.a)(o,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"container-fluid"},[e("div",{staticClass:"row"},[e("div",{staticClass:"col-md-12"},[e("div",{staticClass:"card"},[e("div",{staticClass:"card-header card-header-primary card-header-icon"},[t._m(0),t._v(" "),e("h4",{staticClass:"card-title"},[t._v("\n            "+t._s(t.$t("global.view"))+" Detail\n            "),e("strong",[t._v(t._s(t.$t("cruds.marketlist.title_singular")))])])]),t._v(" "),e("div",{staticClass:"card-body"},[e("div",{staticClass:"row"},[e("div",{staticClass:"col-lg-3"},[e("back-button")],1)])]),t._v(" "),e("div",{staticClass:"card-body"},[e("div",{staticClass:"row"},[e("div",{staticClass:"col-md-12"},[e("div",{staticClass:"table-responsive"},[e("div",{staticClass:"table"},[e("tbody",[e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.code"))+"\n                      ")]),t._v(" "),e("td",[t._v("\n                        "+t._s(t.entry.code)+"\n                      ")])]),t._v(" "),e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.event"))+"\n                      ")]),t._v(" "),e("td",[t._v("\n                        "+t._s(t.entry.event)+"\n                      ")])]),t._v(" "),e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.created_at"))+"\n                      ")]),t._v(" "),e("td",[t._v("\n                        "+t._s(t.entry.created_at)+"\n                      ")])]),t._v(" "),e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.user"))+"\n                      ")]),t._v(" "),e("td",[e("datatable-single",{attrs:{row:t.entry,field:"user.name"}})],1)]),t._v(" "),e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.status"))+"\n                      ")]),t._v(" "),e("td",[e("datatable-enum",{attrs:{row:t.entry,field:"status"}})],1)]),t._v(" "),e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.bu"))+"\n                      ")]),t._v(" "),e("td",[e("datatable-single",{attrs:{row:t.entry,field:"bu.name"}})],1)]),t._v(" "),e("tr",[e("td",{staticClass:"text-primary"},[t._v("\n                        "+t._s(t.$t("cruds.marketlist.fields.site"))+"\n                      ")]),t._v(" "),e("td",[e("datatable-single",{attrs:{row:t.entry,field:"site.name"}})],1)])])])])])])]),t._v(" "),e("div",{staticClass:"card-body"},[e("bootstrap-alert"),t._v(" "),e("table",{staticClass:"table table-bordered",attrs:{name:"inputItem"}},[e("thead",[e("th",[t._v("Item")]),t._v(" "),e("th",[t._v("Tanggal dibutuhkan")]),t._v(" "),e("th",[t._v("Qty")]),t._v(" "),e("th",[t._v("Satuan")]),t._v(" "),e("th",[t._v("Notes")]),t._v(" "),"user_acc"===t.entry.status||"selesai"===t.entry.status?e("th",[t._v("Qty yg diterima")]):t._e(),t._v(" "),"user_acc"===t.entry.status||"selesai"===t.entry.status?e("th",[t._v("Tanggal diterima")]):t._e()]),t._v(" "),e("tbody",t._l(t.entry.items,(function(a,s){return e("tr",{key:s},[e("td",{staticStyle:{background:"#dadada"}},[e("input",{staticClass:"form-control wrapText",attrs:{disabled:""},domProps:{value:a.item.name}})]),t._v(" "),e("td",{staticStyle:{background:"#dadada"}},[e("input",{staticClass:"form-control wrapText",attrs:{disabled:""},domProps:{value:a.required_date}})]),t._v(" "),e("td",{staticStyle:{background:"#dadada"}},[t.$can(t.entry.status)&&t.$can("marketlist_edit")?e("input",{staticClass:"form-control wrapText",attrs:{type:"number",step:"0.1"},domProps:{value:a.qty},on:{input:function(e){return t.updateItemQty(s,e)}}}):e("input",{staticClass:"form-control wrapText",attrs:{disabled:"",type:"number",step:"0.1"},domProps:{value:a.qty}})]),t._v(" "),e("td",{staticStyle:{background:"#dadada"}},[t.$can(t.entry.status)&&t.$can("marketlist_edit")?e("v-select",{key:"satuan-field",attrs:{name:"satuan",label:"name",value:a.satuan,options:t.lists.satuan},on:{input:function(e){return t.updateItemSatuan(s,e)},search:[function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"focus",void 0,e.key,void 0)?null:t.focusField("item")},function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"blur",void 0,e.key,void 0)?null:t.clearFocus.apply(null,arguments)}]}}):e("input",{staticClass:"form-control wrapText",attrs:{disabled:"",type:"text"},domProps:{value:a.satuan}})],1),t._v(" "),e("td",{staticStyle:{background:"#dadada"}},[t.$can(t.entry.status)&&t.$can("marketlist_edit")?e("input",{staticClass:"form-control wrapText",attrs:{type:"text"},domProps:{value:a.notes},on:{input:function(e){return t.updateItemNotes(s,e)}}}):e("input",{staticClass:"form-control wrapText",attrs:{disabled:"",type:"text"},domProps:{value:a.notes}})]),t._v(" "),"user_acc"===t.entry.status||"selesai"===t.entry.status?e("td",[e("input",{staticClass:"form-control wrapText",attrs:{type:"number"},domProps:{value:a.approved_qty},on:{input:function(e){return t.updateItemApprovedQty(s,e)}}})]):t._e(),t._v(" "),"user_acc"===t.entry.status||"selesai"===t.entry.status?e("td",[null===a.approved_date?e("vuejs-datepicker",{attrs:{"input-class":"form-control",format:"dd-MM-yyyy",value:a.approved_date_front_end},on:{input:function(e){return t.updateItemApprovedDate(s,e)}}}):e("input",{staticClass:"form-control wrapText",attrs:{type:"text"},domProps:{value:a.approved_date},on:{input:function(e){return t.updateItemApprovedDate(s,e)}}})],1):t._e()])})),0)])],1),t._v(" "),t.$can(t.entry.status)?e("div",{staticClass:"card-body"},["user_acc"===t.entry.status?e("div",{staticClass:"row"},[e("div",{staticClass:"col-lg-3"},[e("label",[e("input",{attrs:{type:"checkbox"},domProps:{value:t.entry.isClosed},on:{input:t.updateChecked}}),t._v("\n                Close Order\n              ")])])]):t._e(),t._v(" "),e("div",{staticClass:"row"},[e("div",{staticClass:"col-lg-3"},[e("button",{staticClass:"btn btn-info",attrs:{type:"button"},on:{click:function(e){return e.preventDefault(),t.approve(t.entry)}}},[t._v("\n                Approve\n              ")])]),t._v(" "),e("div",{staticClass:"col-lg-3"},[e("button",{staticClass:"btn btn-danger",attrs:{type:"button"},on:{click:function(e){return e.preventDefault(),t.rejectData(t.entry)}}},[t._v("\n                Reject\n              ")])])])]):t._e()])])])])}),[function(){var t=this._self._c;return t("div",{staticClass:"card-icon"},[t("i",{staticClass:"material-icons"},[this._v("remove_red_eye")])])}],!1,null,null,null);e.default=d.exports}}]);