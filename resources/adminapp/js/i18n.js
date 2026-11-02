import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

export default new VueI18n({
  locale: import.meta.env.VITE_VUE_APP_I18N_LOCALE || 'id',
  fallbackLocale: import.meta.env.VITE_VUE_APP_I18N_FALLBACK_LOCALE || 'id',
  silentTranslationWarn: true
})