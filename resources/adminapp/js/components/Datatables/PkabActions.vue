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
      @click.prevent="approveData(row.id)"
      type="button"
    >
    Approve
    </a>

    <a
      href="#"
      class="btn btn-lg btn-round btn-danger"
      v-if="$can(xprops.permission_prefix + 'delete' || row.status === 0)"
      @click.prevent="destroyData(row.id)"
      type="button"
    >
    Hapus
    </a>
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
      // Code...
    }
  },
  created() {
    // Code...
  },
  methods: {
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
    },
    approveData(id) {
      this.$swal({
        title: 'Approve?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        showCloseButton: true,
        cancelButtonText: 'Reject',
      }).then(result => {
        if(result.isConfirmed) {
          console.log(result);
          this.$store
            .dispatch(this.xprops.module + '/approveData', id)
            .then(result => {
              this.$eventHub.$emit('approve-success')
            })
        } else if(result.isDismissed && result.dismiss === 'cancel') {
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
                    .dispatch(this.xprops.module + '/rejectData', {'id': id, 'ket': result.value})
                    .then(result => {

                      //redirect logic
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
          // console.log(result)
          //   this.$store
          //   .dispatch(this.xprops.module + '/rejectData', {'id': id, 'ket': result.value})
          //   .then(result => {
          //     this.$eventHub.$emit('reject-success')
          //   })
        }
      })
    }
  }
}
</script>