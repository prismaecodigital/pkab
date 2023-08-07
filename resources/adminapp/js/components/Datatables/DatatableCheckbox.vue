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
  props: ['row'],
  methods: {
  handleCheckboxChange(event) {
    const isChecked = event.target.checked;
    console.log('Value prop:', this.row);
    console.log('Checkbox checked state:', isChecked);
    this.$emit('change', isChecked);

    // You can also emit an event if needed
    // this.$emit('input', isChecked);
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