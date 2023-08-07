<template>
  <div class="form-check">
    <label class="form-check-label">
      <input
        class="form-check-input"
        type="checkbox"
        :checked="row.selected"
        @change="handleCheckboxChange"
      />
      <span class="form-check-sign">
        <span class="check"></span>
      </span>
    </label>
  </div>
</template>

<script>
export default {
  props: ['row','data'],
  methods: {
  handleCheckboxChange(event) {
    const isChecked = event.target.checked;
    this.row.selected = !this.row.selected;
    const checkedValues = this.data.filter(row => row.selected);
    this.$emit('change', isChecked); // Emit input event for v-model
    this.$emit('checked-values', checkedValues); // Emit checked-values event
  },
},
}
</script>

<style scoped>
.check {
  opacity: 1 !important;
}

.form-check .form-check-label {
  cursor: default !important;
}
</style>