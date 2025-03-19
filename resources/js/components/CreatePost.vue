<template>
     <!-- Back -->
     <div class="mx-auto max-w-4xl mt-10">
        <a href="javascript:history.back()"
            class="bg-gradient-to-r from-pink-400 via-rose-500 to-pink-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-gradient-to-r hover:from-rose-500 hover:via-pink-600 hover:to-pink-700 transition-all cursor-pointer"
        >
            <i class="fa fa-arrow-left mr-2"></i> Back
        </a>
    </div>


    <div class="max-w-4xl mx-auto mt-10 p-8 bg-white shadow-lg rounded-2xl transition-all duration-300">
        <!-- Heading -->
        <h2 class="text-4xl font-extrabold mb-8 text-gray-900 text-center">
            {{ editMode ? 'Edit Post' : 'Create Post' }}
        </h2>

        <!-- Form -->
        <form class="space-y-6" @submit.prevent="savePost">
            <!-- Title Field -->
            <div>
                <label for="title" class="block text-lg font-medium text-gray-700 mb-2">
                    Title
                </label>
                <input
                    type="text"
                    id="title"
                    v-model="form.title"
                    placeholder="Enter post title"
                    class="w-full px-5 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 outline-none"
                    :class="{'border-red-500': errors.title}"
                />
                <p v-if="errors.title" class="text-red-500 text-sm mt-1">{{ errors.title[0] }}</p>
            </div>

            <!-- Content Field -->
            <div>
                <label for="content" class="block text-lg font-medium text-gray-700 mb-2">
                    Content
                </label>
                <textarea
                    id="content"
                    v-model="form.content"
                    placeholder="Enter post content"
                    rows="5"
                    class="w-full px-5 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-1 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 outline-none"
                    :class="{'border-red-500': errors.content}"
                ></textarea>
                <p v-if="errors.content" class="text-red-500 text-sm mt-1">{{ errors.content[0] }}</p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full py-3 text-white font-semibold rounded-lg cursor-pointer shadow-md transition-all duration-300"
                :class="editMode ? 'bg-amber-500 hover:bg-amber-600' : 'bg-violet-500 hover:bg-violet-600'"
            >
                {{ editMode ? 'Update Post' : 'Create Post' }}
            </button>
        </form>
    </div>
</template>


<script>
import axios from "axios";
import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";

export default {
    data() {
        return {
            form: {
                title: "",
                content: "",
            },
            errors: {},
            editMode: false,
            editId: null,
        };
    },
    created() {
        if (this.$route.query.id) {
            this.editMode = true;
            this.editId = this.$route.query.id;
            this.fetchPost(this.editId); 
        }
    },
    methods: {
        async fetchPost(id) {
            try {
                const response = await axios.get(`/api/posts/${id}`);
                this.form = response.data; 
            } catch (error) {
                console.error("Error fetching post:", error);
            }
        },
        async savePost() {
            try {
                if (this.editMode) {
                    const response = await axios.put(`/api/posts/${this.editId}`, this.form);
                    Toastify({
                        text: "Post updated successfully",
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                    }).showToast();
                } else {
                    const response = await axios.post("/api/posts", this.form);
                    Toastify({
                        text: "Post created successfully",
                        duration: 3000,
                        newWindow: true,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                    }).showToast();
                }

                this.form = {
                    title: "",
                    content: "",
                };

                this.$router.push("/");
            } catch (error) {
                if (error.response && error.response.data.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    console.error(error);
                }
            }
        }
    },
};
</script>