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
import ProfileSingle from './cruds/Profile/single'
import SitesIndex from './cruds/Sites'
import SitesSingle from './cruds/Sites/single'
import MarketlistItemsIndex from './cruds/MarketlistItems'
import MarketlistItemsSingle from './cruds/MarketlistItems/single'
import CategoriesIndex from './cruds/Categories'
import CategoriesSingle from './cruds/Categories/single'
import MarketlistsIndex from './cruds/Marketlists'
import MarketlistsSingle from './cruds/Marketlists/single'
import MarketlistDonesIndex from './cruds/MarketlistDones'
import MarketlistDonesSingle from './cruds/MarketlistDones/single'
import Survey from './modules/survey'

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
    ProfileSingle,
    SitesIndex,
    SitesSingle,
    CategoriesIndex,
    CategoriesSingle,
    MarketlistItemsIndex,
    MarketlistItemsSingle,
    MarketlistsIndex,
    MarketlistsSingle,
    MarketlistDonesIndex,
    MarketlistDonesSingle,
    Survey
  },
  strict: debug
})