(window.webpackJsonp=window.webpackJsonp||[]).push([[43],{GCgq:function(t,e,s){"use strict";s.r(e);var a=s("VTBJ"),l=(s("FNk8"),s("L2JU")),i={data:function(){return{status:"",activeField:"",depts:[]}},computed:Object(a.a)({},Object(l.c)("UsersSingle",["entry","loading","lists"])),beforeDestroy:function(){this.resetState()},watch:{"$route.params.id":{immediate:!0,handler:function(){this.resetState(),this.fetchEditData(this.$route.params.id)}}},methods:Object(a.a)(Object(a.a)({},Object(l.b)("UsersSingle",["fetchEditData","updateData","resetState","setName","setEmail","setPassword","setRoles","setBu","setDept","setListDepts","setUsername"])),{},{selectAllBu:function(){this.setBu("all")},deselectAllBu:function(){this.setBu([]),this.setDept([]),this.setListDepts([])},selectAllDept:function(){this.setDept("all")},deselectAllDept:function(){this.setDept([])},updateName:function(t){this.setName(t.target.value)},updateUsername:function(t){this.setUsername(t.target.value)},updateEmail:function(t){this.setEmail(t.target.value)},updatePassword:function(t){this.setPassword(t.target.value)},updateRoles:function(t){this.setRoles(t)},submitForm:function(){var t=this;this.updateData().then((function(){t.$router.push({name:"users.index"}),t.$eventHub.$emit("update-success")})).catch((function(e){t.status="failed",_.delay((function(){t.status=""}),3e3)}))},focusField:function(t){this.activeField=t},clearFocus:function(){this.activeField=""},updateBu:function(t){var e=this;this.setDept([]);var s=t.map((function(t){return t.id}));this.setBu(t),null!=t[0]?axios.get("/budept-all",{params:{bu:s}}).then((function(t){e.depts=t.data})).catch((function(t){console.log(t)})):this.depts=[],console.log("ok")},updateDept:function(t){this.setDept(t)}})},r=s("KHd+"),u=Object(r.a)(i,(function(){var t=this,e=t._self._c;return e("div",{staticClass:"container-fluid"},[e("form",{on:{submit:function(e){return e.preventDefault(),t.submitForm.apply(null,arguments)}}},[e("div",{staticClass:"row"},[e("div",{staticClass:"col-md-12"},[e("div",{staticClass:"card"},[e("div",{staticClass:"card-header card-header-primary card-header-icon"},[t._m(0),t._v(" "),e("h4",{staticClass:"card-title"},[t._v("\n              "+t._s(t.$t("global.edit"))+"\n              "),e("strong",[t._v(t._s(t.$t("cruds.user.title_singular")))])])]),t._v(" "),e("div",{staticClass:"card-body"},[e("back-button")],1),t._v(" "),e("div",{staticClass:"card-body"},[e("bootstrap-alert"),t._v(" "),e("div",{staticClass:"row"},[e("div",{staticClass:"col-md-12"},[e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":t.entry.name,"is-focused":"name"==t.activeField}},[e("label",{staticClass:"bmd-label-floating required"},[t._v(t._s(t.$t("cruds.user.fields.name")))]),t._v(" "),e("input",{staticClass:"form-control",attrs:{type:"text",required:""},domProps:{value:t.entry.name},on:{input:t.updateName,focus:function(e){return t.focusField("name")},blur:t.clearFocus}})]),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":t.entry.username,"is-focused":"username"==t.activeField}},[e("label",{staticClass:"bmd-label-floating required"},[t._v(t._s(t.$t("cruds.user.fields.username")))]),t._v(" "),e("input",{staticClass:"form-control",attrs:{type:"text",required:""},domProps:{value:t.entry.username},on:{input:t.updateUsername,focus:function(e){return t.focusField("username")},blur:t.clearFocus}})]),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":t.entry.email,"is-focused":"email"==t.activeField}},[e("label",{staticClass:"bmd-label-floating required"},[t._v(t._s(t.$t("cruds.user.fields.email")))]),t._v(" "),e("input",{staticClass:"form-control",attrs:{type:"text",required:""},domProps:{value:t.entry.email},on:{input:t.updateEmail,focus:function(e){return t.focusField("email")},blur:t.clearFocus}})]),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":t.entry.password,"is-focused":"password"==t.activeField}},[e("label",{staticClass:"bmd-label-floating"},[t._v(t._s(t.$t("cruds.user.fields.password")))]),t._v(" "),e("input",{staticClass:"form-control",attrs:{type:"password"},domProps:{value:t.entry.password},on:{input:t.updatePassword,focus:function(e){return t.focusField("password")},blur:t.clearFocus}})]),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":0!==t.entry.roles.length,"is-focused":"roles"==t.activeField}},[e("label",{staticClass:"required"},[t._v(t._s(t.$t("cruds.user.fields.roles")))]),t._v(" "),e("v-select",{key:"roles-field",attrs:{name:"roles",label:"title",value:t.entry.roles,options:t.lists.roles,closeOnSelect:!1,multiple:""},on:{input:t.updateRoles}})],1),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":0!==t.entry.dept.length,"is-focused":"bu"==t.activeField}},[e("label",{},[t._v(t._s(t.$t("cruds.user.fields.bu")))]),t._v(" "),e("v-select",{key:"bu-field",attrs:{name:"bu",label:"name",value:t.entry.bu,options:t.lists.bu,closeOnSelect:!1,multiple:""},on:{input:t.updateBu,change:t.updateBu}}),t._v(" "),e("span",{staticClass:"select-all badge",on:{click:t.selectAllBu}},[t._v("Pilih Semua")]),t._v(" "),e("span",{staticClass:"select-all badge",on:{click:t.deselectAllBu}},[t._v("Batalkan pilihan")])],1),t._v(" "),e("div",{staticClass:"form-group bmd-form-group",class:{"is-filled":0!==t.entry.dept.length,"is-focused":"dept"==t.activeField}},[e("label",{},[t._v(t._s(t.$t("cruds.user.fields.dept")))]),t._v(" "),e("v-select",{key:"dept-field",attrs:{name:"dept",label:"name",value:t.entry.dept,options:t.lists.dept,closeOnSelect:!1,multiple:""},on:{input:t.updateDept}}),t._v(" "),e("span",{staticClass:"select-all badge",on:{click:t.selectAllDept}},[t._v("Pilih Semua")]),t._v(" "),e("span",{staticClass:"select-all badge",on:{click:t.deselectAllDept}},[t._v("Batalkan pilihan")])],1)])])],1),t._v(" "),e("div",{staticClass:"card-footer"},[e("vue-button-spinner",{staticClass:"btn-primary",attrs:{status:t.status,isLoading:t.loading,disabled:t.loading}},[t._v("\n              "+t._s(t.$t("global.save"))+"\n            ")])],1)])])])])])}),[function(){var t=this._self._c;return t("div",{staticClass:"card-icon"},[t("i",{staticClass:"material-icons"},[this._v("edit")])])}],!1,null,null,null);e.default=u.exports}}]);