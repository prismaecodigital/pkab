function getDefaultState() {
    return {
      survey: [],
    }
  }
  
  const getters = {
    survey: state => state.survey
  }
  
  const actions = {
    fetchSurvey({ commit }) {
      axios.get('user-survey').then(response => {
        commit('setUserSurvey', response.data.survey)
      })
    },
    updateUserSurvey() {
        axios.get('update-user-survey').then(response => {
          console.log('updated')
        })
      },
  }
  
  const mutations = {
    setUserSurvey(state, value) {
      state.survey = value
    }
  }
  
  export default {
    namespaced: true,
    state: getDefaultState,
    getters,
    actions,
    mutations
  }