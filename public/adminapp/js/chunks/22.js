(window.webpackJsonp=window.webpackJsonp||[]).push([[22],{H7S1:function(t,a,e){(t.exports=e("I1BE")(!1)).push([t.i,".fade-enter-active[data-v-47896582],.fade-leave-active[data-v-47896582]{transition:opacity .1s}.fade-enter[data-v-47896582],.fade-leave-to[data-v-47896582]{opacity:0}",""])},RPvt:function(t,a,e){"use strict";e.r(a);var s={},i=(e("wfxb"),e("KHd+")),n={methods:{toggleSidebar:function(){this.$sidebar.displaySidebar(!this.$sidebar.showSidebar)}}},r={},l={components:{DashboardContent:Object(i.a)(s,(function(){var t=this._self._c;return t("transition",{attrs:{name:"fade",mode:"out-in"}},[t("router-view",{key:this.$route.fullPath})],1)}),[],!1,null,"47896582",null).exports,TopNavbar:Object(i.a)(n,(function(){var t=this,a=t._self._c;return a("nav",{staticClass:"navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top"},[a("div",{staticClass:"container-fluid"},[a("div",{staticClass:"navbar-wrapper"},[a("div",{staticClass:"navbar-brand"},[t._v("\n        "+t._s(t.$t(t.$route.meta.title))+"\n      ")])]),t._v(" "),a("button",{staticClass:"navbar-toggler",class:{toggled:t.$sidebar.showSidebar},attrs:{type:"button","data-toggle":"collapse","aria-controls":"navigation-index","aria-expanded":"false","aria-label":"Toggle navigation"},on:{click:t.toggleSidebar}},[a("span",{staticClass:"sr-only"},[t._v("Toggle navigation")]),t._v(" "),a("span",{staticClass:"navbar-toggler-icon icon-bar"}),t._v(" "),a("span",{staticClass:"navbar-toggler-icon icon-bar"}),t._v(" "),a("span",{staticClass:"navbar-toggler-icon icon-bar"})]),t._v(" "),a("div",{staticClass:"collapse navbar-collapse justify-content-end"},[a("ul",{staticClass:"navbar-nav"},[a("top-navbar-languages")],1)])])])}),[],!1,null,null,null).exports,MobileMenu:Object(i.a)(r,(function(){var t=this._self._c;return t("ul",{staticClass:"nav nav-mobile-menu"},[t("top-navbar-languages")],1)}),[],!1,null,null,null).exports},data:function(){return{sidebarLinks:[{title:"global.dashboard",icon:"dashboard",path:{name:"dashboard"}},{title:"cruds.pkab.title",icon:"list_alt",path:{name:"pkab"},gate:"pkab_access",children:[{title:"cruds.pkabItem.title",icon:"table_view",path:{name:"pkab_items.index"},gate:"pkab_item_access"},{title:"cruds.pkabItem.selesai",icon:"check_circle",path:{name:"pkab_dones.index"},gate:"pkab_item_access"}]},{title:"cruds.marketlist.title",icon:"storefront",path:{name:"marketlist"},gate:"marketlist_access",children:[{title:"cruds.marketlist.title",icon:"table_view",path:{name:"marketlists.index"},gate:"marketlist_access"},{title:"cruds.marketlist.selesai",icon:"check_circle",path:{name:"marketlist-dones.index"},gate:"marketlist_access"},{title:"Item Marketlist Master",icon:"category",path:{name:"marketlist-items.index"},gate:"marketlist_access"}]},{title:"cruds.userManagement.title",icon:"person",path:{name:"user_management"},gate:"user_management_access",children:[{title:"cruds.permission.title",icon:"perm_data_setting",path:{name:"permissions.index"},gate:"permission_access"},{title:"cruds.role.title",icon:"group",path:{name:"roles.index"},gate:"role_access"},{title:"cruds.user.title",icon:"person",path:{name:"users.index"},gate:"user_access"}]},{title:"cruds.buDept.title",icon:"table_view",path:{name:"bu_dept"},gate:"bu_dept_access",children:[{title:"cruds.bu.title",icon:"business",path:{name:"bus.index"},gate:"bu_access"},{title:"cruds.site.title",icon:"work",path:{name:"sites.index"},gate:"site_access"},{title:"cruds.dept.title",icon:"work_outline",path:{name:"depts.index"},gate:"dept_access"}]},{title:"Profile",icon:"person",path:{name:"profile.password.edit"},gate:"profile"}]}}},c=Object(i.a)(l,(function(){var t=this._self._c;return t("div",{staticClass:"wrapper",class:{"nav-open":this.$sidebar.showSidebar}},[t("event-hub"),this._v(" "),t("side-bar",{attrs:{sidebarLinks:this.sidebarLinks}},[t("mobile-menu",{attrs:{slot:"content"},slot:"content"})],1),this._v(" "),t("div",{staticClass:"main-panel"},[t("top-navbar"),this._v(" "),t("div",{staticClass:"content"},[t("dashboard-content")],1)],1)],1)}),[],!1,null,null,null);a.default=c.exports},ruTY:function(t,a,e){var s=e("H7S1");"string"==typeof s&&(s=[[t.i,s,""]]);var i={hmr:!0,transform:void 0,insertInto:void 0};e("aET+")(s,i);s.locals&&(t.exports=s.locals)},wfxb:function(t,a,e){"use strict";e("ruTY")}}]);