<template>
  <div class="wrapper" :class="{ 'nav-open': $sidebar.showSidebar }">
    <event-hub></event-hub>
    <side-bar :sidebarLinks="sidebarLinks">
      <mobile-menu slot="content"></mobile-menu>
    </side-bar>

    <div class="main-panel">
      <top-navbar></top-navbar>
      <div class="content">
        <dashboard-content></dashboard-content>
      </div>
    </div>
  </div>
</template>

<script>
import DashboardContent from './Content.vue'
import TopNavbar from './TopNavbar.vue'
import MobileMenu from './MobileMenu.vue'

export default {
  components: {
    DashboardContent,
    TopNavbar,
    MobileMenu
  },
  data() {
    return {
      sidebarLinks: [
        {
          title: 'global.dashboard',
          icon: 'dashboard',
          path: { name: 'dashboard' }
        },
        {
          title: 'cruds.pkab.title',
          icon: 'list_alt',
          path: { name: 'pkab' },
          gate: 'pkab_access',
          children: [
            {
              title: 'cruds.pkabItem.title',
              icon: 'table_view',
              path: { name: 'pkab_items.index' },
              gate: 'pkab_item_access'
            },
            {
              title: 'cruds.pkabItem.selesai',
              icon: 'check_circle',
              path: { name: 'pkab_dones.index' },
              gate: 'pkab_item_access'
            }
          ]
        },
        {
          title: 'cruds.marketlist.title',
          icon: 'storefront',
          path: { name: 'marketlist' },
          gate: 'marketlist_access',
          children: [
            {
              title: 'cruds.marketlist.title',
              icon: 'table_view',
              path: { name: 'marketlists.index' },
              gate: 'marketlist_access'
            },
            {
              title: 'cruds.marketlist.selesai',
              icon: 'check_circle',
              path: { name: 'marketlist-dones.index' },
              gate: 'marketlist_access'
            },
            {
              title: 'Item Marketlist Master',
              icon: 'category',
              path: { name: 'marketlist-items.index' },
              gate: 'marketlist_access'
            },
          ]
        },
        {
          title: 'cruds.userManagement.title',
          icon: 'person',
          path: { name: 'user_management' },
          gate: 'user_management_access',
          children: [
            {
              title: 'cruds.permission.title',
              icon: 'perm_data_setting',
              path: { name: 'permissions.index' },
              gate: 'permission_access'
            },
            {
              title: 'cruds.role.title',
              icon: 'group',
              path: { name: 'roles.index' },
              gate: 'role_access'
            },
            {
              title: 'cruds.user.title',
              icon: 'person',
              path: { name: 'users.index' },
              gate: 'user_access'
            }
          ]
        },
        {
          title: 'cruds.buDept.title',
          icon: 'table_view',
          path: { name: 'bu_dept' },
          gate: 'bu_dept_access',
          children: [
            {
              title: 'cruds.bu.title',
              icon: 'business',
              path: { name: 'bus.index' },
              gate: 'bu_access'
            },
            {
              title: 'cruds.site.title',
              icon: 'work',
              path: { name: 'sites.index' },
              gate: 'site_access'
            },
            {
              title: 'cruds.dept.title',
              icon: 'work_outline',
              path: { name: 'depts.index' },
              gate: 'dept_access'
            },
          ]
        },
        {
          title: 'Profile',
          icon: 'person',
          path: { name: 'profile.password.edit' },
          gate: 'profile'
        },
        {
          title: 'Report Issue/Bug',
          icon: 'bug_report',
          path: '/report-issue',
        }
      ]
    }
  }
}
</script>