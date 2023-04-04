import Vue from 'vue'
import Vuex from 'vuex'

import Alert from './modules/alert'
import I18NStore from './modules/i18n'

import PermissionsIndex from './cruds/Permissions'
import PermissionsSingle from './cruds/Permissions/single'
import RolesIndex from './cruds/Roles'
import RolesSingle from './cruds/Roles/single'
import UsersIndex from './cruds/Users'
import UsersSingle from './cruds/Users/single'
import BusIndex from './cruds/Bus'
import BusSingle from './cruds/Bus/single'
import DeptsIndex from './cruds/Depts'
import DeptsSingle from './cruds/Depts/single'
import PkabItemsIndex from './cruds/PkabItems'
import PkabItemsSingle from './cruds/PkabItems/single'
import PkabDonesIndex from './cruds/PkabDones'
import PkabDonesSingle from './cruds/PkabDones/single'
import ItemsIndex from './cruds/Items'
import ItemsSingle from './cruds/Items/single'
import StatusHistoriesIndex from './cruds/StatusHistories'

Vue.use(Vuex)

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
  modules: {
    Alert,
    I18NStore,
    PermissionsIndex,
    PermissionsSingle,
    RolesIndex,
    RolesSingle,
    UsersIndex,
    UsersSingle,
    BusIndex,
    BusSingle,
    DeptsIndex,
    DeptsSingle,
    PkabItemsIndex,
    PkabItemsSingle,
    PkabDonesIndex,
    PkabDonesSingle,
    ItemsIndex,
    ItemsSingle,
    StatusHistoriesIndex
  },
  strict: debug
})