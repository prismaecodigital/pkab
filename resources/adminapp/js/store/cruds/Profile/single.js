function initialState() {
  return {
    entry: {
      id: null,
      name: '',
      email: null,
      no_hp: null,
      email_verified_at: '',
      username: null,
      password: null,
      roles: [],
      remember_token: '',
      bu: [],
      dept: [],
      notifable_email: true,
      notifable_wa: false,
      created_at: '',
      updated_at: '',
      deleted_at: ''
    },
    lists: {
      roles: [],
      dept: []
    },
    loading: false
  }
}

const route = 'password'

const getters = {
  entry: state => state.entry,
  lists: state => state.lists,
  loading: state => state.loading
}

const actions = {
  storeData({ commit, state, dispatch }) {
    commit('setLoading', true)
    dispatch('Alert/resetState', null, { root: true })

    return new Promise((resolve, reject) => {
      let params = objectToFormData(state.entry, {
        indices: true,
        booleansAsIntegers: true
      })
      axios
        .post(route, params)
        .then(response => {
          resolve(response)
        })
        .catch(error => {
          let message = error.response.data.message || error.message
          let errors = error.response.data.errors

          dispatch(
            'Alert/setAlert',
            { message: message, errors: errors, color: 'danger' },
            { root: true }
          )

          reject(error)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    })
  },
  updateData({ commit, state, dispatch }) {
    commit('setLoading', true)
    dispatch('Alert/resetState', null, { root: true })

    return new Promise((resolve, reject) => {
      let params = objectToFormData(state.entry, {
        indices: true,
        booleansAsIntegers: true
      })
      params.set('_method', 'POST')
      axios
        .post(`${route}`, params)
        .then(response => {
          resolve(response)
        })
        .catch(error => {
          let message = error.response.data.message || error.message
          let errors = error.response.data.errors

          dispatch(
            'Alert/setAlert',
            { message: message, errors: errors, color: 'danger' },
            { root: true }
          )

          reject(error)
        })
        .finally(() => {
          commit('setLoading', false)
        })
    })
  },
  setName({ commit }, value) {
    commit('setName', value)
  },
  setUsername({ commit }, value) {
    commit('setUsername', value)
  },
  setEmail({ commit }, value) {
    commit('setEmail', value)
  },
  setPhone({ commit }, value) {
    commit('setPhone', value)
  },
  setEmailVerifiedAt({ commit }, value) {
    commit('setEmailVerifiedAt', value)
  },
  setPassword({ commit }, value) {
    commit('setPassword', value)
  },
  setRoles({ commit }, value) {
    commit('setRoles', value)
  },
  setRememberToken({ commit }, value) {
    commit('setRememberToken', value)
  },
  setNotifableEmail( {commit}) {
    commit('setNotifableEmail')
  },
  setNotifableWa( {commit}) {
    commit('setNotifableWa')
  },
  setBu({ commit }, value) {
    commit('setBu', value)
  },
  setDept({ commit }, value) {
    commit('setDept', value)
  },
  setCreatedAt({ commit }, value) {
    commit('setCreatedAt', value)
  },
  setUpdatedAt({ commit }, value) {
    commit('setUpdatedAt', value)
  },
  setDeletedAt({ commit }, value) {
    commit('setDeletedAt', value)
  },
  fetchEditData({ commit, dispatch }) {
    axios.get(`${route}/edit`).then(response => {
      commit('setEntry', response.data.data)
      commit('setLists', response.data.meta)
    })
  },
  resetState({ commit }) {
    commit('resetState')
  }
}

const mutations = {
  setEntry(state, entry) {
    // Preserve the specific fields
    const notifableEmail = +entry.notifable_email === 1;
    const notifableWa = +entry.notifable_wa === 1;

    // Update the entry state by spreading the existing entry and the new entry data
    state.entry = {
      ...state.entry,
      ...entry,

      // Explicitly set notifable_email and notifable_wa from database
      notifable_email: notifableEmail,
      notifable_wa: notifableWa
    };
  },
  setName(state, value) {
    state.entry.name = value
  },
  setUsername(state, value) {
    state.entry.username = value
  },
  setEmail(state, value) {
    state.entry.email = value
  },
  setPhone(state, value) {
    state.entry.no_hp = value
  },
  setEmailVerifiedAt(state, value) {
    state.entry.email_verified_at = value
  },
  setPassword(state, value) {
    state.entry.password = value
  },
  setRoles(state, value) {
    state.entry.roles = value
  },
  setRememberToken(state, value) {
    state.entry.remember_token = value
  },
  setBu(state, value) {
    state.entry.bu = value
  },
  setDept(state, value) {
    state.entry.dept = value
  },
  setNotifableEmail(state) {
    state.entry.notifable_email = !state.entry.notifable_email
  },
  setNotifableWa(state) {
    state.entry.notifable_wa = !state.entry.notifable_wa
  },
  setCreatedAt(state, value) {
    state.entry.created_at = value
  },
  setUpdatedAt(state, value) {
    state.entry.updated_at = value
  },
  setDeletedAt(state, value) {
    state.entry.deleted_at = value
  },
  setLists(state, lists) {
    state.lists = lists
  },
  setLoading(state, loading) {
    state.loading = loading
  },
  resetState(state) {
    state = Object.assign(state, initialState())
  }
}

export default {
  namespaced: true,
  state: initialState,
  getters,
  actions,
  mutations
}