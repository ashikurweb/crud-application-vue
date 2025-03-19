<template>
    <div class="max-w-6xl mx-auto mt-8 py-10 px-4">
    <h2 class="text-4xl font-extrabold text-gray-800 mb-8 text-center tracking-wide">
        All Posts
    </h2>

    <!-- Create Button -->
    <div class="mb-6">
        <button
            @click="navigateToCreatePost"
            class="bg-gradient-to-r cursor-pointer from-teal-500 to-green-500 text-white font-semibold py-3 px-5 rounded-lg hover:shadow-lg transition-shadow duration-300"
        >
            + Create Post
        </button>
    </div>

    <!-- Modern Table -->
    <div class="bg-white border border-gray-300 rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Content</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(post, index) in posts.data" :key="post.id" class="hover:bg-gray-100 transition-all duration-300">
                    <td class="px-6 py-4 text-gray-800 font-medium">
                        {{ (posts.current_page - 1) * posts.per_page + index + 1 }}
                    </td>
                    <td class="px-6 py-4 text-gray-900 font-semibold">{{ post.title }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ limitText(post.content, 50) }}</td>
                    <td class="px-6 py-4 flex space-x-3">
                        <button @click="openViewModal(post)" class="text-blue-500 hover:text-blue-700"><i class="fa fa-eye"></i></button>
                        <button @click="editPost(post)" class="text-yellow-500 hover:text-yellow-700"><i class="fa fa-edit"></i></button>
                        <button @click="deletePost(post.id)" class="text-red-500 hover:text-red-700"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div v-if="showPagination && posts.links" class="flex justify-center items-center mt-6 space-x-2">
        <button v-for="(link, index) in posts.links" :key="index" @click="fetchPosts(link.url)"
            :disabled="!link.url"
            class="px-4 py-2 rounded-lg transition-all duration-300"
            :class="{
                'bg-indigo-500 hover:bg-indigo-600 text-white shadow-md': link.active,
                'bg-gray-400 hover:bg-indigo-500 text-white': !link.active && link.url,
                'bg-gray-300 text-gray-600 cursor-not-allowed': !link.url
            }"
            v-html="link.label">
        </button>
    </div>


    <!-- View Modal -->
    <transition name="fade">
        <div v-if="isViewModalOpen" class="fixed inset-0 backdrop-brightness-50 flex items-center justify-center p-6" @click.self="closeViewModal">
            <transition name="slide">
                <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Post Details</h3>
                        <button @click="closeViewModal" class="text-rose-500 hover:text-rose-700">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-semibold text-gray-700">ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedPost.id }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Title</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedPost.title }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-700">Content</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedPost.content }}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button @click="closeViewModal" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            Close
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</div>

</template>

<script>
import axios from "axios";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

export default {
    data() {
        return {
            posts: {},
            showPagination: false,
            isViewModalOpen: false, 
            selectedPost: {}, 
        };
    },

    methods: {
        async fetchPosts(url = "/api/posts") {
            const response = await axios.get(url);
            this.posts = response.data.posts;
            this.showPagination = response.data.showPagination;
        },

        navigateToCreatePost() {
            this.$router.push('/create-post');
        },

        editPost(post) {
            this.$router.push({ path: '/create-post', query: { id: post.id } });
        },

        async deletePost(id) {
            await axios.delete(`/api/posts/${id}`);

            Toastify({
                text: "Post deleted successfully",
                duration: 3000,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                style: {
                    background: "linear-gradient(to right, #ff6b6b, #f13)",
                },
            }).showToast();
            this.fetchPosts();
        },

        limitText(text, limit) {
            if (text.length > limit) {
                return text.substring(0, limit) + "...";
            }
            return text;
        },

        openViewModal(post) {
            this.selectedPost = post; 
            this.isViewModalOpen = true; 
        },

        closeViewModal() {
            this.isViewModalOpen = false;
            this.selectedPost = {}; 
        },
    },

    mounted() {
        this.fetchPosts();
    },
};
</script>

<style scoped>
/* Fade Transition for Modal Background */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Slide Transition for Modal Content */
.slide-enter-active, .slide-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.slide-enter-from, .slide-leave-to {
    transform: translateY(-20px);
    opacity: 0;
}
</style>