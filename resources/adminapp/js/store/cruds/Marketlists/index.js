const set = key => (state, val) => {
    state[key] = val
  }
  
  function initialState() {
    return {
      data: [],
      jsonData: [],
      total: 0,
      query: {},
      lists: {
        bu : [],
        site : []
      },
      loading: false
    }
  }
  
  const route = 'marketlists'
  
  const getters = {
    data: state => state.data,
    total: state => state.total,
    loading: state => state.loading,
    jsonData: state => state.jsonData,
    lists: state => state.lists
  }
  
  const actions = {
    fetchIndexData({ commit, state }) {
      commit('setLoading', true)
      axios
        .get(route, { params: state.query })
        .then(response => {
          commit('setData', response.data.data.data)
          commit('setTotal', response.data.data.total)
          commit('setJsonData', response.data.data.data)
          commit('setLists', response.data.meta)
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
    setJsonData(state, data) {
      data.forEach(function(value) {
        value.selected = false
        value.items.forEach(function(val) {
          state.jsonData.push({
            'ml_id' : value.id,
            'BU': value.bu.name,
            'Site' : value.site.name ?? 'ML',
            'Kategori': val.item.category.name,
            'name': val.item.name + ' - ' + val.satuan + ' (' + val.notes + ')',
            'tanggal_dibutuhkan': val.required_date,
            'qty' : val.qty
          })
        })
      })
    },
    setTotal: set('total'),
    setQuery(state, query) {
      query.page = (query.offset + query.limit) / query.limit
      state.query = query
    },
    setLists(state, value) {
      state.lists = value
      console.log(value)
      console.log(state.lists)
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