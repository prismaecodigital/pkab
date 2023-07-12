<template>
  <div class="container-fluid">
    <form @submit.prevent="submitForm">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header card-header-primary card-header-icon">
              <div class="card-icon">
                <i class="material-icons">add</i>
              </div>
              <h4 class="card-title">
                {{ $t('global.create') }}
                <strong>PKAB</strong>
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
                      'is-filled': entry.req_date,
                      'is-focused': activeField == 'req_date'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.pkabItem.fields.req_date')
                    }}</label>
                    <!-- <datetime-picker
                      class="form-control"
                      type="text"
                      picker="date"
                      :value="entry.req_date"
                      @input="updateReqDate"
                      @focus="focusField('req_date')"
                      @blur="clearFocus"
                    >
                    </datetime-picker> -->
                    <vuejs-datepicker
                      input-class="form-control"
                      format="dd-MM-yyyy"
                      :disabled-dates="date.disabledDates"
                      :value="entry.req_date_front_end"
                      @input="updateReqDate"
                      
                    >
                    </vuejs-datepicker>
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.bu_id !== null,
                      'is-focused': activeField == 'bu'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.pkabItem.fields.bu')
                    }}</label>
                    <v-select
                      name="bu"
                      label="name"
                      :key="'bu-field'"
                      :value="entry.bu_id"
                      :options="lists.bu"
                      :reduce="entry => entry.id"
                      @input="updateBu"
                      @search.focus="focusField('bu')"
                      @search.blur="clearFocus"
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.site_id !== null,
                      'is-focused': activeField == 'site'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.pkabItem.fields.site')
                    }}</label>
                    <v-select
                      name="site"
                      label="name"
                      :key="'site-field'"
                      :value="entry.site_id"
                      :options="sites"
                      :reduce="entry => entry.id"
                      @input="updateSite"
                      @search.focus="focusField('site')"
                      @search.blur="clearFocus"
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.dept_id !== null,
                      'is-focused': activeField == 'dept'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.pkabItem.fields.dept')
                    }}</label>
                    <v-select
                      name="dept"
                      label="name"
                      :key="'dept-field'"
                      :value="entry.dept_id"
                      :options="depts"
                      :reduce="entry => entry.id"
                      @input="updateDept"
                      @search.focus="focusField('dept')"
                      @search.blur="clearFocus"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div class="card-header card-header-primary card-header-icon">
              <h4 class="card-title">
                <strong>Item</strong>
              </h4>
            </div>
            <br>
            <div class="card-body">
              <bootstrap-alert />
              <table class="table table-bordered" name="inputItem">
                <thead>
                  <th></th>
                  <th>Name</th>
                  <th>Merk</th>
                  <th>Spesifikasi</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                </thead>
                <tbody>
                  <tr v-for="(item, k) in entry.items" :key="k">
                    <td scope="row" class="trashIconContainer">
                        <i class="fa fa-trash-o" @click="deleteRow(k, item)"></i>
                    </td>
                    <td>
                        <textarea class="form-control wrapText" type="text" :value="item.name" @input="updateItemName(k, $event)" required></textarea>
                    </td>
                    <td>
                        <textarea class="form-control wrapText" type="text" :value="item.merk" @input="updateItemMerk(k, $event)" required></textarea>
                    </td>
                    <td :style="{ height: item.spesifikasiHeight }">
                        <textarea class="form-control wrapText" :value="item.spesifikasi" @input="updateItemSpec(k, $event, $event.target, item)" required></textarea>
                    </td>
                    <td>
                        <input class="form-control wrapText" type="number" :value="item.qty" @input="updateItemQty(k, $event)" required/>
                    </td>
                    <td>
                        <input class="form-control wrapText" type="text" :value="item.satuan" @input="updateItemSatuan(k, $event)" required/>
                    </td>
                  </tr>
                </tbody>
              </table>
              <button type='button' class="btn btn-info" @click="addNewRow">
                  <i class="fa fa-plus-circle"></i>
                  Add Item
              </button>
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
<style scoped>
.trashIconContainer:hover {
  cursor: pointer;
  color: red;
}
</style>

<script>
import { mapGetters, mapActions } from 'vuex'
import BootstrapAlert from '../../components/BootstrapAlert.vue'

export default {
  components: { BootstrapAlert },
  data() {
    return {
      status: '',
      activeField: '',
      depts: [],
      sites: [],
      date: {
        disabledDates: {
          to: new Date(new Date() - (24 * 60 * 60 * 1000))
        }
      }
    }
  },
  computed: {
    ...mapGetters('PkabItemsSingle', ['entry', 'loading', 'lists']),
  },
  mounted() {
    this.fetchCreateData()
  },
  beforeDestroy() {
    this.resetState()
  },
  methods: {
    ...mapActions('PkabItemsSingle', [
      'storeData',
      'resetState',
      'setReqDate',
      'setBu',
      'setDept',
      'setItems',
      'setItemName',
      'setItemMerk',
      'setItemSpesifikasi',
      'setItemQty',
      'setItemSatuan',
      'setSite',
      'addItem',
      'deleteItem',
      'fetchCreateData'
    ]),
    updateReqDate(value) {
      //let value = moment(date).format('DD-MM-YYYY')
      this.setReqDate(value)
    },
    updateBu(value) {
      this.setDept([])
      this.setSite([])
      this.setBu(value)
      if(value != null) {
        axios.get('/budept', {
          params: {
              bu: value
          }
        })
        .then(response => {
            this.depts = response.data;
        })
        .catch(error => {
            console.log(error);
        });
        axios.get('/busite', {
          params: {
              bu: value
          }
        })
        .then(response => {
            this.sites = response.data;
        })
        .catch(error => {
            console.log(error);
        });
      }
      else {
        this.depts = []
        this.sites = []
      }
      
    },
    updateDept(value) {
      this.setDept(value)
    },
    updateSite(value) {
      this.setSite(value)
    },
    submitForm() {
      this.storeData()
        .then(() => {
          this.$router.push({ name: 'pkab_items.index' })
          this.$eventHub.$emit('create-success')
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
    updateItemName(index, event, val) {
      val = event.target.value
      this.setItemName({index, val});
    },
    updateItemMerk(index, event, val) {
      val = event.target.value
      this.setItemMerk({index, val})
      //this.entry.items[index].merk = event.target.value;
    },
    updateItemSpec(index, event, textarea, item, val) {
      textarea.style.cssText = `height: ${textarea.scrollHeight}px !important;`;
      item.spesifikasiHeight = `${textarea.scrollHeight}px`;
      val = event.target.value;
      this.setItemSpesifikasi({index, val})
    },
    updateItemQty(index, event, val) {
      val = event.target.value
      this.setItemQty({index, val})
      // this.entry.items[index].qty = event.target.value;
    },
    updateItemSatuan(index, event, val) {
      val = event.target.value
      this.setItemSatuan({index, val})
      // this.entry.items[index].qty = event.target.value;
    },

    addNewRow() {
        this.addItem()
    },
    deleteRow(index) {
        this.$swal({
        title: 'Hapus Item ini ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        showCloseButton: true,
      }).then(result => {
        if(result.isConfirmed) {
          this.deleteItem(index)
        }
      })
    },
    
  }
}
</script>