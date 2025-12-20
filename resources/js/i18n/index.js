import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import id from './locales/id.json'

const messages = {
  en,
  id
}

const i18n = createI18n({
  legacy: false,
  locale: 'id', // default language
  fallbackLocale: 'en',
  messages,
  globalInjection: true
})

export default i18n
