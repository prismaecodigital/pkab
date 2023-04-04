function initialState() {
    return {
      entry: {
        id: null,
        pkab_id: null,
        name: '',
        merk: '',
        spesifikasi: '',
        qty: '',
        created_at: '',
        updated_at: '',
        deleted_at: ''
      },
      lists: {
        pkab: []
      },
      loading: false
    }
  }
  
  const route = 'items'
  
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
        params.set('_method', 'PUT')
        axios
          .post(`${route}/${state.entry.id}`, params)
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
    setPkab({ commit }, value) {
      commit('setPkab', value)
    },
    setName({ commit }, value) {
      commit('setName', value)
    },
    setMerk({ commit }, value) {
      commit('setMerk', value)
    },
    setSpesifikasi({ commit }, value) {
      commit('setSpesifikasi', value)
    },
    setQty({ commit }, value) {
      commit('setQty', value)
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
    resetState({ commit }) {
      commit('resetState')
    }
  }
  
  const mutations = {
    setEntry(state, entry) {
      state.entry = entry
    },
    setPkab(state, value) {
      state.entry.pkab_id = value
    },
    setName(state, value) {
      state.entry.name = value
    },
    setMerk(state, value) {
      state.entry.merk = value
    },
    setSpesifikasi(state, value) {
      state.entry.spesifikasi = value
    },
    setQty(state, value) {
      state.entry.qty = value
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