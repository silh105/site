import { createApp } from 'vue';
import AuthModal from './components/AuthModal.vue';
import NewsFeed from './components/NewsFeed.vue';
import Messenger from './components/Messenger.vue';

const app = createApp({
    data() {
        return { isAuthModalOpen: false };
    },
    methods: {
        openAuthModal() { this.isAuthModalOpen = true; },
        closeAuthModal() { this.isAuthModalOpen = false; }
    }
});

app.component('auth-modal', AuthModal);
app.component('news-feed', NewsFeed);
app.component('messenger', Messenger);
app.mount('#app');