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
                {{ $t('global.edit') }}
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
                      'is-filled': entry.site_id !== null,
                      'is-focused': activeField == 'site'
                    }"
                  >
                    <label class="">{{
                      $t('cruds.marketlist.fields.site')
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
            <div class="card-header card-header-primary card-header-icon">
              <h4 class="card-title">
                <strong>Item</strong>
                <br><br>
                Upload XLSX : <input type="file" @change="handleFileUpload" />
              </h4>
            </div>
            <br>
            <!-- <div class="card-body">
              <table>
                <thead>
                  <tr>
                    <th v-for="header in headers" :key="header">{{ header }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in excelData" :key="rowIndex">
                    <td v-for="(cell, colIndex) in row" :key="colIndex">{{ cell }}</td>
                  </tr>
                </tbody>
              </table>
            </div> -->
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
                        <i class="fa fa-trash-o" @click="deleteRow(k)"></i>
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
                      :value="item.required_date"
                      @input="updateItemRequiredDate(k, $event)"
                    >
                    </vuejs-datepicker>
                    </td>
                    <td>
                        <input class="form-control wrapText" type="number" step="0.1" :value="item.qty" @input="updateItemQty(k, $event)" required/>
                    </td>
                    <td>
                    <v-select
                      name="satuan"
                      label="name"
                      :key="'satuan-field'"
                      :value="item.satuan"
                      :options="lists.satuan"
                      @input="updateItemSatuan(k, $event)"
                      @search.focus="focusField('item')"
                      @search.blur="clearFocus"
                    />
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
import { read, utils } from 'xlsx';

export default {
  components: { BootstrapAlert },
  data() {
    return {
      status: '',
      activeField: '',
      sites: [],
      date: {
        disabledDates: {
          to: new Date(new Date() - (24 * 60 * 60 * 1000))
        }
      },
      headers: [], // To store table headers based on Excel columns
      excelData: [], // To store the data from the Excel fil
      rowIndex: null,
    }
  },
  computed: {
    ...mapGetters('MarketlistsSingle', ['entry', 'loading', 'lists']),
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
    ...mapActions('MarketlistsSingle', [
      'storeData',
      'resetState',
      'setEvent',
      'setBu',
      'setSite',
      'setItems',
      'setItemId',
      'setItemRequiredDate',
      'setItemNotes',
      'setItemQty',
      'setItemSatuan',
      'addItem',
      'deleteItem',
      'fetchEditData',
      'updateDataOnly'
    ]),
    updateEvent(e) {
      this.setEvent(e.target.value)
    },
    updateBu(value) {
      this.setSite([])
      this.setBu(value)
      if(value != null) {
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
        this.sites = []
      }
      
    },
    updateSite(value) {
      this.setSite(value)
    },
    submitForm() {
      this.updateDataOnly()
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
    updateItemSatuan(index, val) {
      this.setItemSatuan({index, val})
      // this.entry.items[index].qty = event.target.value;
    },
    updateItemRequiredDate2(index, value) {
      const [day, month, year] = value.split("-");
      const parsedDate = new Date(year, month - 1, day)
      console.log(parsedDate)
      this.setItemRequiredDate({index, value: parsedDate})
      // imported from excel
    },
    updateItemId2(index, value) {
      const item = this.lists.item.find((obj) => obj.name === value);
      console.log(item.id)
      this.setItemId({index, value: item.id})
      // imported from excel
    },
    updateItemNotes2(index, val) {
      this.setItemNotes({index, val})
      // imported from excel
    },
    updateItemQty2(index, val) {
      this.setItemQty({index, val})
      // imported from excel 
    },
    updateItemSatuan2(index, val) {
      this.setItemSatuan({index, val})
      // imported from excel 
    },
    addNewRow() {
        this.addItem()
    },
    deleteRow(index) {
      console.log(index)
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
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.readExcelFile(file);
      }
    },
    readExcelFile(file) {
      const reader = new FileReader();

      reader.onload = (event) => {
        const data = event.target.result;
        const workbook = read(data, { type: 'binary' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        const excelData = utils.sheet_to_json(worksheet, {raw: false });
        const rowExcel = excelData.filter(obj => obj.hasOwnProperty('Qty'));
        console.log(rowExcel)
        // Process the excelData and add new rows based on the 'qty' column
        for (let i = 0; i < rowExcel.length; i++) {
          const rowData = rowExcel[i];
          // if (rowData.qty !== null) {
          //   // Clone the 'rowData' object to prevent reactivity issues
          //   const newRow = { ...rowData };
          //   if(newRow.Qty !== undefined) {
          //     console.log(newRow)
          //     // Process import to UI
          //     this.addItem()
          //     this.updateItemId2(i, newRow.Item)
          //     this.updateItemRequiredDate2(i, newRow.Tanggal_dibutuhkan)
          //     this.updateItemQty2(i, newRow.Qty)
          //     this.updateItemSatuan2(i, newRow.Satuan)
          //     this.updateItemNotes2(i, newRow.Notes)
          //   }
          // }
          if(rowData.Qty !== undefined) {
              if(i !== 0) {
                this.addItem()
              }
              this.updateItemId2(i, rowData.Item)
              this.updateItemRequiredDate2(i, rowData.Tanggal_dibutuhkan)
              this.updateItemQty2(i, rowData.Qty)
              this.updateItemSatuan2(i, rowData.Satuan)
              this.updateItemNotes2(i, rowData.Notes)
          }
        }
      };

      reader.readAsBinaryString(file);
    },
    
  }
}
</script>