<template>
  <div class="dt-action-container">
    <router-link
      v-if="$can(xprops.permission_prefix + 'show')"
      :to="{ name: xprops.route + '.show', params: { id: row.id } }"
      class="btn btn-lg btn-round btn-info"
    >
    Detail
    </router-link>

    <a
      href="#"
      class="btn btn-lg btn-round btn-success"
      v-if="$can(row.status)"
      @click.prevent="openApprove(row.id)"
      type="button"
    >
    Approve
    </a>

    <a
      href="#"
      class="btn btn-lg btn-round btn-danger"
      v-if="$can(xprops.permission_prefix + 'delete')"
      @click.prevent="destroyData(row.id)"
      type="button"
    >
    Hapus
    </a>

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
      @confirm="onConfirm"
      @extra="openReject"
      @cancel="closeDialog"
    />
  </div>
</template>

<style scoped>
.btn {
  padding: 5px 10px !important;
}
</style>

<script>
export default {
  props: ['row', 'xprops'],
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
      }
    }
  },
  methods: {
    openApprove(id) {
      this.dialog = {
        visible: true,
        mode: 'approve',
        title: 'Approve?',
        text: 'Approve this PKAB?',
        input: false,
        placeholder: '',
        confirmText: 'Approve',
        cancelText: 'Cancel',
        extraText: 'Reject',
        danger: false,
        error: '',
        id
      }
    },
    openReject() {
      this.dialog = {
        ...this.dialog,
        mode: 'reject',
        title: 'Reject?',
        text: 'Masukkan Alasan',
        input: true,
        placeholder: 'Alasan penolakan',
        confirmText: 'Reject',
        extraText: '',
        danger: true,
        error: ''
      }
    },
    closeDialog() {
      this.dialog = { ...this.dialog, visible: false }
    },
    onConfirm(value) {
      if (this.dialog.mode === 'reject') {
        if (!value) {
          this.dialog = { ...this.dialog, error: 'Masukkan Alasan' }
          return
        }
        this.$store
          .dispatch(this.xprops.module + '/rejectData', { id: this.dialog.id, ket: value })
          .then(() => {
            this.closeDialog()
            this.$eventHub.$emit('reject-success')
          })
          .catch(error => {
            this.dialog = { ...this.dialog, error: this.errorMessage(error) }
          })
        return
      }

      this.$store
        .dispatch(this.xprops.module + '/approveData', this.dialog.id)
        .then(() => {
          this.closeDialog()
          this.$eventHub.$emit('approve-success')
        })
        .catch(error => {
          this.dialog = { ...this.dialog, error: this.errorMessage(error) }
        })
    },
    errorMessage(error) {
      return (error && error.response && error.response.data && error.response.data.message)
        || (error && error.message)
        || 'Request failed'
    },
    destroyData(id) {
      this.$swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        confirmButtonColor: '#dd4b39',
        focusCancel: true,
        reverseButtons: true
      }).then(result => {
        if (result.value) {
          this.$store
            .dispatch(this.xprops.module + '/destroyData', id)
            .then(result => {
              this.$eventHub.$emit('delete-success')
            })
        }
      })
    }
  }
}
</script>
