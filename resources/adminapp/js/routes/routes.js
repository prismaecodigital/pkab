import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const View = { template: '<router-view></router-view>' }

const routes = [
  {
    path: '/',
    component: () => import('@pages/Layout/DashboardLayout.vue'),
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('@pages/Dashboard.vue'),
        meta: { title: 'global.dashboard' }
      },
      {
        path: 'pkab',
        name: 'pkab',
        component: View,
        redirect: { name: 'pkab_items.index' },
        children: [
          {
            path: 'pkab-items',
            name: 'pkab_items.index',
            component: () => import('@cruds/PkabItems/Index.vue'),
            meta: { title: 'cruds.pkabItem.title' }
          },
          {
            path: 'pkab-items/create',
            name: 'pkab_items.create',
            component: () => import('@cruds/PkabItems/Create.vue'),
            meta: { title: 'cruds.pkabItem.title' }
          },
          {
            path: 'pkab-items/:id',
            name: 'pkab_items.show',
            component: () => import('@cruds/PkabItems/Show.vue'),
            meta: { title: 'cruds.pkabItem.title' }
          },
          {
            path: 'pkab-items/:id/edit',
            name: 'pkab_items.edit',
            component: () => import('@cruds/PkabItems/Edit.vue'),
            meta: { title: 'cruds.pkabItem.title' }
          },
          {
            path: 'pkab-dones',
            name: 'pkab_dones.index',
            component: () => import('@cruds/PkabDones/Index.vue'),
            meta: { title: 'cruds.pkabItem.selesai' }
          },
          {
            path: 'pkab-dones/:id',
            name: 'pkab-dones.show',
            component: () => import('@cruds/PkabDones/Show.vue'),
            meta: { title: 'cruds.pkabItem.selesai' }
          },
        ]
      },
      {
        path: 'marketlist',
        name: 'marketlist',
        component: View,
        redirect: { name: 'marketlists.index' },
        children: [
          {
            path: 'marketlist',
            name: 'marketlists.index',
            component: () => import('@cruds/Marketlists/Index.vue'),
            meta: { title: 'cruds.marketlist.title' }
          },
          {
            path: 'marketlist/create',
            name: 'marketlists.create',
            component: () => import('@cruds/Marketlists/Create.vue'),
            meta: { title: 'cruds.marketlist.title' }
          },
          {
            path: 'marketlist/:id',
            name: 'marketlists.show',
            component: () => import('@cruds/Marketlists/Show.vue'),
            meta: { title: 'cruds.marketlist.title' }
          },
          {
            path: 'marketlist/:id/edit',
            name: 'marketlists.edit',
            component: () => import('@cruds/Marketlists/Edit.vue'),
            meta: { title: 'cruds.marketlist.title' }
          },
          {
            path: 'marketlist-dones',
            name: 'marketlist-dones.index',
            component: () => import('@cruds/MarketlistDones/Index.vue'),
            meta: { title: 'cruds.marketlist.title' }
          },
          {
            path: 'marketlist-dones/:id',
            name: 'marketlist-dones.show',
            component: () => import('@cruds/MarketlistDones/Show.vue'),
            meta: { title: 'cruds.marketlist.title' }
          },
          {
            path: 'marketlist-items',
            name: 'marketlist-items.index',
            component: () => import('@cruds/MarketlistItems/Index.vue'),
            meta: { title: 'cruds.marketlistItem.title' }
          },
          {
            path: 'marketlist-items/create',
            name: 'marketlist-items.create',
            component: () => import('@cruds/MarketlistItems/Create.vue'),
            meta: { title: 'cruds.marketlistItem.title' }
          },
          {
            path: 'marketlist-items/:id',
            name: 'marketlist-items.show',
            component: () => import('@cruds/MarketlistItems/Show.vue'),
            meta: { title: 'cruds.marketlistItem.title' }
          },
          {
            path: 'marketlist-items/:id/edit',
            name: 'marketlist-items.edit',
            component: () => import('@cruds/MarketlistItems/Edit.vue'),
            meta: { title: 'cruds.marketlistItem.title' }
          },
        ]
      },
      {
        path: 'user-management',
        name: 'user_management',
        component: View,
        redirect: { name: 'permissions.index' },
        children: [
          {
            path: 'permissions',
            name: 'permissions.index',
            component: () => import('@cruds/Permissions/Index.vue'),
            meta: { title: 'cruds.permission.title' }
          },
          {
            path: 'permissions/create',
            name: 'permissions.create',
            component: () => import('@cruds/Permissions/Create.vue'),
            meta: { title: 'cruds.permission.title' }
          },
          {
            path: 'permissions/:id',
            name: 'permissions.show',
            component: () => import('@cruds/Permissions/Show.vue'),
            meta: { title: 'cruds.permission.title' }
          },
          {
            path: 'permissions/:id/edit',
            name: 'permissions.edit',
            component: () => import('@cruds/Permissions/Edit.vue'),
            meta: { title: 'cruds.permission.title' }
          },
          {
            path: 'roles',
            name: 'roles.index',
            component: () => import('@cruds/Roles/Index.vue'),
            meta: { title: 'cruds.role.title' }
          },
          {
            path: 'roles/create',
            name: 'roles.create',
            component: () => import('@cruds/Roles/Create.vue'),
            meta: { title: 'cruds.role.title' }
          },
          {
            path: 'roles/:id',
            name: 'roles.show',
            component: () => import('@cruds/Roles/Show.vue'),
            meta: { title: 'cruds.role.title' }
          },
          {
            path: 'roles/:id/edit',
            name: 'roles.edit',
            component: () => import('@cruds/Roles/Edit.vue'),
            meta: { title: 'cruds.role.title' }
          },
          {
            path: 'users',
            name: 'users.index',
            component: () => import('@cruds/Users/Index.vue'),
            meta: { title: 'cruds.user.title' }
          },
          {
            path: 'users/create',
            name: 'users.create',
            component: () => import('@cruds/Users/Create.vue'),
            meta: { title: 'cruds.user.title' }
          },
          {
            path: 'users/:id',
            name: 'users.show',
            component: () => import('@cruds/Users/Show.vue'),
            meta: { title: 'cruds.user.title' }
          },
          {
            path: 'users/:id/edit',
            name: 'users.edit',
            component: () => import('@cruds/Users/Edit.vue'),
            meta: { title: 'cruds.user.title' }
          }
        ]
      },
      {
        path: 'bu-dept',
        name: 'bu_dept',
        component: View,
        redirect: { name: 'bus.index' },
        children: [
          {
            path: 'bus',
            name: 'bus.index',
            component: () => import('@cruds/Bus/Index.vue'),
            meta: { title: 'cruds.bu.title' }
          },
          {
            path: 'bus/create',
            name: 'bus.create',
            component: () => import('@cruds/Bus/Create.vue'),
            meta: { title: 'cruds.bu.title' }
          },
          {
            path: 'bus/:id',
            name: 'bus.show',
            component: () => import('@cruds/Bus/Show.vue'),
            meta: { title: 'cruds.bu.title' }
          },
          {
            path: 'bus/:id/edit',
            name: 'bus.edit',
            component: () => import('@cruds/Bus/Edit.vue'),
            meta: { title: 'cruds.bu.title' }
          },
          {
            path: 'depts',
            name: 'depts.index',
            component: () => import('@cruds/Depts/Index.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'depts/create',
            name: 'depts.create',
            component: () => import('@cruds/Depts/Create.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'depts/:id',
            name: 'depts.show',
            component: () => import('@cruds/Depts/Show.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'depts/:id/edit',
            name: 'depts.edit',
            component: () => import('@cruds/Depts/Edit.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'sites',
            name: 'sites.index',
            component: () => import('@cruds/Sites/Index.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'sites/create',
            name: 'sites.create',
            component: () => import('@cruds/Sites/Create.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'sites/:id',
            name: 'sites.show',
            component: () => import('@cruds/Sites/Show.vue'),
            meta: { title: 'cruds.dept.title' }
          },
          {
            path: 'sites/:id/edit',
            name: 'sites.edit',
            component: () => import('@cruds/Sites/Edit.vue'),
            meta: { title: 'cruds.dept.title' }
          }
        ]
      },
      {
        path: 'profile/edit',
        name: 'profile.password.edit',
        component: () => import('@cruds/Profile/Edit.vue'),
        meta: { title: 'Profile' }
      }
    ]
  }
]

export default new VueRouter({
  mode: 'history',
  base: '/admin',
  routes
})