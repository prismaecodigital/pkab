<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary card-header-icon">
            <div class="card-icon">
              <i class="material-icons">remove_red_eye</i>
            </div>
            <h4 class="card-title">
              {{ $t('global.view') }} Detail
              <strong>{{ $t('cruds.marketlist.title_singular') }}</strong>
            </h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-3">
                <back-button></back-button>
              </div>
            </div>
            
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="table-responsive">
                  <div class="table">
                    <tbody>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.code') }}
                        </td>
                        <td>
                          {{ entry.code }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.event') }}
                        </td>
                        <td>
                          {{ entry.event }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.created_at') }}
                        </td>
                        <td>
                          {{ entry.created_at }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.user') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="user.name">
                          </datatable-single>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.status') }}
                        </td>
                        <td>
                          <datatable-enum :row="entry" field="status">
                          </datatable-enum>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.bu') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="bu.name">
                          </datatable-single>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.marketlist.fields.site') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="site.name">
                          </datatable-single>
                        </td>
                      </tr>
                    </tbody>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body">
            <bootstrap-alert />
            <table class="table table-bordered" name="inputItem">
              <thead>
                  <th>Item</th>
                  <th>Tanggal dibutuhkan</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Notes</th>
                  <th v-if="entry.status === 'user_acc' || entry.status === 'selesai'">Qty yg diterima</th>
                  <th v-if="entry.status === 'user_acc' || entry.status === 'selesai'">Tanggal diterima</th>
              </thead>
              <tbody>
                <tr v-for="(item, k) in entry.items" :key="k">
                  <td style="background: #dadada">
                      <input disabled class="form-control wrapText" :value="item.item.name"/>
                  </td>
                  <td style="background: #dadada">
                      <input disabled class="form-control wrapText" :value="item.required_date"/>
                  </td>
                  <td style="background: #dadada">
                      <input v-if="$can(entry.status) && $can('marketlist_edit')" class="form-control wrapText" type="number" :value="item.qty" @input="updateItemQty(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="number" :value="item.qty"/>
                  </td>
                  <td style="background: #dadada">
                      <input v-if="$can(entry.status) && $can('marketlist_edit')" class="form-control wrapText" type="text" :value="item.satuan" @input="updateItemSatuan(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="text" :value="item.satuan"/>
                  </td>
                  <td style="background: #dadada">
                      <input v-if="$can(entry.status) && $can('marketlist_edit')" class="form-control wrapText" type="text" :value="item.notes" @input="updateItemNotes(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="text" :value="item.notes"/>
                  </td>
                  <td v-if="entry.status === 'user_acc' || entry.status === 'selesai'">
                      <input class="form-control wrapText" type="number" :value="item.approved_qty" @input="updateItemApprovedQty(k, $event)"/>
                      <!-- <input disabled class="form-control wrapText" type="number" :value="item.approved_qty" @input="updateItemApprovedQty(k, $event)"/> -->
                  </td>
                  <td v-if="entry.status === 'user_acc' || entry.status === 'selesai'">
                    <!-- <input v-if="item.approved_date === null" class="form-control wrapText" type="date" :value="item.approved_date_front_end" @input="updateItemApprovedDate(k, $event)"/>
                    <input v-else class="form-control wrapText" type="text" :value="item.approved_date" @input="updateItemApprovedDate(k, $event)"/> -->
                    <vuejs-datepicker
                      v-if="item.approved_date === null"
                      input-class="form-control"
                      format="dd-MM-yyyy"
                      :value="item.approved_date_front_end"
                      @input="updateItemApprovedDate(k, $event)"
                    >
                    </vuejs-datepicker>
                     <input v-else class="form-control wrapText" type="text" :value="item.approved_date" @input="updateItemApprovedDate(k, $event)"/>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="$can(entry.status)" class="card-body">
            <div v-if="entry.status === 'user_acc'" class="row">
              <div class="col-lg-3">
                <label>
                  <input type="checkbox" :value="entry.isClosed" @input="updateChecked" />
                  Close Order
                </label>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3">
                <button type='button' class="btn btn-info" @click.prevent="approveData(entry)">
                  Approve
                </button>
              </div>
              <div class="col-lg-3">
                <button type='button' class="btn btn-danger" @click.prevent="rejectData(entry)">
                  Reject
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
input:disabled, textarea:disabled {
cursor: auto;
}
</style>

<script>
import { mapGetters, mapActions } from 'vuex'
import DatatableSingle from '@components/Datatables/DatatableSingle'
import DatatableEnum from '@components/Datatables/DatatableEnum'
import "vue-cute-timeline/dist/index.css";

export default {
  components: {
    DatatableSingle,
    DatatableEnum,
  },
  data() {
    return {
      xprops: {
        module: 'MarketlistsSingle',
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('MarketlistsSingle', ['entry', 'lists']),
  },
  watch: {
    '$route.params.id': {
      immediate: true,
      handler() {
        this.resetState()
        this.fetchShowData(this.$route.params.id)
      }
    }
  },
  methods: {
    ...mapActions('MarketlistsSingle', ['fetchShowData', 'loading', 'resetState', 'approveData', 'updateData', 'setItemId', 'updateMergedData',
      'rejectData',
      'setItemRequiredDate',
      'setItemNotes',
      'setItemQty',
      'setItemSatuan',
      'setItemApprovedQty',
      'setItemApprovedDate',
      'setChecked'
      ]),
    updateItemId(index, value) {
      this.setItemId({index, value});
    },
    updateItemRequiredDate(index, value) {
      console.log(value)
      //let val = moment(date).format('DD-MM-YYYY')
      this.setItemRequiredDate({index, value})
    },
    updateItemQty(index, event, val) {
      val = event.target.value
      this.setItemQty({index, val})
      // this.entry.items[index].qty = event.target.value;
    },
    updateItemSatuan(index, event, val) {
      val = event.target.value
      this.setItemSatuan({index, val})
    },
    updateItemApprovedQty(index, event, val) {
      val = event.target.value
      this.setItemApprovedQty({index, val})
    },
    updateItemApprovedDate(index, val) {
      this.setItemApprovedDate({index, val})
    },
    updateChecked(event) {
      const value = event.target.checked
      // console.log(value)
      this.entry.isClosed = value
      // console.log(this.entry.isClosed)
    },
    approveData(id) {
      this.$swal({
        title: 'Approve?',
        text: 'Are you sure you want to approve this item?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then(result => {
        if(result.value) {
          let value = result.value
          this.$store
            .dispatch(this.xprops.module + '/updateData', id)
            .then(result => {

              //redirect logic
              this.fetchShowData(this.$route.params.id)
              this.$eventHub.$emit('approve-success')
            })
        }
      })
    },
    rejectData(id) {
      this.$swal({
        title: 'Reject?',
        text: 'Masukkan Alasan',
        input: 'text',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
      }).then(result => {
        if(result.value) {
          this.$store
            .dispatch(this.xprops.module + '/rejectData', {'id': id.id, 'ket': result.value})
            .then(result => {
              
              //redirect logic
              this.fetchShowData(this.$route.params.id)
              this.$eventHub.$emit('reject-success')
            })
        }
        else {
            this.$swal({
            icon: 'error',
            title: 'Failed',
            text: 'Masukkan Alasan'
            })
        }
      })
    },
  }
}
</script>