<template>
<div>
    <div class="card">
        <Toolbar class="mb-6">
            <template #start>
                <Button label="Nueva Constancia" icon="pi pi-id-card" class="mr-2" @click="abrirFormulario()" />
            </template>
            <template #end>
                <Button label="Exportar CSV" icon="pi pi-upload" @click="exportCSV()" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedConstancia" :value="constancias" dataKey="id" stripedRows paginator :rows="10" :filters="filters" :rowsPerPageOptions="[5,10,25]">
            <template #header>
                <div class="flex flex-wrap gap-2 items-center justify-between">
                    <h4 class="m-0">Lista de Constancias Generadas</h4>
                    <div class="flex items-center gap-2">
                        <InputText v-model="filters.global.value" placeholder="Buscar..." />
                        <Button icon="pi pi-refresh" class="ml-2" @click="listarConstancias" />
                    </div>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" />
            <Column field="codigo" header="NUM. DOC." sortable />
            <Column field="dni" header="DNI" sortable />
            <Column field="nombres" header="NOMBRES" sortable />
            <Column field="ciclo" header="CICLO" sortable />
            <Column field="sede" header="SEDE" sortable />
            <Column field="area" header="ÁREA" sortable />
            <Column field="curso" header="CURSO" sortable />
            <Column field="cantidad_horas" header="HORAS" sortable />
            <Column field="fecha_emision" header="EMISIÓN" sortable />

            <Column header="Estado">
                <template #body="{ data }">
                    <Tag :value="data.estado" :severity="getEstadoLabel(data.estado)" />
                </template>
            </Column>

            <Column header="Acciones">
                <template #body="{ data }">
                    <ButtonGroup>
                        <Button icon="pi pi-pencil" severity="success" outlined @click="editarConstancia(data)" />
                        <Button icon="pi pi-refresh" severity="warning" outlined @click="formEstadoConstancia(data)" />
                        <Button icon="pi pi-file-pdf" severity="danger" outlined @click="generarPDF(data.id)" />
                    </ButtonGroup>
                </template>
            </Column>
        </DataTable>
    </div>

    <!-- DIALOG: CREAR / EDITAR CONSTANCIA -->
    <Dialog v-model:visible="constanciaForm" :style="{ width: '600px' }" modal header="Crear / Editar Constancia">
        <div class="p-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-bold">DNI</label>
                    <InputText v-model.trim="constancia.dni" :class="{ 'p-invalid': submitted && !constancia.dni }" />
                    <small v-if="submitted && !constancia.dni" class="p-error">DNI requerido.</small>
                </div>

                <div>
                    <label class="font-bold">Código</label>
                    <InputText v-model="constancia.codigo" />
                </div>

                <div>
                    <label class="font-bold">Nombres</label>
                    <InputText v-model="constancia.nombres" />
                </div>

                <div>
                    <label class="font-bold">Ciclo</label>
                    <InputText v-model="constancia.ciclo" />
                </div>

                <div>
                    <label class="font-bold">Sede</label>
                    <InputText v-model="constancia.sede" />
                </div>

                <div>
                    <label class="font-bold">Área</label>
                    <InputText v-model="constancia.area" />
                </div>

                <div>
                    <label class="font-bold">Curso</label>
                    <InputText v-model="constancia.curso" />
                </div>

                <div>
                    <label class="font-bold">Horas</label>
                    <InputText v-model="constancia.cantidad_horas" />
                </div>

                <div>
                    <label class="font-bold">Fecha Emisión</label>
                    <InputText v-model="constancia.fecha_emision" placeholder="DD/MM/YYYY" />
                </div>

                <div class="col-span-2">
                    <label class="font-bold">Texto / Observaciones (lo que aparecerá en la constancia)</label>
                    <InputTextarea v-model="constancia.texto_constancia" rows="4" autoResize />
                </div>

                <div class="col-span-2">
                    <label class="font-bold">Responsable (firma mostrada)</label>
                    <InputText v-model="constancia.responsable" placeholder="Mg. Nombre Responsable - Cargo" />
                </div>
            </div>
        </div>

        <template #footer>
            <Button label="Cancelar" icon="pi pi-times" text @click="ocultarFormularioConstancia" />
            <Button label="Guardar" icon="pi pi-check" @click="guardarConstancia" />
        </template>
    </Dialog>

    <!-- DIALOG: CONFIRMAR ESTADO -->
    <Dialog v-model:visible="estadoConstanciaForm" :style="{ width: '450px' }" modal header="Confirmar cambio de estado">
        <div class="flex items-center gap-4">
            <i class="pi pi-exclamation-triangle text-3xl" />
            <span>¿Está seguro de cambiar el estado de la constancia de <b>{{ constancia.nombres }}</b>?</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="estadoConstanciaForm = false" />
            <Button label="Sí" icon="pi pi-check" @click="cambiarEstadoConstancia" />
        </template>
    </Dialog>
</div>
</template>

