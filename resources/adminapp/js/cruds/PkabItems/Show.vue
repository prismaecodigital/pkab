<template>
  <div class="container-fluid">
    <confirm-dialog
      :visible="dialog.visible"
      :title="dialog.title"
      :text="dialog.text"
      :input="dialog.input"
      :placeholder="dialog.placeholder"
      :confirm-text="dialog.confirmText"
      :cancel-text="dialog.cancelText"
      :extra-text="dialog.extraText"
      :danger="dialog.danger"
      :error="dialog.error"
      @confirm="onDialogConfirm"
      @cancel="closeDialog"
    />
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
                          {{ $t('cruds.pkabItem.fields.bu') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="bu.name">
                          </datatable-single>
                        </td>
                      </tr>
                      <tr v-if="entry.site_id">
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.site') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="site.name">
                          </datatable-single>
                        </td>
                      </tr>
                      <tr>
                        <td class="text-primary">
                          {{ $t('cruds.pkabItem.fields.dept') }}
                        </td>
                        <td>
                          <datatable-single :row="entry" field="dept.name">
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
                <th>Name</th>
                <th>Merk</th>
                <th>Spesifikasi</th>
                <th>Qty</th>
                <th>Satuan</th>
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
                  <td>
                      <input v-if="$can(entry.status) && $can('pkab_item_edit')" class="form-control wrapText" type="text" :value="item.satuan" @input="updateItemSatuan(k, $event)"/>
                      <input disabled v-else class="form-control wrapText" type="text" :value="item.satuan"/>
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
      dialog: {
        visible: false,
        mode: 'approve',
        title: '',
        text: '',
        input: false,
        placeholder: '',
        confirmText: 'Yes',
        cancelText: 'Cancel',
        extraText: '',
        danger: false,
        error: '',
        id: null
      },
      mergedFields: {
        'Kode' : 'kode',
        'Tgl dibuat' : 'Tanggal dibuat',
        'Tgl dibutuhkan' : 'Tanggal dibutuhkan',
        'User' : 'User',
        'BU' : 'BU',
        'Dept' : 'Dept',
        'Site' : 'Site',
        'Status' : 'status',
        'name': 'name',
        'merk': 'merk',
        'spesifikasi': 'spesifikasi',
        'qty' : 'qty',
        'satuan' : 'satuan'
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
    },
    entry: {
      handler() {
          this.updateAllItemSpecHeights()
      }
    }
  },
  methods: {
    ...mapActions('PkabItemsSingle', ['fetchShowData', 'loading', 'resetState', 'approveData', 'updateData', 'setItemName', 'updateMergedData',
      'rejectData',
      'setItemMerk',
      'setItemSpesifikasi',
      'setItemQty',
      'setItemSatuan',
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
    updateAllItemSpecHeights() {
      console.log('ok')
      this.$nextTick(() => {
        // Assuming 'entry' represents your data model containing items
        const rows = this.$el.querySelectorAll('table[name="inputItem"] tbody tr');
        rows.forEach((row, index) => {
          const textarea = row.querySelector('textarea');
          if (textarea) {
                textarea.style.height = 'auto'; // Reset height
                textarea.style.cssText = `height: ${textarea.scrollHeight}px !important;`;
                this.entry.items[index].spesifikasiHeight = textarea.style.cssText
          }
        });

      });
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
    approveData(id) {
      this.dialog = {
        visible: true,
        mode: 'approve',
        title: 'Approve?',
        text: 'Are you sure you want to approve this item?',
        input: false,
        placeholder: '',
        confirmText: 'Approve',
        cancelText: 'Cancel',
        extraText: '',
        danger: false,
        error: '',
        id
      }
    },
    rejectData(id) {
      this.dialog = {
        visible: true,
        mode: 'reject',
        title: 'Reject?',
        text: 'Masukkan Alasan',
        input: true,
        placeholder: 'Alasan penolakan',
        confirmText: 'Reject',
        cancelText: 'Cancel',
        extraText: '',
        danger: true,
        error: '',
        id
      }
    },
    closeDialog() {
      this.dialog = { ...this.dialog, visible: false }
    },
    errorMessage(error) {
      return (error && error.response && error.response.data && error.response.data.message)
        || (error && error.message)
        || 'Request failed'
    },
    onDialogConfirm(value) {
      if (this.dialog.mode === 'reject') {
        if (!value) {
          this.dialog = { ...this.dialog, error: 'Masukkan Alasan' }
          return
        }
        this.$store
          .dispatch(this.xprops.module + '/rejectData', { id: this.dialog.id.id, ket: value })
          .then(() => {
            this.closeDialog()
            this.fetchShowData(this.$route.params.id)
            this.$eventHub.$emit('reject-success')
          })
          .catch(error => {
            this.dialog = { ...this.dialog, error: this.errorMessage(error) }
          })
        return
      }

      this.$store
        .dispatch(this.xprops.module + '/updateData', this.dialog.id)
        .then(() => {
          this.closeDialog()
          this.fetchShowData(this.$route.params.id)
          this.$eventHub.$emit('approve-success')
        })
        .catch(error => {
          this.dialog = { ...this.dialog, error: this.errorMessage(error) }
        })
    },
  }
}
</script>