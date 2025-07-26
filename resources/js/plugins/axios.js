import axios from "axios";

axios.defaults.baseURL = import.meta.env.VITE_API_URL;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

export default axios;
