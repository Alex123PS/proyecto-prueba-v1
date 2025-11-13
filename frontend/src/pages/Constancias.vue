<script setup>
import ConstanciasService from '@/services/ConstanciasService';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';

const toast = useToast();
const dt = ref();
const constancias = ref([]);
const constanciaForm = ref(false);
const estadoConstanciaForm = ref(false);
const constancia = ref({});
const selectedConstancia = ref({});
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const submitted = ref(false);

async function listarConstancias() {
  try {
    constancias.value = await ConstanciasService.getConstancias();
  } catch (error) {
    console.error(error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron obtener los constancias', life: 3000 });
  }
}

function formularioConstancia() {
  constancia.value = {};
  submitted.value = false;
  constanciaForm.value = true;
}

function ocultarFormularioConstancia() {
  constanciaForm.value = false;
  submitted.value = false;
}

async function guardarConstancia() {
  submitted.value = true;
  try {
    if (constancia?.value?.dni?.trim()) {
      if (constancia.value.id) {
        await ConstanciasService.updateConstancias(constancia.value.id, constancia.value);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Constancia actualizada', life: 3000 });
      } else {
        await ConstanciasService.createConstancias(constancia.value);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Constancia creada', life: 3000 });
      }
      await listarConstancias(); // refrescar tabla con la función global
      constanciaForm.value = false;
      constancia.value = {};
    }
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo realizar la operacion', life: 3000 });
    console.error(error);
  }
}

function editarConstancia(cons) {
  constancia.value = { ...cons };
  constanciaForm.value = true;
}

function formEstadoConstancia(cons) {
  constancia.value = { ...cons };
  estadoConstanciaForm.value = true;
}

async function cambiarEstadoConstancia() {
  try {
    if (constancia?.value?.dni?.trim()) {
      if (constancia.value.estado === 'Emitida') {
        await ConstanciasService.desactivarConstancia(constancia.value.id);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Constancia anulada', life: 3000 });
      } else {
        await ConstanciasService.activarConstancia(constancia.value.id);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Constancia emitida', life: 3000 });
      }
      await listarConstancias(); // refrescar tabla con la función global
      estadoConstanciaForm.value = false;
      constancia.value = {};
    }
  } catch (error) {
    console.error(error);
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo realizar la operacion', life: 3000 });
  }
}

function getEstadoLabel(status) {
  switch (status) {
    case 'Emitida':
      return 'success';
    case 'Anulada':
      return 'danger';
    default:
        return null;
  }
}

async function generarPDF(id) {
  try {
    const pdfUrl = await ConstanciasService.generarPDF(id);
    window.open(pdfUrl, '_blank'); // abrir en nueva pestaña
    toast.add({ severity: 'success', summary: 'PDF generado', detail: 'Constancia generada correctamente', life: 3000 });
  } catch (error) {
    toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo generar el PDF', life: 3000 });
    console.error(error);
  }
}

function exportCSV() {
    dt.value.exportCSV();
}

onMounted(() => {
  listarConstancias();
});

</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <Button label="Generar Constancia" icon="pi pi-id-card" class="mr-2" @click="formularioConstancia" />
        </template>
        <template #end>
          <Button label="Exportar" icon="pi pi-upload" @click="exportCSV($event)" />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedConstancia"
        :value="constancias"
        dataKey="id"
        stripedRows
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        currentPageReportTemplate="Mostrando {first} al {last} de {totalRecords} constancias"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4>Lista de Constancias Generadas</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filters['global'].value" placeholder="Buscar..." />
            </IconField>
          </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem"></Column>
        <Column field="codigo" header="NUM. DOC." sortable></Column>
        <Column field="dni" header="DNI" sortable></Column>
        <Column field="nombres" header="NOMBRES" sortable></Column>
        <Column field="ciclo" header="CICLO" sortable></Column>
        <Column field="sede" header="SEDE" sortable></Column>
        <Column field="area" header="AREA" sortable></Column>
        <Column field="curso" header="CURSO" sortable></Column>
        <Column field="cantidad_horas" header="CANTIDAD HORAS" sortable></Column>
        <Column field="fecha_emision" header="EMISION" sortable></Column>
        <Column field="estado" header="Estado">
          <template #body="slotProps">
            <Tag :value="slotProps.data.estado" :severity="getEstadoLabel(slotProps.data.estado)" />
          </template>
        </Column>
        <Column header="Acciones">
            <template #body="slotProps">
            <ButtonGroup>
                <Button icon="pi pi-pencil" severity="success" outlined @click="editarConstancia(slotProps.data)" />
                <Button v-if="slotProps.data.estado === 'Emitida'" icon="pi pi-times" severity="danger" outlined @click="formEstadoConstancia(slotProps.data)" />
                <Button v-else icon="pi pi-check" severity="info" outlined @click="formEstadoConstancia(slotProps.data)" />
                <Button icon="pi pi-file-pdf" severity="secondary" outlined @click="generarPDF(slotProps.data.id)" />
            </ButtonGroup>
            </template>
        </Column>
      </DataTable>
    </div>

    <Dialog v-model:visible="constanciaForm" :style="{ width: '450px' }" header="Generar Constancia" :modal="true">
      <div class="flex flex-col gap-6">
        <div>
          <label for="Número de DNI" class="block font-bold mb-3">Número de DNI</label>
          <InputText id="dni" v-model.trim="constancia.dni" required="true" autofocus :invalid="submitted && !constancia.dni" fluid />
          <small v-if="submitted && !constancia.dni" class="text-red-500">DNI requerido.</small>
        </div>
        <div>
          <label for="ciclo" class="block font-bold mb-3">INGRESAR CICLOS</label>
          <InputText id="ciclo" v-model="constancia.ciclo" required="true" fluid />
        </div>
      </div>

      <template #footer>
        <Button label="Cancelar" icon="pi pi-times" text @click="ocultarFormularioConstancia" />
        <Button label="Guardar" icon="pi pi-check" @click="guardarConstancia" />
      </template>
    </Dialog>

    <Dialog v-model:visible="estadoConstanciaForm" :style="{ width: '450px' }" header="Confirm" :modal="true">
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="constancia.estado === 'Anulada'"
          >Esta seguro de emitir la constancia de <b>{{ constancia.nombres }}</b
          >?</span
        >
        <span v-else
          >Esta seguro de anular la constancia de <b>{{ constancia.nombres }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button label="No" icon="pi pi-times" text @click="estadoConstanciaForm = false" />
        <Button label="Yes" icon="pi pi-check" @click="cambiarEstadoConstancia" />
      </template>
    </Dialog>
  </div>
</template>
