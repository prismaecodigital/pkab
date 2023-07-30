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
                <strong>MARKETLIST</strong>
              </h4>
            </div>
            <div class="card-body">
              <back-button></back-button>
            </div>
            <div class="card-body">
              <bootstrap-alert />
              <div class="row">
                <div class="col-md-12">
                  <!-- <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.req_date,
                      'is-focused': activeField == 'req_date'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.marketlist.fields.req_date')
                    }}</label>
                    <vuejs-datepicker
                      input-class="form-control"
                      format="dd-MM-yyyy"
                      :disabled-dates="date.disabledDates"
                      :value="entry.req_date_front_end"
                      @input="updateReqDate"
                      
                    >
                    </vuejs-datepicker>
                  </div> -->
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.event,
                      'is-focused': activeField == 'event'
                    }"
                  >
                    <label class="bmd-label-floating required">{{
                      $t('cruds.marketlist.fields.event')
                    }}</label>
                    <input
                      class="form-control"
                      type="text"
                      :value="entry.event"
                      @input="updateEvent"
                      @focus="focusField('event')"
                      @blur="clearFocus"
                      required
                    />
                  </div>
                  <div
                    class="form-group bmd-form-group"
                    :class="{
                      'is-filled': entry.bu_id !== null,
                      'is-focused': activeField == 'bu'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.marketlist.fields.bu')
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
                      'is-filled': entry.dept_id !== null,
                      'is-focused': activeField == 'dept'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.marketlist.fields.dept')
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
                  <th>Item</th>
                  <th>Tanggal dibutuhkan</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Notes</th>
                </thead>
                <tbody>
                  <tr v-for="(item, k) in entry.items" :key="k">
                    <td scope="row" class="trashIconContainer">
                        <i class="fa fa-trash-o" @click="deleteRow(k, item)"></i>
                    </td>
                    <td>
                    <v-select
                      name="item"
                      label="name"
                      :key="'item-field'"
                      :value="item.item_id"
                      :options="lists.item"
                      :reduce="item => item.id"
                      @input="updateItemId(k, $event)"
                      @search.focus="focusField('item')"
                      @search.blur="clearFocus"
                    />
                    </td>
                    <td>
                    <vuejs-datepicker
                      input-class="form-control"
                      format="dd-MM-yyyy"
                      :disabled-dates="date.disabledDates"
                      :value="item.required_date_front_end"
                      @input="updateItemRequiredDate(k, $event)"
                    >
                    </vuejs-datepicker>
                    </td>
                    <td>
                        <input class="form-control wrapText" type="number" :value="item.qty" @input="updateItemQty(k, $event)" required/>
                    </td>
                    <td>
                        <input class="form-control wrapText" type="text" :value="item.satuan" @input="updateItemSatuan(k, $event)" required/>
                    </td>
                    <td>
                        <input class="form-control wrapText" type="text" :value="item.notes" @input="updateItemNotes(k, $event)"/>
                    </td>
                  </tr>
                </tbody>
              </table>
              <button type='button' class="btn btn-info" @click="addNewRow">
                  <i class="fa fa-plus-circle"></i>
                  Tambah Item
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
      date: {
        disabledDates: {
          to: new Date(new Date() - (24 * 60 * 60 * 1000))
        }
      }
    }
  },
  computed: {
    ...mapGetters('MarketlistDonesSingle', ['entry', 'loading', 'lists']),
  },
  mounted() {
    this.fetchCreateData()
  },
  beforeDestroy() {
    this.resetState()
  },
  methods: {
    ...mapActions('MarketlistDonesSingle', [
      'storeData',
      'resetState',
      'setEvent',
      'setBu',
      'setDept',
      'setItems',
      'setItemId',
      'setItemRequiredDate',
      'setItemNotes',
      'setItemQty',
      'setItemSatuan',
      'addItem',
      'deleteItem',
      'fetchCreateData'
    ]),
    updateEvent(e) {
      this.setEvent(e.target.value)
    },

    updateBu(value) {
      this.setDept([])
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
      }
      else {
        this.depts = []
      }
      
    },
    updateDept(value) {
      this.setDept(value)
    },
    submitForm() {
      this.storeData()
        .then(() => {
          this.$router.push({ name: 'marketlists.index' })
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
    updateItemId(index, value) {
      this.setItemId({index, value});
    },
    updateItemRequiredDate(index, value) {
      console.log(value)
      //let val = moment(date).format('DD-MM-YYYY')
      this.setItemRequiredDate({index, value})
    },
    updateItemNotes(index, event, val) {
      val = event.target.value
      this.setItemNotes({index, val})
      //this.entry.items[index].merk = event.target.value;
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