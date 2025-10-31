import { useRouter } from 'vue-router'

export function useNavigation() {
  const router = useRouter()

  const navigateTo = (routeName: string) => {
    router.push({ name: routeName })
  }

  return { navigateTo }
}
