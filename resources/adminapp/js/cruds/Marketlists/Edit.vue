<template>
  <div class="container-fluid">
    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">edit</i>
              </div>
              <h4 class="card-title">
                {{ $t('global.edit') }}
                <strong>{{ $t('cruds.pkabItem.title_singular') }}</strong>
              </h4>
            </div>
            <div class="card-body">
              <back-button></back-button>
            </div>
            <div class="card-body">
              <bootstrap-alert />
              <div class="row">
                <div class="col-md-12">
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.req_date,
                      'is-focused': activeField == 'req_date'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.pkabItem.fields.req_date')
                    }}</label>
                    <input
                      class="form-control"
                      type="text"
                      :value="entry.req_date"
                      @input="updateReqDate"
                      @focus="focusField('req_date')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'has-items': entry.site_id !== null,
                      'is-focused': activeField == 'site'
                    }"
                  >
                    <label class="bmd-label-floating">{{
                      $t('cruds.pkabItem.fields.site')
                    }}</label>
                    <v-select
                      name="site"
                      label="name"
                      :key="'site-field'"
                      :value="entry.site_id"
                      :options="lists.site"
                      :reduce="entry => entry.id"
                      @input="updateSite"
                      @search.focus="focusField('site')"
                      @search.blur="clearFocus"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <vue-button-spinner
                class="btn-primary"
                :status="status"
                :isLoading="loading"
                :disabled="loading"
              >
                {{ $t('global.save') }}
              </vue-button-spinner>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'

export default {
  data() {
    return {
      status: '',
      activeField: ''
    }
  },
  computed: {
    ...mapGetters('PkabItemsSingle', ['entry', 'loading', 'lists'])
  },
  beforeDestroy() {
    this.resetState()
  },
  watch: {
    '$route.params.id': {
      immediate: true,
      handler() {
        this.resetState()
        this.fetchEditData(this.$route.params.id)
      }
    }
  },
  methods: {
    ...mapActions('PkabItemsSingle', [
      'fetchEditData',
      'updateData',
      'resetState',
      'setReqDate',
      'setSite'
    ]),
    updateReqDate(e) {
      this.setReqDate(e.target.value)
    },
    updateSite(value) {
      this.setSite(value)
    },
    submitForm() {
      this.updateData()
        .then(() => {
          this.$router.push({ name: 'marketlists.index' })
          this.$eventHub.$emit('update-success')
        })
        .catch(error => {
          this.status = 'failed'
          _.delay(() => {
            this.status = ''
          }, 3000)
        })
    },
    focusField(name) {
      this.activeField = name
    },
    clearFocus() {
      this.activeField = ''
    }
  }
}
</script>