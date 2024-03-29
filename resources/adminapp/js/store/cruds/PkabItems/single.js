function initialState() {
    return {
      entry: {
        id: null,
        code: '',
        req_date: '',
        req_date_front_end: '',
        user_id: null,
        status: 'leader_acc',
        dept_id: null,
        site_id: null,
        bu_id: null,
        ket: '',
        created_at: '',
        updated_at: '',
        deleted_at: '',
        items: [{
          name : '',
          merk : '',
          spesifikasi : '',
          qty : '',
          satuan: ''
        }]
      },
      lists: {
        user: [],
        status: [],
        bu: [],
        dept: [],
        satuan: []
      },
      loading: false,
      jsonData: [{
      }],
      mergedData: [],
      timelineData: []
    }
  }
  
  const route = 'pkab-items'
  
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
    setReqDate({ commit }, value) {
      commit('setReqDate', value)
    },
    setUser({ commit }, value) {
      commit('setUser', value)
    },
    setStatus({ commit }, value) {
      commit('setStatus', value)
    },
    setBu({ commit }, value) {
      commit('setBu', value)
    },
    setDept({ commit }, value) {
      commit('setDept', value)
    },
    setSite({ commit }, value) {
      commit('setSite', value)
    },
    setKet({ commit }, value) {
      commit('setKet', value)
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
    setItemName({ commit }, {index, val}) {
      commit('setItemName', {index, val})
    },
    setItemMerk({ commit }, {index, val}) {
      commit('setItemMerk', {index, val})
    },
    setItemSpesifikasi({ commit }, {index, val}) {
      commit('setItemSpesifikasi', {index, val})
    },
    setItemQty({ commit }, {index, val}) {
      commit('setItemQty', {index, val})
    },
    setItemSatuan({ commit }, {index, val}) {
      commit('setItemSatuan', {index, val})
    },
    addItem({commit}) {
      commit('addItem')
    },
    exportData({commit}) {
      commit('exportData')
    },
    deleteItem({commit}, index) {
      commit('deleteItem', index)
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
      })
    },
    resetState({ commit }) {
      commit('resetState')
    }
  }
  
  const mutations = {
    setEntry(state, entry) {
      state.jsonData = {
        'kode': entry.code,
        'Tanggal dibuat' : entry.created_at,
        'Tanggal dibutuhlan' : entry.req_date,
        'BU' : entry.bu.name,
        'Site' : entry.site ? entry.site.name : ' ',
        'Dept' : entry.dept.name,
        'User' : entry.user.name,
        '---' : '---',
        'Item' : '',
      },
      state.jsonData = Object.keys(state.jsonData).map(key => {
        return {
          'Field': key,
          'Value': state.jsonData[key]
        }
      }),
      state.entry = entry
      entry.items.forEach(function(val) {
        state.mergedData.push({
          'kode': entry.code,
          'Tanggal dibuat' : entry.created_at,
          'Tanggal dibutuhkan' : entry.req_date,
          'BU' : entry.bu.name,
          'Site' : entry.site ? entry.site.name : ' ',
          'Dept' : entry.dept.name,
          'User' : entry.user.name,
          'status' : entry.status_label,
          'name': val.name,
          'merk': val.merk,
          'spesifikasi': val.spesifikasi,
          'qty' : val.qty,
          'satuan' : val.satuan,
        })
      }),
      state.timelineData = [
        {
          'status_val'  : '',
          'status'      : 'Pengisian Form PKAB',
          'proses'      : 'selesai',
          'tanggal'     : '',
          'user'        : entry.user.name,
        },
        {
          'status_val'  : 'leader_acc',
          'status'      : 'Menunggu Persetujuan Leader',
          'proses'      : '',
          'tanggal'     : '',
          'user'        : 'user'
        },
        {
          'status_val'  : 'purchasing_acc_1',
          'status'      : 'Pengajuan FPBD',
          'proses'      : '',
          'tanggal'     : '',
          'user'        : 'user'
        },
        {
          'status_val'  : 'purchasing_acc_2',
          'status'      : 'Menunggu Dana',
          'proses'      : '',
          'tanggal'     : '',
          'user'        : 'user'
        },
        {
          'status_val'  : 'purchasing_acc_3',
          'status'      : 'Proses Pembelian',
          'proses'      : '',
          'tanggal'     : '',
          'user'        : 'user'
        },
        {
          'status_val'  : 'user_acc',
          'status'      : 'Menunggu konfirmasi penerimaan',
          'proses'      : '',
          'tanggal'     : '',
          'user'        : 'user'
        },
      ]
      if(entry.status != 'cancel') {
        entry.status_history.forEach(function(val, index) {
          if(val.status == state.timelineData[index+1].status_val) {
            state.timelineData[index].tanggal = moment(val.created_at).format('DD MMMM YYYY, HH:mm')
            state.timelineData[index].user = val.user.name
            state.timelineData[index].proses = 'selesai'
          }
          state.timelineData[entry.status_history.length].proses = 'proses'
        })
      }
      if(entry.status == 'cancel') {
        entry.status_history.forEach(function(val, index) {
          if(val.status == state.timelineData[index+1].status_val) {
            state.timelineData[index].tanggal = moment(val.created_at).format('DD MMMM YYYY, HH:mm')
            state.timelineData[index].user = val.user.name
            state.timelineData[index].proses = 'selesai'
          }
          state.timelineData[entry.status_history.length - 1].proses = 'cancel'
        })
      }
      
    },
    setReqDate(state, value) {
      state.entry.req_date = moment(value).format('DD-MM-YYYY')
      state.entry.req_date_front_end = value
    },
    setUser(state, value) {
      state.entry.user_id = value
    },
    setStatus(state, value) {
      state.entry.status = value
    },
    setBu(state, value) {
      state.entry.bu_id = value
    },
    setDept(state, value) {
      state.entry.dept_id = value
    },
    setSite(state, value) {
      state.entry.site_id = value
    },
    setBu(state, value) {
      state.entry.bu_id = value
    },
    setKet(state, value) {
      state.entry.ket = value
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
    setItemName(state, {index,val}) {
      state.entry.items[index].name = val
    },
    setItemMerk(state, {index,val}) {
      state.entry.items[index].merk = val
    },
    setItemSpesifikasi(state, {index,val}) {
      state.entry.items[index].spesifikasi = val
    },
    setItemQty(state, {index,val}) {
      state.entry.items[index].qty = val
    },
    setItemSatuan(state, {index,val}) {
      state.entry.items[index].satuan = val
    },
    addItem(state) {
      state.entry.items.push({
        name: '',
        merk: '',
        spesifikasi: '',
        qty: '',
        satuan: ''
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