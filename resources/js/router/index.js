import { createRouter, createWebHistory } from 'vue-router';
import Barbers from '../pages/Barbers.vue';
import Services from '../pages/Services.vue';
import Home from '../pages/Home.vue';
import Booking from '../pages/Booking.vue';
import Contacts from '@/pages/Contacts.vue';
import AdminLayout from '../pages/admin/AdminLayout.vue';
import AdminLogin from '../pages/admin/Login.vue';
import AdminDashboard from '../pages/admin/Dashboard.vue';
import AdminBookings from '../pages/admin/Bookings.vue';
import AdminBarbers from '../pages/admin/Barbers.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/barbers',
        name: 'barbers',
        component: Barbers
    },
    {
        path: '/services',
        name: 'services',
        component: Services
    },
    {
        path: '/booking',
        name: 'booking',
        component: Booking
    },
    {
        path: '/contacts',
        name: 'contacts',
        component: Contacts
    },
    {
        path: '/admin/login',
        name: 'AdminLogin',
        component: AdminLogin,
        meta: { requiresAuth: false }
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: AdminDashboard
            },
            {
                path: 'bookings',
                name: 'AdminBookings',
                component: AdminBookings
            },
            {
                path: 'barbers',
                name: 'AdminBarbers',
                component: AdminBarbers
            }
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guard using auth store
router.beforeEach(async (to, from, next) => {
    // Check if route requires authentication
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // Get auth store
        const authStore = (await import('@/stores/authStore')).useAuthStore()
        
        // Check if user is authenticated
        if (!authStore.isAuthenticated) {
            // Try to restore session
            await authStore.init()
            
            // If still not authenticated, redirect to login
            if (!authStore.isAuthenticated) {
                next({
                    path: '/admin/login',
                    query: { redirect: to.fullPath }
                })
                return
            }
        }
    }
    next()
})

export default router; 