const set = key => (state, val) => {
    state[key] = val
  }
  
  function initialState() {
    return {
      data: [],
      jsonData: [],
      total: 0,
      query: {},
      loading: false
    }
  }
  
  const route = 'marketlist-dones'
  
  const getters = {
    data: state => state.data,
    total: state => state.total,
    loading: state => state.loading,
    jsonData: state => state.jsonData
  }
  
  const actions = {
    fetchIndexData({ commit, state }) {
      commit('setLoading', true)
      axios
        .get(route, { params: state.query })
        .then(response => {
          commit('setData', response.data.data)
          commit('setTotal', response.data.total)
          commit('setJsonData', response.data.data)
        })
        .catch(error => {
          message = error.response.data.message || error.message
          // TODO error handling
        })
        .finally(() => {
          // console.log(state.query)
          commit('setLoading', false)
        })
    },
    destroyData({ commit, state, dispatch }, id) {
      axios
        .delete(`${route}/${id}`)
        .then(response => {
          dispatch('fetchIndexData')
        })
        .catch(error => {
          message = error.response.data.message || error.message
          // TODO error handling
        })
    },
    approveData({ commit, state, dispatch }, id) {
      commit('setLoading', true)
      axios.post(`/marketlists/approve`, {'id': id})
      .then(response => {
        // handle success
        dispatch('fetchIndexData')
      })
      .catch(error => {
        // handle error
        message = error.response.data.message || error.message
      })
    },
    rejectData({ commit, dispatch }, {id, ket}) {
      commit('setLoading', true)
      axios.post(`/marketlists/reject`, {'id': id, 'ket' : ket})
      .then(response => {
        // handle success
        dispatch('fetchIndexData')
      })
      .catch(error => {
        // handle error
        message = error.response.data.message || error.message
      })
    },
    setQuery({ commit }, value) {
      commit('setQuery', _.cloneDeep(value))
    },
    resetState({ commit }) {
      commit('resetState')
    }
  }
  
  const mutations = {
    setData: set('data'),
    setJsonData: set('jsonData'),
    setTotal: set('total'),
    setQuery(state, query) {
      query.page = (query.offset + query.limit) / query.limit
      state.query = query
    },
    setLoading: set('loading'),
    resetState(state) {
      Object.assign(state, initialState())
    }
  }
  
  export default {
    namespaced: true,
    state: initialState,
    getters,
    actions,
    mutations
  }