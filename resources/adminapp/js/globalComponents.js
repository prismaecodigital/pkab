import { abilitiesPlugin } from '@casl/vue'
import ability from './services/ability'
import Datatable from '@laraveldaily/vue2-datatable'
import VueSweetalert2 from 'vue-sweetalert2'
import MaterialSpinner from '@components/MaterialSpinner.vue'
import VueButtonSpinner from '@components/VueButtonSpinner.vue'
import BootstrapAlert from '@components/BootstrapAlert.vue'
import SideBar from '@components/SidebarPlugin'
import LanguagesPlugin from '@components/LanguagesPlugin'
import EventHub from '@components/EventHubPlugin'
import BackButton from '@components/BackButton.vue'
import DatetimePicker from '@components/DatetimePicker.vue'
import VueRadio from '@components/VueRadio.vue'
import vSelect from 'vue-select'
import Datepicker from 'vuejs-datepicker';
import excel from 'vue-excel-export'
import { Timeline, TimelineItem, TimelineTitle } from 'vue-cute-timeline'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import CustomTableBody from '@components/Datatables/CustomTableBody.vue'
import ConfirmDialog from '@components/ConfirmDialog.vue'

const GlobalComponents = {
  install(Vue) {
    Vue.use(abilitiesPlugin, ability)
    Vue.use(SideBar)
    Vue.use(LanguagesPlugin)
    Vue.use(EventHub)
    Vue.use(Datatable)
    Vue.use(VueSweetalert2)

    // sweetalert2 can fail to render (blocked CSS, browser extension, unsupported
    // browser) and then the promise never resolves, so the action dies silently.
    // Wrap it: if no visible dialog appears shortly after the call, fall back to
    // the native confirm/prompt so every $swal() call site keeps working.
    const swal = Vue.prototype.$swal
    Vue.prototype.$swal = function (options = {}) {
      const nativeResult = () => {
        if (options.input) {
          const value = window.prompt(options.text || options.title || '')
          return { isConfirmed: !!value, isDismissed: !value, value: value || undefined }
        }
        const ok = window.confirm(options.title || options.text || 'Are you sure?')
        return { isConfirmed: ok, isDismissed: !ok, dismiss: ok ? undefined : 'cancel', value: ok }
      }

      if (typeof swal !== 'function') {
        console.error('[swal] not available, using native dialog')
        return Promise.resolve(nativeResult())
      }

      let promise
      try {
        promise = swal(options)
      } catch (error) {
        console.error('[swal] threw, using native dialog', error)
        return Promise.resolve(nativeResult())
      }

      return new Promise((resolve) => {
        let settled = false
        const timer = setTimeout(() => {
          if (settled) return
          const popup = document.querySelector('.swal2-popup')
          let visible = false
          if (popup) {
            const rect = popup.getBoundingClientRect()
            const style = getComputedStyle(popup)
            const onScreen = rect.width > 0 && rect.height > 0 && rect.bottom > 0 && rect.top < window.innerHeight
            const opaque = style.visibility !== 'hidden' && style.opacity !== '0' && style.display !== 'none'
            // also make sure nothing (overlay/z-index) is covering the dialog
            const top = document.elementFromPoint(rect.left + rect.width / 2, rect.top + rect.height / 2)
            visible = onScreen && opaque && !!top && popup.contains(top)
          }
          if (!visible) {
            settled = true
            console.error('[swal] dialog did not render, using native dialog')
            try { swal.close() } catch (error) { /* ignore */ }
            resolve(nativeResult())
          }
        }, 300)

        Promise.resolve(promise).then((result) => {
          if (settled) return
          settled = true
          clearTimeout(timer)
          resolve(result)
        }).catch((error) => {
          if (settled) return
          settled = true
          clearTimeout(timer)
          console.error('[swal] rejected, using native dialog', error)
          resolve(nativeResult())
        })
      })
    }
    Vue.use(excel)
    Vue.use(BootstrapVue);
    Vue.use(IconsPlugin);

    Vue.component('timeline', Timeline)
    Vue.component('timeline-item', TimelineItem)
    Vue.component('timeline-title', TimelineTitle)
    Vue.component('vuejs-datepicker', Datepicker)
    Vue.component('datetime-picker', DatetimePicker)
    Vue.component('back-button', BackButton)
    Vue.component('material-spinner', MaterialSpinner)
    Vue.component('vue-button-spinner', VueButtonSpinner)
    Vue.component('bootstrap-alert', BootstrapAlert)
    Vue.component('v-radio', VueRadio)
    Vue.component('v-select', vSelect)
    Vue.component('custom-table-body', CustomTableBody)
    Vue.component('confirm-dialog', ConfirmDialog)
  }
}

export default GlobalComponents