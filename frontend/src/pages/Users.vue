<script setup>
import { ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import { FilterMatchMode } from "@primevue/core/api";
import UsersService from "@/services/UsersService";
import RolesService from "@/services/RolesService";

const toast = useToast();
const dt = ref(); // Referencia a la DataTable
const users = ref([]); // Lista de usuarios cargados desde la API
const user = ref({}); // Usuario actual en edición o creación
const userDialog = ref(false); // Controla visibilidad del formulario modal
const submitted = ref(false); // Controla validaciones visuales

// Listas de opciones para selects
const rolesList = ref([]);

// Listas formateadas para PrimeVue <Dropdown>
const roleItems = ref([]);

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Obtener lista de usuarios
async function listarUsers() {
  try {
    const data = await UsersService.getUsers();
    users.value = data;
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "No se pudieron obtener los usuarios",
      life: 3000,
    });
  }
}

// Obtener lista de roles desde la API
async function listarRoles() {
  try {
    const rolesData = await RolesService.getRoles();
    rolesList.value = rolesData;

    // Convertimos el array de roles a un formato compatible con <Dropdown>
    roleItems.value = rolesList.value.map((r) => ({
      label: r.name,
      value: r.name,
    }));
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "warn",
      summary: "Aviso",
      detail: "No se pudieron cargar roles",
      life: 3000,
    });
  }
}

// Abrir formulario para crear un nuevo usuario
function nuevoUser() {
  user.value = {};
  submitted.value = false;
  userDialog.value = true;
}

// Cerrar el formulario
function ocultarDialog() {
  userDialog.value = false;
  submitted.value = false;
}

// Guardar usuario (crear o actualizar)
async function guardarUser() {
  submitted.value = true;
  try {
    // Validar si hay nombre y email antes de enviar
    if (user?.value?.name?.trim() && user?.value?.email?.trim()) {
      // Creamos el "payload" (datos que enviaremos a la API)
      // Esto permite tener control sobre qué campos se envían
      const payload = {
        name: user.value.name,
        email: user.value.email,
        password: user.value.password,
        roles: user.value.roles,
      };

      if (user.value.id) {
        // Si el usuario ya tiene id => editar
        await UsersService.updateUser(user.value.id, payload);
        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Usuario actualizado",
          life: 3000,
        });
      } else {
        // Caso contrario => crear nuevo usuario
        await UsersService.createUser(payload);
        toast.add({
          severity: "success",
          summary: "Éxito",
          detail: "Usuario creado",
          life: 3000,
        });
      }

      await listarUsers(); // Refrescamos la tabla
      userDialog.value = false;
      user.value = {};
    }
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "No se pudo guardar el usuario",
      life: 3000,
    });
  }
}

// Cargar datos de usuario seleccionado para edición
function editarUser(u) {
  user.value = { ...u }; // Clonamos el objeto para evitar mutar la tabla directamente
  userDialog.value = true;
}

// Desactivar usuario (similar a “eliminar”, pero sin borrar registro)
async function desactivarUser(u) {
  try {
    await UsersService.desactivarUser(u.id);
    toast.add({
      severity: "warn",
      summary: "Desactivado",
      detail: "Usuario desactivado correctamente",
      life: 3000,
    });
    await listarUsers();
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "No se pudo desactivar el usuario",
      life: 3000,
    });
  }
}

// Activar usuario
async function activarUser(u) {
  try {
    await UsersService.activarUser(u.id);
    toast.add({
      severity: "success",
      summary: "Activado",
      detail: "Usuario activado correctamente",
      life: 3000,
    });
    await listarUsers();
  } catch (error) {
    console.error(error);
    toast.add({
      severity: "error",
      summary: "Error",
      detail: "No se pudo activar el usuario",
      life: 3000,
    });
  }
}

// Exportar tabla a CSV
function exportCSV() {
  dt.value.exportCSV();
}

