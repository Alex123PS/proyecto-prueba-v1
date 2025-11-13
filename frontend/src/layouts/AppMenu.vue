<script setup>
import { ref, computed } from 'vue'
import AppMenuItem from './AppMenuItem.vue';
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

const model = computed(() => [
  {
    label: 'Home',
    items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/' }]
  },
  {
    label: 'Pages',
    icon: 'pi pi-fw pi-briefcase',
    items: [
      auth.hasPermission('constancias.view') && {
        label: 'Constancias',
        icon: 'pi pi-fw pi-id-card',
        to: '/pages/constancias'
      },
      auth.hasPermission('users.view') && {
        label: 'Usuarios',
        icon: 'pi pi-fw pi-user',
        to: '/pages/usuarios'
      }
    ].filter(Boolean)
  }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
