import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null)
    const tenant = ref(null)

    const isAuthenticated = computed(() => !!user.value)
    const isOwner = computed(() => user.value?.role === 'owner')

    function setUser(userData) {
        user.value = userData
    }

    function setTenant(tenantData) {
        tenant.value = tenantData
    }

    function clear() {
        user.value = null
        tenant.value = null
    }

    return { user, tenant, isAuthenticated, isOwner, setUser, setTenant, clear }
})