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
                <strong>{{ $t('cruds.user.title_singular') }}</strong>
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
                      'is-filled': entry.name,
                      'is-focused': activeField == 'name'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.user.fields.name')
                    }}</label>
                    <input
                      class="form-control"
                      type="text"
                      :value="entry.name"
                      @input="updateName"
                      @focus="focusField('name')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.username,
                      'is-focused': activeField == 'username'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.user.fields.username')
                    }}</label>
                    <input
                      class="form-control"
                      type="text"
                      :value="entry.username"
                      @input="updateUsername"
                      @focus="focusField('username')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.email,
                      'is-focused': activeField == 'email'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.user.fields.email')
                    }}</label>
                    <input
                      class="form-control"
                      type="text"
                      :value="entry.email"
                      @input="updateEmail"
                      @focus="focusField('email')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.password,
                      'is-focused': activeField == 'password'
                    }"
                  >
                    <label class="bmd-label-floating">{{
                      $t('cruds.user.fields.password')
                    }}</label>
                    <input
                      class="form-control"
                      type="password"
                      :value="entry.password"
                      @input="updatePassword"
                      @focus="focusField('password')"
                      @blur="clearFocus"
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.roles.length !== 0,
                      'is-focused': activeField == 'roles'
                    }"
                  >
                    <label class="required">{{
                      $t('cruds.user.fields.roles')
                    }}</label>
                    <v-select
                      name="roles"
                      label="title"
                      :key="'roles-field'"
                      :value="entry.roles"
                      :options="lists.roles"
                      :closeOnSelect="false"
                      multiple
                      @input="updateRoles"                      
                    />
                  </div>
                                    <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.dept.length !== 0,
                      'is-focused': activeField == 'bu'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.user.fields.bu')
                    }}</label>
                    <v-select
                      name="bu"
                      label="name"
                      :key="'bu-field'"
                      :value="entry.bu"
                      :options="lists.bu"
                      :closeOnSelect="false"
                      multiple
                      @input="updateBu"
                      @change="updateBu"
                    />
                    <span class="select-all badge" @click="selectAllBu">Pilih Semua</span>
                    <span class="select-all badge" @click="deselectAllBu">Batalkan pilihan</span>
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.dept.length !== 0,
                      'is-focused': activeField == 'dept'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.user.fields.dept')
                    }}</label>
                    <v-select
                      name="dept"
                      label="name"
                      :key="'dept-field'"
                      :value="entry.dept"
                      :options="lists.dept"
                      :closeOnSelect="false"
                      multiple
                      @input="updateDept"                      
                    />
                    <span class="select-all badge" @click="selectAllDept">Pilih Semua</span>
                    <span class="select-all badge" @click="deselectAllDept">Batalkan pilihan</span>
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
      activeField: '',
      depts: []
    }
  },
  computed: {
    ...mapGetters('UsersSingle', ['entry', 'loading', 'lists'])
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
    ...mapActions('UsersSingle', [
      'fetchEditData',
      'updateData',
      'resetState',
      'setName',
      'setUsername',
      'setEmail',
      'setPassword',
      'setRoles', 'setBu', 'setDept', 'setListDepts','setUsername'
    ]),
    selectAllBu() {
      this.setBu('all');
    },
    deselectAllBu() {
      this.setBu([]);
      this.setDept([])
      this.setListDepts([])
    },
    selectAllDept() {
      this.setDept('all')
    },
    deselectAllDept() {
      this.setDept([])
    },
    updateName(e) {
      this.setName(e.target.value)
    },
    updateUsername(e) {
      this.setUsername(e.target.value)
    },
    updateEmail(e) {
      this.setEmail(e.target.value)
    },
    updatePassword(e) {
      this.setPassword(e.target.value)
    },
    updateRoles(value) {
      this.setRoles(value)
    },
    submitForm() {
      this.updateData()
        .then(() => {
          this.$router.push({ name: 'users.index' })
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
    },
    updateBu(value) {
      this.setDept([])
      this.setBu(value)
    },
    updateDept(value) {
      this.setDept(value)
    },
  }
}
</script>