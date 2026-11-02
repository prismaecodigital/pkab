import _ from 'lodash'
import { serialize as objectToFormData } from 'object-to-formdata'
import axios from 'axios'

window._ = _
window.objectToFormData = objectToFormData

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = axios

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
window.axios.defaults.baseURL = '/api/v1/'
window.axios.interceptors.response.use(
  function (response) {
    return response
  },
  function (error) {
    if (error.response.status === 401 || error.response.status === 419) {
      location.assign('/login')
    }

    return Promise.reject(error)
  }
)

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
//     encrypted: true
// });
