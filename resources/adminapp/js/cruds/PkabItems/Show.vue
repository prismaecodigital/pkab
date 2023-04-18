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
              <strong>{{ $t('cruds.pkabItem.title_singular') }}</strong>
            </h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-3">
                <back-button></back-button>
              </div>
              <div class="col-lg-3">
                <export-excel v-if="$can('export')"
                  :fields   = "mergedFields"
                  :data   = "mergedData">
                  <button class="btn btn-success">Export Item</button>
                </export-excel>
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
                          {{ $t('cruds.pkabItem.fields.code') }}
                        </td>
                        <td>
                          {{ entry.code }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.created_at') }}
                        </td>
                        <td>
                          {{ entry.created_at }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.req_date') }}
                        </td>
                        <td>
                          {{ entry.req_date }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.user') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="user.name">
                          </datatable-single>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.status') }}
                        </td>
                        <td>
                          <datatable-enum :row="entry" field="status">
                          </datatable-enum>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.dept') }}
                        </td>
                        <td>
                          <bu-dept-single :row="entry" field="dept.bu">
                          </bu-dept-single>
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
                <th>Name</th>
                <th>Merk</th>
                <th>Spesifikasi</th>
                <th>Qty</th>
              </thead>
              <tbody>
                <tr v-for="(item, k) in entry.items" :key="k">
                  <td>
                      <input v-if="$can(entry.status) && $can('pkab_item_edit')" class="form-control wrapText" type="text" :value="item.name" @input="updateItemName(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="text" :value="item.name"/>
                  </td>
                  <td>
                      <input v-if="$can(entry.status) && $can('pkab_item_edit')" class="form-control wrapText" type="text" :value="item.merk" @input="updateItemMerk(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="text" :value="item.merk"/>
                  </td>
                  <td :style="{ height: item.spesifikasiHeight }">
                      <textarea v-if="$can(entry.status) && $can('pkab_item_edit')" class="form-control wrapText" :value="item.spesifikasi" @input="updateItemSpec(k, $event, $event.target, item)"/>
                      <textarea disabled v-else class="form-control wrapText" :value="item.spesifikasi"/>
                  </td>
                  <td>
                      <input v-if="$can(entry.status) && $can('pkab_item_edit')" class="form-control wrapText" type="number" :value="item.qty" @input="updateItemQty(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="number" :value="item.qty"/>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="card-body">
            <h4><strong>Alur Pengajuan PKAB</strong></h4>
              <timeline v-for="(item, index) in timelineData" :key="index">
                <timeline-item v-if="item.proses == 'selesai'" bg-color="green">
                  {{ item.status }}
                  <p v-if="item.tanggal">Tanggal : {{ item.tanggal }}</p>
                  <p v-if="item.user">Diproses Oleh : {{ item.user }}</p>
                </timeline-item>
                <timeline-item v-if="item.proses == 'proses'" bg-color="yellow">
                  {{ item.status }}
                  <p v-if="item.tanggal">Tanggal : {{ item.tanggal }}</p>
                </timeline-item>
                <timeline-item v-if="item.proses == ''" bg-color="red">
                  {{ item.status }}
                </timeline-item>
              </timeline>
          </div>
          <div v-if="$can(entry.status)" class="card-body">
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
import BuDeptSingle from '@components/Datatables/BuDeptSingle'
import "vue-cute-timeline/dist/index.css";

export default {
  components: {
    DatatableSingle,
    DatatableEnum,
    BuDeptSingle
  },
  data() {
    return {
      mergedFields: {
        'Kode' : 'kode',
        'Tgl dibuat' : 'Tanggal dibuat',
        'Tgl dibutuhkan' : 'Tanggal dibutuhkan',
        'User' : 'User',
        'BU' : 'BU',
        'Dept/Site' : 'Dept',
        'Status' : 'status',
        'name': 'name',
        'merk': 'merk',
        'spesifikasi': 'spesifikasi',
        'qty' : 'qty'
      },
      xprops: {
        module: 'PkabItemsSingle',
      }
    }
  },
  beforeDestroy() {
    this.resetState()
  },
  computed: {
    ...mapGetters('PkabItemsSingle', ['entry', 'jsonData', 'mergedData', 'timelineData']),
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
    ...mapActions('PkabItemsSingle', ['fetchShowData', 'loading', 'resetState', 'approveData', 'updateData', 'setItemName', 'updateMergedData',
      'rejectData',
      'setItemMerk',
      'setItemSpesifikasi',
      'setItemQty',
      'setKet']),
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
          this.setKet(value)
          this.$store
            .dispatch(this.xprops.module + '/updateData', id)
            .then(result => {

              //redirect logic
              this.$router.push('/pkab/pkab-items');
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
          console.log(id.id)
          console.log(result.value)
          this.$store
            .dispatch(this.xprops.module + '/rejectData', {'id': id.id, 'ket': result.value})
            .then(result => {

              //redirect logic
              this.$router.push('/pkab/pkab-items');
            })
        }
      })
    },
  }
}
</script>