<script setup>
import ConstanciasService from '@/services/ConstanciasService';
import { FilterMatchMode } from '@primevue/core/api';
import { useToast } from 'primevue/usetoast';
import { onMounted, ref } from 'vue';
import jsPDF from 'jspdf';
import * as QRCode from 'qrcode';

const toast = useToast();
const dt = ref();
const constancias = ref([]);
const constanciaForm = ref(false);
const estadoConstanciaForm = ref(false);
const constancia = ref({});
const selectedConstancia = ref([]);
const submitted = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const LOGO_UNAP = "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Logo_UNAP.png/800px-Logo_UNAP.png";
const LOGO_CEPRE = "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cb/Logo_UNAP.png/800px-Logo_UNAP.png";

async function listarConstancias() {
    try {
        constancias.value = await ConstanciasService.getConstancias();
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudieron obtener las constancias', life: 3000 });
    }
}

function abrirFormulario(data = null) {
    if (data) {
        constancia.value = { ...data };
    } else {
        constancia.value = {
            dni: '',
            codigo: '',
            nombres: '',
            ciclo: '',
            sede: '',
            area: '',
            curso: '',
            cantidad_horas: '',
            fecha_emision: new Date().toLocaleDateString(),
            texto_constancia: '',
            responsable: 'Mg. Nombre Responsable - Cargo',
            estado: 'Emitida'
        };
    }
    submitted.value = false;
    constanciaForm.value = true;
}

function ocultarFormularioConstancia() {
    constanciaForm.value = false;
    submitted.value = false;
}

async function guardarConstancia() {
    submitted.value = true;
    if (!constancia.value?.dni?.trim()) {
        toast.add({ severity: 'warn', summary: 'Atención', detail: 'Debe ingresar DNI', life: 2500 });
        return;
    }

    try {
        if (constancia.value.id) {
            await ConstanciasService.updateConstancias(constancia.value.id, constancia.value);
            toast.add({ severity: 'success', summary: 'Actualizado', detail: 'Constancia actualizada', life: 3000 });
        } else {
            await ConstanciasService.createConstancias(constancia.value);
            toast.add({ severity: 'success', summary: 'Creado', detail: 'Constancia creada', life: 3000 });
        }
        await listarConstancias();
        constanciaForm.value = false;
        constancia.value = {};
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo guardar la constancia', life: 3000 });
    }
}

function editarConstancia(data) {
    abrirFormulario(data);
}

function formEstadoConstancia(data) {
    constancia.value = { ...data };
    estadoConstanciaForm.value = true;
}

async function cambiarEstadoConstancia() {
    try {
        if (constancia.value.estado === 'Emitida') {
            await ConstanciasService.desactivarConstancia(constancia.value.id);
            toast.add({ severity: 'success', summary: 'Constancia anulada', life: 3000 });
        } else {
            await ConstanciasService.activarConstancia(constancia.value.id);
            toast.add({ severity: 'success', summary: 'Constancia emitida', life: 3000 });
        }
        await listarConstancias();
        estadoConstanciaForm.value = false;
        constancia.value = {};
    } catch (error) {
        console.error(error);
        toast.add({ severity: 'error', summary: 'Error', detail: 'No se pudo cambiar el estado', life: 3000 });
    }
}

function getEstadoLabel(status) {
    return status === "Emitida" ? "success" : "danger";
}

