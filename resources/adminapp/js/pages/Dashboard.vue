<template>
  <div class="container-fluid">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-success card-header-icon">
              <div class="card-icon">
                <i class="material-icons">language</i>
              </div>
              <h4 class="card-title">
                {{ $t('global.dashboard') }}
              </h4>
            </div>
            <div class="card-body">
              {{ $t('global.youAreLoggedIn') }}
            </div>
            <div class="card-body">
              
            </div>
            <div>
                <b-modal id="formSurvey" centered hide-backdrop ok-only no-close-on-backdrop no-close-on-esc hide-header-close>
                  <template #modal-title>
                    Survey
                  </template>
                  <div>
                    Anda belum mengisi survey. Silahkan isi survey terlebih dahulu. Hanya 1-2 menit, santai saja
                  </div>
                  <template #modal-footer>
                    <b-button size="sm" variant="outline-secondary" @click="goToSurvey">
                            Klik di sini
                    </b-button>
                    <!-- <a class="btn btn-sm btn-success" :href="'https://google.com'" target="_blank">Klik disini</a> -->
                  </template>
                </b-modal>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
export default {
  data() {
    return {}
  },
  computed: {
    ...mapGetters('Survey', ['survey']),
  },
  watch: {
    survey() {
      if(!this.survey.has_completed_survey) {
        this.$bvModal.show('formSurvey')
      }
    }
  },
  methods: {
    ...mapActions('Survey', ['updateUserSurvey']),
    goToSurvey() {
      window.open(this.survey.link, '_blank');
      this.$bvModal.hide('formSurvey');
      this.updateUserSurvey();
      console.log('ok')
    }
  }
}
</script>