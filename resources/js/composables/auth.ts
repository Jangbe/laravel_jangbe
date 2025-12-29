import axios from "@axios"
import { ref, shallowRef } from "vue"

export type User = {
    name: string
    username: string
}

const checked = ref(false)
const user = shallowRef<User | null>(null)

export const useAuth = function () {
    const me = async () => {
        if (checked.value) return

        try {
            const { data } = await axios.get("me")
            user.value = data.hasOwnProperty('id') ? data : null
            checked.value = true
        } catch {
            user.value = null
        }
    }

    const logout = async () => {
        try {
            await axios.post('logout')
        } finally {
            user.value = null
        }
    }

    return { checked, user, me, logout }
}