(window.webpackJsonp=window.webpackJsonp||[]).push([[19],{"+Jzw":function(t,e,a){var s=a("/mkV");"string"==typeof s&&(s=[[t.i,s,""]]);var i={hmr:!0,transform:void 0,insertInto:void 0};a("aET+")(s,i);s.locals&&(t.exports=s.locals)},"/mkV":function(t,e,a){(t.exports=a("I1BE")(!1)).push([t.i,".trashIconContainer[data-v-3a0b5bb2]:hover{cursor:pointer;color:red}",""])},"24w7":function(t,e,a){"use strict";a("+Jzw")},tX2I:function(t,e,a){"use strict";a.r(e);var s=a("ODXe"),i=a("VTBJ"),n=(a("FNk8"),a("BkPv"),a("I4K+"),a("//ze"),a("L2JU")),o=a("vkmG"),u=a("JcrP"),r={components:{BootstrapAlert:o.a},data:function(){return{status:"",activeField:"",sites:[],date:{disabledDates:{to:new Date(new Date-864e5)}},headers:[],excelData:[],rowIndex:null}},computed:Object(i.a)({},Object(n.c)("MarketlistsSingle",["entry","loading","lists"])),mounted:function(){this.fetchCreateData()},beforeDestroy:function(){this.resetState()},methods:Object(i.a)(Object(i.a)({},Object(n.b)("MarketlistsSingle",["storeData","resetState","setEvent","setBu","setSite","setItems","setItemId","setItemRequiredDate","setItemNotes","setItemQty","setItemSatuan","addItem","deleteItem","fetchCreateData"])),{},{updateEvent:function(t){this.setEvent(t.target.value)},updateBu:function(t){var e=this;this.setSite([]),this.setBu(t),null!=t?axios.get("/busite",{params:{bu:t}}).then((function(t){e.sites=t.data})).catch((function(t){console.log(t)})):this.sites=[]},updateSite:function(t){this.setSite(t)},submitForm:function(){var t=this;this.storeData().then((function(){t.$router.push({name:"marketlists.index"}),t.$eventHub.$emit("create-success")})).catch((function(e){t.status="failed",_.delay((function(){t.status=""}),3e3)}))},focusField:function(t){this.activeField=t},clearFocus:function(){this.activeField=""},updateItemId:function(t,e){this.setItemId({index:t,value:e})},updateItemRequiredDate:function(t,e){console.log(e),this.setItemRequiredDate({index:t,value:e})},updateItemNotes:function(t,e,a){a=e.target.value,this.setItemNotes({index:t,val:a})},updateItemQty:function(t,e,a){a=e.target.value,this.setItemQty({index:t,val:a})},updateItemSatuan:function(t,e){this.setItemSatuan({index:t,val:e})},updateItemRequiredDate2:function(t,e){var a=e.split("-"),i=Object(s.a)(a,3),n=i[0],o=i[1],u=i[2],r=new Date(u,o-1,n);console.log(r),this.setItemRequiredDate({index:t,value:r})},updateItemId2:function(t,e){var a=this.lists.item.find((function(t){return t.name===e}));console.log(a.id),this.setItemId({index:t,value:a.id})},updateItemNotes2:function(t,e){this.setItemNotes({index:t,val:e})},updateItemQty2:function(t,e){this.setItemQty({index:t,val:e})},updateItemSatuan2:function(t,e){this.setItemSatuan({index:t,val:e})},addNewRow:function(){this.addItem()},deleteRow:function(t){var e=this;console.log(t),this.$swal({title:"Hapus Item ini ?",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes",showCloseButton:!0}).then((function(a){a.isConfirmed&&e.deleteItem(t)}))},handleFileUpload:function(t){var e=t.target.files[0];e&&this.readExcelFile(e)},readExcelFile:function(t){var e=this,a=new FileReader;a.onload=function(t){var a=t.target.result,s=Object(u.a)(a,{type:"binary"}),i=s.SheetNames[0],n=s.Sheets[i],o=u.b.sheet_to_json(n,{raw:!1}).filter((function(t){return t.hasOwnProperty("Qty")}));console.log(o);for(var r=0;r<o.length;r++){var d=o[r];void 0!==d.Qty&&(0!==r&&e.addItem(),e.updateItemId2(r,d.Item),e.updateItemRequiredDate2(r,d.Tanggal_dibutuhkan),e.updateItemQty2(r,d.Qty),e.updateItemSatuan2(r,d.Satuan),e.updateItemNotes2(r,d.Notes))}},a.readAsBinaryString(t)}})},d=(a("24w7"),a("KHd+")),l=Object(d.a)(r,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"container-fluid"},[e("form",{on:{submit:function(e){return e.preventDefault(),t.submitForm.apply(null,arguments)}}},[e("div",{staticClass:"row"},[e("div",{staticClass:"col-md-12"},[e("div",{staticClass:"card"},[e("div",{staticClass:"card-header card-header-primary card-header-icon"},[t._m(0),t._v(" "),e("h4",{staticClass:"card-title"},[t._v("\n              "+t._s(t.$t("global.create"))+"\n              "),e("strong",[t._v("MARKETLIST")])])]),t._v(" "),e("div",{staticClass:"card-body"},[e("back-button")],1),t._v(" "),e("div",{staticClass:"card-body"},[e("bootstrap-alert"),t._v(" "),e("div",{staticClass:"row"},[e("div",{staticClass:"col-md-12"},[e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":t.entry.event,"is-focused":"event"==t.activeField}},[e("label",{staticClass:"bmd-label-floating required"},[t._v(t._s(t.$t("cruds.marketlist.fields.event")))]),t._v(" "),e("input",{staticClass:"form-control",attrs:{type:"text",required:""},domProps:{value:t.entry.event},on:{input:t.updateEvent,focus:function(e){return t.focusField("event")},blur:t.clearFocus}})]),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":null!==t.entry.bu_id,"is-focused":"bu"==t.activeField}},[e("label",{},[t._v(t._s(t.$t("cruds.marketlist.fields.bu")))]),t._v(" "),e("v-select",{key:"bu-field",attrs:{name:"bu",label:"name",value:t.entry.bu_id,options:t.lists.bu,reduce:function(t){return t.id}},on:{input:t.updateBu,search:[function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"focus",void 0,e.key,void 0)?null:t.focusField("bu")},function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"blur",void 0,e.key,void 0)?null:t.clearFocus.apply(null,arguments)}]}})],1),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":null!==t.entry.site_id,"is-focused":"site"==t.activeField}},[e("label",{},[t._v(t._s(t.$t("cruds.marketlist.fields.site")))]),t._v(" "),e("v-select",{key:"site-field",attrs:{name:"site",label:"name",value:t.entry.site_id,options:t.sites,reduce:function(t){return t.id}},on:{input:t.updateSite,search:[function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"focus",void 0,e.key,void 0)?null:t.focusField("site")},function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"blur",void 0,e.key,void 0)?null:t.clearFocus.apply(null,arguments)}]}})],1)])])],1),t._v(" "),e("div",{staticClass:"card-header card-header-primary card-header-icon"},[e("h4",{staticClass:"card-title"},[e("strong",[t._v("Item")]),t._v(" "),e("br"),e("br"),t._v("\n              Upload XLSX : "),e("input",{attrs:{type:"file"},on:{change:t.handleFileUpload}})])]),t._v(" "),e("br"),t._v(" "),e("div",{staticClass:"card-body"},[e("bootstrap-alert"),t._v(" "),e("table",{staticClass:"table table-bordered",attrs:{name:"inputItem"}},[t._m(1),t._v(" "),e("tbody",t._l(t.entry.items,(function(a,s){return e("tr",{key:s},[e("td",{staticClass:"trashIconContainer",attrs:{scope:"row"}},[e("i",{staticClass:"fa fa-trash-o",on:{click:function(e){return t.deleteRow(s)}}})]),t._v(" "),e("td",[e("v-select",{key:"item-field",attrs:{name:"item",label:"name",value:a.item_id,options:t.lists.item,reduce:function(t){return t.id}},on:{input:function(e){return t.updateItemId(s,e)},search:[function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"focus",void 0,e.key,void 0)?null:t.focusField("item")},function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"blur",void 0,e.key,void 0)?null:t.clearFocus.apply(null,arguments)}]}})],1),t._v(" "),e("td",[e("vuejs-datepicker",{attrs:{"input-class":"form-control",format:"dd-MM-yyyy","disabled-dates":t.date.disabledDates,value:a.required_date_front_end},on:{input:function(e){return t.updateItemRequiredDate(s,e)}}})],1),t._v(" "),e("td",[e("input",{staticClass:"form-control wrapText",attrs:{type:"number",step:"0.1",required:""},domProps:{value:a.qty},on:{input:function(e){return t.updateItemQty(s,e)}}})]),t._v(" "),e("td",[e("v-select",{key:"satuan-field",attrs:{name:"satuan",label:"name",value:a.satuan,options:t.lists.satuan},on:{input:function(e){return t.updateItemSatuan(s,e)},search:[function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"focus",void 0,e.key,void 0)?null:t.focusField("item")},function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"blur",void 0,e.key,void 0)?null:t.clearFocus.apply(null,arguments)}]}})],1),t._v(" "),e("td",[e("input",{staticClass:"form-control wrapText",attrs:{type:"text"},domProps:{value:a.notes},on:{input:function(e){return t.updateItemNotes(s,e)}}})])])})),0)]),t._v(" "),e("button",{staticClass:"btn btn-info",attrs:{type:"button"},on:{click:t.addNewRow}},[e("i",{staticClass:"fa fa-plus-circle"}),t._v("\n                Tambah Item\n            ")])],1),t._v(" "),e("div",{staticClass:"card-footer"},[e("vue-button-spinner",{staticClass:"btn-primary",attrs:{status:t.status,isLoading:t.loading,disabled:t.loading}},[t._v("\n              "+t._s(t.$t("global.save"))+"\n            ")])],1)])])])])])}),[function(){var t=this._self._c;return t("div",{staticClass:"card-icon"},[t("i",{staticClass:"material-icons"},[this._v("add")])])},function(){var t=this,e=t._self._c;return e("thead",[e("th"),t._v(" "),e("th",[t._v("Item")]),t._v(" "),e("th",[t._v("Tanggal dibutuhkan")]),t._v(" "),e("th",[t._v("Qty")]),t._v(" "),e("th",[t._v("Satuan")]),t._v(" "),e("th",[t._v("Notes")])])}],!1,null,"3a0b5bb2",null);e.default=l.exports}}]);