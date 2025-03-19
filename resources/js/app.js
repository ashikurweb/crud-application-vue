import { createApp } from 'vue'; // Vue 3 import
import App from './App.vue'; // Your root Vue component
import router from './router'; // Your Vue Router configuration

// Create the Vue app
const app = createApp(App);

// Use Vue Router
app.use(router);

// Mount the app to the #app element
app.mount('#app');