async function generarPDF(id) {
    const cons = constancias.value.find(c => c.id === id);
    if (!cons) {
        toast.add({ severity: "error", summary: "Error", detail: "Constancia no encontrada" });
        return;
    }

    try {
        const doc = new jsPDF({ orientation: "portrait", unit: "pt", format: "A4" });
        const logoUNAP = await loadImage(LOGO_UNAP);
        const logoCEPRE = await loadImage(LOGO_CEPRE);

        const nombreCompleto = `${cons.nombres} ${cons.ap_paterno} ${cons.ap_materno}`.trim();
        const fecha = cons.fecha_emision || "—";

        if (cons.estado === "Anulada") {
            doc.addImage(logoUNAP, "PNG", 40, 25, 70, 70);
            doc.addImage(logoCEPRE, "PNG", 500, 25, 70, 70);

            doc.setFont("Helvetica", "bold");
            doc.setFontSize(18);
            doc.text("UNIVERSIDAD NACIONAL DEL ALTIPLANO – PUNO", 300, 70, { align: "center" });
            doc.text("CEPRE – Centro Preuniversitario", 300, 95, { align: "center" });

            doc.setFillColor(180, 0, 0);
            doc.rect(0, 130, 600, 40, "F");

            doc.setFontSize(17);
            doc.setTextColor(255, 255, 255);
            doc.text("CONSTANCIA ANULADA", 300, 158, { align: "center" });

            doc.setTextColor(0, 0, 0);
            doc.setFont("Helvetica", "normal");
            doc.setFontSize(13);

            doc.text(
                `La constancia con código: ${cons.codigo}\n` +
                `Perteneciente a: ${nombreCompleto}\n` +
                `Ha sido ANULADA oficialmente.\n\n` +
                `Fecha de emisión original: ${fecha}\n\n` +
                "Este documento NO tiene validez para ningún trámite institucional.",
                300, 320, { align: "center" }
            );

            doc.setFontSize(70);
            doc.setTextColor(255, 0, 0);
            doc.setFont("Helvetica", "bold");
            doc.text("ANULADA", 300, 520, { align: "center", angle: 30 });

            window.open(doc.output("bloburl"), "_blank");
            return;
        }

        doc.addImage(logoUNAP, "PNG", 40, 25, 70, 70);
        doc.addImage(logoCEPRE, "PNG", 500, 25, 70, 70);

        doc.setFont("Helvetica", "bold");
        doc.setFontSize(15);
        doc.text("UNIVERSIDAD NACIONAL DEL ALTIPLANO – PUNO", 300, 50, { align: "center" });

        doc.setFontSize(13);
        doc.text("CEPRE – Centro Preuniversitario", 300, 70, { align: "center" });
        doc.text("Dirección Académica", 300, 88, { align: "center" });

        doc.setFillColor(10, 40, 120);
        doc.rect(0, 120, 600, 40, "F");

        doc.setTextColor(255, 255, 255);
        doc.setFontSize(16);
        doc.text("CONSTANCIA DE ESTUDIOS", 300, 147, { align: "center" });

        doc.setTextColor(0, 0, 0);

        doc.setFont("Helvetica", "bolditalic");
        doc.setFontSize(22);
        doc.text(nombreCompleto.toUpperCase(), 300, 200, { align: "center" });

        doc.setFont("Helvetica", "italic");
        doc.setFontSize(15);
        doc.text(`DNI: ${cons.dni}   |   Código: ${cons.codigo}`, 300, 225, { align: "center" });

        doc.setFont("Helvetica", "normal");
        doc.setFontSize(12);

        const texto1 =
`Por medio de la presente, la Dirección Académica del Centro Preuniversitario
de la Universidad Nacional del Altiplano – Puno, hace constar que el(la) estudiante
${nombreCompleto}, identificado(a) con DNI N° ${cons.dni}, registra la siguiente
información académica:`;

        doc.text(texto1, 40, 265, { maxWidth: 520 });

        const datos = [
            ["Ciclo:", cons.ciclo],
            ["Sede:", cons.sede],
            ["Área:", cons.area],
            ["Curso:", cons.curso],
            ["Horas:", cons.cantidad_horas],
            ["Fecha de emisión:", fecha]
        ];

        let y = 360;
        datos.forEach(([label, value]) => {
            doc.setFont("Helvetica", "bold");
            doc.text(label, 40, y);

            doc.setFont("Helvetica", "normal");
            doc.text(String(value ?? "—"), 180, y);

            y += 22;
        });

        if (cons.observaciones) {
            doc.setFont("Helvetica", "bold");
            doc.text("Observaciones:", 40, y + 20);

            doc.setFont("Helvetica", "normal");
            doc.text(cons.observaciones, 40, y + 40, { maxWidth: 520 });
            y += 70;
        }

        const texto2 =
`La presente constancia se expide a solicitud del interesado, para los fines que
estime por conveniente, en la ciudad de Puno, a los ${fecha}.`;

        doc.text(texto2, 40, y + 30, { maxWidth: 520 });

        doc.setFont("Helvetica", "normal");
        doc.text("__________________________________", 300, 680, { align: "center" });

        doc.setFont("Helvetica", "bold");
        doc.text(cons.emitido_por || "Dirección Académica – CEPRE UNAP", 300, 700, { align: "center" });

        const qrInfo =
`Validación oficial CEPRE–UNAP
ID: ${cons.id}
Código: ${cons.codigo}
DNI: ${cons.dni}
Estudiante: ${nombreCompleto}
Curso: ${cons.curso}
Fecha emisión: ${fecha}`;

        const qrData = await QRCode.toDataURL(qrInfo);
        doc.addImage(qrData, "PNG", 455, 590, 120, 120);

        doc.setFontSize(9);
        doc.setTextColor(80, 80, 80);
        doc.text(
            "Documento generado digitalmente conforme al Reglamento Académico de la UNAP. Válido sin firma física.",
            300, 815, { align: "center" }
        );

        window.open(doc.output("bloburl"), "_blank");

    } catch (error) {
        console.error(error);
        toast.add({ severity: "error", summary: "Error", detail: "No se pudo generar el PDF" });
    }
}

function loadImage(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.crossOrigin = 'Anonymous';
        img.onload = () => resolve(img);
        img.onerror = (e) => reject(e);
        img.src = url;
    });
}

function exportCSV() {
    if (dt.value && dt.value.exportCSV) dt.value.exportCSV();
}

onMounted(() => {
    listarConstancias();
});
</script>

<style scoped>
.card {
    padding: 1rem;
    border-radius: 8px;
    background: white;
}
</style>
