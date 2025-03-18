<template>
    <div class="max-w-6xl mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-4xl font-bold mb-6 text-gray-800 text-center">
            Create Post
        </h2>

        <form class="space-y-4" @submit.prevent="savePost">
            <!-- Title Field -->
            <div>
                <label for="title" class="block text-gray-700 font-medium mb-1"
                    >Title</label
                >
                <input
                    type="text"
                    id="title"
                    v-model="form.title"
                    placeholder="Enter post title"
                    :class="{
                        'w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-violet-500 focus:outline-none transition-all duration-300': true,
                        'border-red-500': errors.title
                    }"
                />
                <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</p>
            </div>

            <!-- Content Field -->
            <div>
                <label
                    for="content"
                    class="block text-gray-700 font-medium mb-1"
                    >Content</label
                >
                <textarea
                    id="content"
                    v-model="form.content"
                    placeholder="Enter post content"
                    rows="4"
                    :class="{
                        'w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-1 focus:ring-violet-500 focus:outline-none transition-all duration-300': true,
                        'border-red-500': errors.content
                    }"
                ></textarea>
                <p v-if="errors.content" class="text-red-500 text-sm mt-1">{{ errors.content[0] }}</p>

            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :class="{
                    'w-full py-3 bg-violet-500 text-white font-semibold rounded-md hover:bg-violet-600 transition-all cursor-pointer': !editMode,
                    'w-full py-3 bg-amber-500 text-white font-semibold rounded-md hover:bg-amber-600 transition-all cursor-pointer': editMode
                }"
            >
                {{ editMode ? 'Update Post' : 'Create Post' }}
            </button>
        </form>
    </div>

    <!-- Post Grid -->
    <div class="max-w-6xl mx-auto mt-8 py-10">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            All Posts
        </h2>

        <div
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >
            <div
                v-for="post in posts.data"
                :key="post.id"
                class="bg-white p-6 border border-slate-200 rounded-xl shadow-md hover:shadow-2xl transform-shadow duration-300 flex flex-col justify-between"
            >
                <div>
                    <h2 class="text-lg font-semibold mb-2 text-gray-900">
                        {{ post.title }}
                    </h2>
                    <p class="text-gray-700 mb-4">
                        {{ limitText(post.content, 30) }}
                    </p>
                </div>

                <!-- Fixed Button Group at Bottom -->
                <div
                    class="mt-auto flex justify-between gap-4 pt-4 border-t border-gray-200"
                >
                    <!-- Edit Button -->
                    <button
                        class="bg-amber-100 text-amber-500 py-2 px-4 font-semibold rounded-md hover:bg-amber-600 hover:text-white transition-all cursor-pointer" @click="editPost(post)"
                    >
                        <i class="fa fa-edit"></i> Edit
                    </button>

                    <!-- Delete Button -->
                    <button
                        class="bg-red-100 text-red-500 py-2 px-4 font-semibold rounded-md hover:bg-red-600 hover:text-white transition-all cursor-pointer" @click="deletePost(post.id)"
                    >
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- pagination -->
        <div if="posts.links" class="flex justify-center items-center space-x-2 mt-6">
            <button v-for="(link,index) in posts.links" 
                :key="index"
                @click="fetchPosts(link.url)"
                :disabled="!link.url"
                class="px-4 py-2 rounded-md"
                :class="{
                    'bg-indigo-500 hover:bg-indigo-600 text-white': link.active,
                    'bg-gray-500 hover:bg-gray-600 text-white': !link.active && link.url,
                    'bg-gray-300 text-gray-600 cursor-not-allowed': !link.url
                }"
                v-html="link.label"></button>
        </div>

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

            form: {
                title: "",
                content: "",
            },

            errors: {},
        };
    },

    methods: {
        async fetchPosts(url = "/api/posts") {
            const { data } = await axios.get(url);
            this.posts = data;
        },

        async savePost() {
            try {

                if (this.editMode) {
                    const response = await axios.put(`/api/posts/${this.editId}`, this.form);
                    this.editMode = false;
                } else {
                    const response = await axios.post("/api/posts", this.form);
                }

                this.form = {
                    title: "",
                    content: "",
                }

                if (this.editMode) {
                    Toastify({
                        text: "Post updated successfully",
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top", 
                        position: "right", 
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #00b4db, #0083b0)",
                        },
                    }).showToast();
                } else {
                    Toastify({
                        text: "Post created successfully",
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #2ab4db, #4d83b0)",
                        },
                    }).showToast();
                }

                this.fetchPosts();
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error("Unexpected error:", error);
                }
            }
        },

        editPost( post ) {
            this.form = {
                title: post.title,
                content: post.content
            };

            this.editId = post.id
            this.editMode = true
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
                }            
            }).showToast();
            this.fetchPosts();
        },

        limitText(text, limit) {
            if (text.length > limit) {
                return text.substring(0, limit) + "...";
            }
            return text;
        },
    },

    mounted() {
        this.fetchPosts();
    },
};
</script>
