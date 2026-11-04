<template>
  <div v-if="visible" class="pkab-dialog-overlay" @click.self="onCancel">
    <div class="pkab-dialog" role="dialog" aria-modal="true">
      <h4 class="pkab-dialog-title">{{ title }}</h4>
      <p v-if="text" class="pkab-dialog-text">{{ text }}</p>
      <input
        v-if="input"
        ref="input"
        v-model="value"
        class="pkab-dialog-input"
        :placeholder="placeholder"
        @keyup.enter="onConfirm"
      />
      <p v-if="error" class="pkab-dialog-error">{{ error }}</p>
      <div class="pkab-dialog-actions">
        <button type="button" class="btn btn-default" @click="onCancel">{{ cancelText }}</button>
        <button v-if="extraText" type="button" class="btn btn-danger" @click="$emit('extra')">{{ extraText }}</button>
        <button type="button" class="btn" :class="danger ? 'btn-danger' : 'btn-info'" @click="onConfirm">
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConfirmDialog',
  props: {
    visible: { type: Boolean, default: false },
    title: { type: String, default: 'Are you sure?' },
    text: { type: String, default: '' },
    input: { type: Boolean, default: false },
    placeholder: { type: String, default: '' },
    confirmText: { type: String, default: 'Yes' },
    cancelText: { type: String, default: 'Cancel' },
    extraText: { type: String, default: '' },
    danger: { type: Boolean, default: false },
    error: { type: String, default: '' }
  },
  data () {
    return { value: '' }
  },
  watch: {
    visible (open) {
      if (open) {
        this.value = ''
        this.$nextTick(() => {
          if (this.$refs.input) this.$refs.input.focus()
        })
      }
    }
  },
  methods: {
    onConfirm () {
      if (this.input && !this.value.trim()) {
        this.$emit('confirm', '')
        return
      }
      this.$emit('confirm', this.value.trim())
    },
    onCancel () {
      this.$emit('cancel')
    }
  }
}
</script>

<style>
.pkab-dialog-overlay {
  position: fixed;
  inset: 0;
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.5);
}
.pkab-dialog {
  width: 90%;
  max-width: 420px;
  padding: 24px;
  border-radius: 8px;
  background: #fff;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35);
  text-align: left;
}
.pkab-dialog-title {
  margin: 0 0 8px;
  font-size: 20px;
  color: #333;
}
.pkab-dialog-text {
  margin: 0 0 16px;
  color: #555;
}
.pkab-dialog-input {
  width: 100%;
  margin-bottom: 16px;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.pkab-dialog-error {
  margin: 0 0 16px;
  color: #c0392b;
}
.pkab-dialog-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>
