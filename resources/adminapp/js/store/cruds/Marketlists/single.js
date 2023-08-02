function initialState() {
    return {
      entry: {
        id: null,
        code: '',
        user_id: null,
        status: 'purchasing_ml_1',
        dept_id: null,
        site_id: null,
        bu_id: null,
        event: '',
        created_at: '',
        updated_at: '',
        deleted_at: '',
        isClosed: false,
        items: [{
          item_id : null,
          required_date : '',
          required_date_front_end : '',
          qty : 0,
          satuan: '',
          approved_qty: '',
          approved_date: '',
          approved_date_front_end: '',
          notes: '',
          item: [],
        }]
      },
      lists: {
        user: [],
        status: [],
        bu: [],
        site: [],
        item: [],
        satuan: []
      },
      loading: false,
      jsonData: [{
      }],
      mergedData: [],
      timelineData: []
    }
  }
  
  const route = 'marketlists'
  
  const getters = {
    entry: state => state.entry,
    lists: state => state.lists,
    loading: state => state.loading,
    jsonData: state => state.jsonData,
    jsonFields: state => state.jsonFields,
    mergedData: state => state.mergedData,
    timelineData: state => state.timelineData
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
    rejectData({ commit, state, dispatch }, {id, ket}) {
      commit('setLoading', true)
      dispatch('Alert/resetState', null, { root: true })
  
      return new Promise((resolve, reject) => {
        let params = objectToFormData(state.entry, {
          indices: true,
          booleansAsIntegers: true
        })
        // params.set('_method', 'PUT')
        params.set('id', id)
        params.set('ket', ket)
        axios
          .post(`${route}/rejectData`, params)
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
    approveData({ commit, dispatch }, id) {
      return axios.post(`${route}/${id}/approve`, { id })
        .then(response => {
          commit('setEntry', response.data.data)
          dispatch('fetchShowData', id, { root: true })
        })
    },
    setUser({ commit }, value) {
      commit('setUser', value)
    },
    setBu({ commit }, value) {
      commit('setBu', value)
    },
    setSite({ commit }, value) {
      commit('setSite', value)
    },
    setEvent({ commit }, value) {
      commit('setEvent', value)
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
    setItems({commit}, value) {
      commit('setItems', value)
    },
    setItemId({ commit }, {index, value}) {
      commit('setItemId', {index, value})
    },
    setItemRequiredDate({ commit }, {index, value}) {
      commit('setItemRequiredDate', {index, value})
    },
    setItemQty({ commit }, {index, val}) {
      commit('setItemQty', {index, val})
    },
    setItemSatuan({ commit }, {index, val}) {
      commit('setItemSatuan', {index, val})
    },
    setItemNotes({ commit }, {index, val}) {
      commit('setItemNotes', {index, val})
    },
    setItemApprovedQty({commit}, {index, val}) {
      commit('setItemApprovedQty', {index, val})
    },
    setItemApprovedDate({commit}, {index, val}) {
      commit('setItemApprovedDate', {index, val})
    },
    setChecked({commit}, {index, value}) {
      commit('setChecked', {index,value})
    },
    addItem({commit}) {
      commit('addItem')
    },
    exportData({commit}) {
      commit('exportData')
    },
    deleteItem({commit}) {
      commit('deleteItem')
    },
    fetchCreateData({ commit }) {
      axios.get(`${route}/create`).then(response => {
        commit('setLists', response.data.meta)
      })
    },
    fetchEditData({ commit, dispatch }, id) {
      axios.get(`${route}/${id}/edit`).then(response => {
        commit('setEntry', response.data.data)
        commit('setLists', response.data.meta)
      })
    },
    fetchShowData({ commit, dispatch }, id) {
      axios.get(`${route}/${id}`).then(response => {
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
      state.entry = entry
    },
    // setReqDate(state, value) {
    //   state.entry.req_date = moment(value).format('DD-MM-YYYY')
    //   state.entry.req_date_front_end = value
    // },
    setUser(state, value) {
      state.entry.user_id = value
    },
    setStatus(state, value) {
      state.entry.status = value
    },
    setBu(state, value) {
      state.entry.bu_id = value
    },
    setSite(state, value) {
      state.entry.site_id = value
    },
    setBu(state, value) {
      state.entry.bu_id = value
    },
    setEvent(state, value) {
      state.entry.event = value
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
    setItems(state, value) {
      state.entry.items = value
    },
    setItemId(state, {index,value}) {
      state.entry.items[index].item_id = value
      // console.log(state.entry.items[index].item_id)
    },
    setItemRequiredDate(state, {index,value}) {
      console.log(value)
      state.entry.items[index].required_date = moment(value).format('DD-MM-YYYY')
      state.entry.items[index].required_date_front_end = value
    },
    setItemApprovedDate(state, {index,val}) {
      state.entry.items[index].approved_date_front_end = val
      state.entry.items[index].approved_date = moment(val).format('DD-MM-YYYY')
      console.log(state.entry.items[index].approved_date)
    },
    setItemQty(state, {index,val}) {
      state.entry.items[index].qty = val
    },
    setItemSatuan(state, {index,val}) {
      state.entry.items[index].satuan = val
    },
    setItemNotes(state, {index,val}) {
      state.entry.items[index].notes = val
    },
    setItemApprovedQty(state, {index, val}) {
      state.entry.items[index].approved_qty = val
    },
    setChecked(state, value) {
      state.entry.isClosed = !state.entry.isClosed
      console.log(state.entry.isClosed)
    },
    addItem(state) {
      state.entry.items.push({
        item_id : null,
        required_date : '',
        required_date_front_end : '',
        qty : '',
        satuan: '',
        approved_qty: '',
        approved_date: '',
        approved_date_front_end: '',
        notes: '',
      });
    },
    deleteItem(state, index) {
      state.entry.items.splice(index, 1);
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