// Se ejecuta al montar el componente
onMounted(async () => {
  await listarUsers();
  await listarRoles();
});
</script>

<template>
  <div class="card">
    <Toolbar class="mb-6">
      <template #start>
        <Button
          label="Nuevo Usuario"
          icon="pi pi-user-plus"
          class="mr-2"
          @click="nuevoUser"
        />
      </template>
      <template #end>
        <Button label="Exportar" icon="pi pi-upload" @click="exportCSV" />
      </template>
    </Toolbar>

    <DataTable
      ref="dt"
      :value="users"
      dataKey="id"
      :paginator="true"
      :rows="10"
      :filters="filters"
      :rowsPerPageOptions="[5, 10, 25]"
      currentPageReportTemplate="Mostrando {first} al {last} de {totalRecords} usuarios"
    >
      <template #header>
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <h4>Lista de Usuarios</h4>
          <IconField>
            <InputIcon><i class="pi pi-search" /></InputIcon>
            <InputText v-model="filters['global'].value" placeholder="Buscar..." />
          </IconField>
        </div>
      </template>

      <Column field="id" header="ID" sortable />
      <Column field="name" header="Nombre" sortable />
      <Column field="email" header="Email" sortable />
      <Column field="estado" header="Estado" sortable />
      <Column field="roles" header="Rol">
        <template #body="slotProps">
          <Tag
            v-for="rol in slotProps.data.roles"
            :key="rol.id"
            :value="rol.name"
            severity="info"
            class="mr-1"
          />
        </template>
      </Column>
      <Column header="Acciones">
        <template #body="slotProps">
          <Button
            icon="pi pi-pencil"
            outlined
            rounded
            class="mr-1"
            severity="success"
            @click="editarUser(slotProps.data)"
          />
          <Button
            v-if="slotProps.data.estado === 'ACTIVO'"
            icon="pi pi-times"
            outlined
            rounded
            severity="danger"
            @click="desactivarUser(slotProps.data)"
          />
          <Button
            v-else
            icon="pi pi-check"
            outlined
            rounded
            severity="info"
            @click="activarUser(slotProps.data)"
          />
        </template>
      </Column>
    </DataTable>

    <!-- Formulario modal -->
    <Dialog
      v-model:visible="userDialog"
      :style="{ width: '500px' }"
      header="Usuario"
      :modal="true"
    >
      <div class="flex flex-col gap-4">
        <div>
          <label for="name" class="block font-bold mb-2">Nombre</label>
          <InputText
            id="name"
            v-model.trim="user.name"
            required="true"
            :invalid="submitted && !user.name"
            fluid
          />
          <small v-if="submitted && !user.name" class="text-red-500"
            >Nombre requerido.</small
          >
        </div>

        <div>
          <label for="email" class="block font-bold mb-2">Email</label>
          <InputText
            id="email"
            v-model.trim="user.email"
            required="true"
            :invalid="submitted && !user.email"
            fluid
          />
          <small v-if="submitted && !user.email" class="text-red-500"
            >Email requerido.</small
          >
        </div>

        <div>
          <label for="password" class="block font-bold mb-2">Contraseña</label>
          <Password
            id="password"
            v-model.trim="user.password"
            :feedback="false"
            toggleMask
            :invalid="submitted && !user.password"
            fluid
          />
          <small v-if="submitted && !user.password" class="text-red-500">
            Contraseña requerida.
          </small>
        </div>

        <div>
          <label for="roles" class="block font-bold mb-2">Roles</label>
          <MultiSelect
            id="roles"
            v-model="user.roles"
            :options="roleItems"
            optionLabel="label"
            optionValue="value"
            display="chip"
            placeholder="Selecciona roles"
            fluid
          />
        </div>
      </div>

      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" text @click="ocultarDialog" />
        <Button label="Guardar" icon="pi pi-check" @click="guardarUser" />
      </template>
    </Dialog>
  </div>
</template>
