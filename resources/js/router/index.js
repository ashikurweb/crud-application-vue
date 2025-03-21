// router/index.js

import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        component: () => import('@/components/Posts.vue'),
    },
    {
        path: '/create-post',
        component: () => import('@/components/CreatePost.vue'),
    },
    {
        path: '/edit-post/:id',
        component: () => import('@/components/CreatePost.vue'),
        props: true,
    },
];

const router = createRouter({
    history: createWebHistory(), 
    routes,
});

export default router;