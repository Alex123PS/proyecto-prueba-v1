<script setup>
import ConductoresService from '@/services/ConductoresService';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';

const toast = useToast();
const dt = ref();
const conductores = ref([]);
const conductorForm = ref(false);
const estadoConductorForm = ref(false);
const conductor = ref({});
const selectedConductor = ref({});
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);

async function listarConductores() {
  try {
    conductores.value = await ConductoresService.getConductores();
  } catch (error) {
    console.error(error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron obtener los conductores', life: 3000 });
  }
}

function formularioConductor() {
  conductor.value = {};
  submitted.value = false;
  conductorForm.value = true;
}

function ocultarFormularioConductor() {
  conductorForm.value = false;
  submitted.value = false;
}

async function guardarConductor() {
  submitted.value = true;
  try {
    if (conductor?.value?.documento?.trim()) {
      if (conductor.value.id) {
        await ConductoresService.updateConductor(conductor.value.id, conductor.value);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Conductor actualizado', life: 3000 });
      } else {
        await ConductoresService.createConductor(conductor.value);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Conductor creado', life: 3000 });
      }
      await listarConductores(); // refrescar tabla con la función global
      conductorForm.value = false;
      conductor.value = {};
    }
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo realizar la operacion', life: 3000 });
    console.error(error);
  }
}

function editarConductor(cond) {
  conductor.value = { ...cond };
  conductorForm.value = true;
}

function formEstadoConductor(cond) {
  conductor.value = { ...cond };
  estadoConductorForm.value = true;
}

async function cambiarEstadoConductor() {
  try {
    if (conductor?.value?.documento?.trim()) {
      if (conductor.value.estado === 'ACTIVO') {
        await ConductoresService.desactivarConductor(conductor.value.id);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Conductor desactivado', life: 3000 });
      } else {
        await ConductoresService.activarConductor(conductor.value.id);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Conductor activado', life: 3000 });
      }
      await listarConductores(); // refrescar tabla con la función global
      estadoConductorForm.value = false;
      conductor.value = {};
    }
  } catch (error) {
    console.error(error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo realizar la operacion', life: 3000 });
  }
}

function getEstadoLabel(status) {
  switch (status) {
    case 'ACTIVO':
      return 'success';
    case 'INACTIVO':
      return 'danger';
    default:
        return null;
  }
}

function exportCSV() {
    dt.value.exportCSV();
}

onMounted(() => {
  listarConductores();
});

</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button label="Nuevo Conductor" icon="pi pi-user-plus" class="mr-2" @click="formularioConductor" />
        </template>
        <template #end>
          <Button label="Exportar" icon="pi pi-upload" @click="exportCSV($event)" />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedConductor"
        :value="conductores"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Mostrando {first} al {last} de {totalRecords} conductores"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4>Lista de Conductores</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filters['global'].value" placeholder="Buscar..." />
            </IconField>
          </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem"></Column>
        <Column field="id" header="ID" sortable></Column>
        <Column field="documento" header="DNI" sortable></Column>
        <Column field="nombres" header="Nombres" sortable></Column>
        <Column field="num_licencia" header="Licencia" sortable></Column>
        <Column field="estado" header="Estado">
          <template #body="slotProps">
            <Tag :value="slotProps.data.estado" :severity="getEstadoLabel(slotProps.data.estado)" />
          </template>
        </Column>
        <Column header="Acciones">
          <template #body="slotProps">
            <Button icon="pi pi-pencil" outlined rounded class="mr-1" severity="success" @click="editarConductor(slotProps.data)" />
            <Button v-if="slotProps.data.estado === 'ACTIVO'" icon="pi pi-times" outlined rounded class="mr-1" severity="danger" @click="formEstadoConductor(slotProps.data)" />
            <Button v-else icon="pi pi-check" outlined rounded severity="info" @click="formEstadoConductor(slotProps.data)" />
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog v-model:visible="conductorForm" :style="{ width: '450px' }" header="Conductor" :modal="true">
      <div class="flex flex-col gap-6">
        <div>
          <label for="documento" class="block font-bold mb-3">DNI</label>
          <InputText id="documento" v-model.trim="conductor.documento" required="true" autofocus :invalid="submitted && !conductor.documento" fluid />
          <small v-if="submitted && !conductor.documento" class="text-red-500">DNI requerido.</small>
        </div>
        <div>
          <label for="nombres" class="block font-bold mb-3">Nombres Completos</label>
          <InputText id="nombres" v-model="conductor.nombres" required="true" fluid />
        </div>
        <div>
          <label for="num_licencia" class="block font-bold mb-3">Licencia</label>
          <InputText id="num_licencia" v-model="conductor.num_licencia" required="true" fluid />
        </div>
      </div>

      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" text @click="ocultarFormularioConductor" />
        <Button label="Guardar" icon="pi pi-check" @click="guardarConductor" />
      </template>
    </Dialog>

    <Dialog v-model:visible="estadoConductorForm" :style="{ width: '450px' }" header="Confirm" :modal="true">
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="conductor.estado === 'ACTIVO'"
          >Esta seguro de desactivar al conductor <b>{{ conductor.nombres }}</b
          >?</span
        >
        <span v-else
          >Esta seguro de activar al conductor <b>{{ conductor.nombres }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" text @click="estadoConductorForm = false" />
        <Button label="Yes" icon="pi pi-check" @click="cambiarEstadoConductor" />
      </template>
    </Dialog>
  </div>
</template>
