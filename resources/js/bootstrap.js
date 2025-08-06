import axios from 'axios';
import { debounce } from 'lodash';

window.axios = axios;
window._ = { debounce };

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
