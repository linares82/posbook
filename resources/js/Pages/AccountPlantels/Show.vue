<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Account Plantel" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link :href="`/accountPlantels/${accountPlantel.plantel_id}`" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
  <a-col :span="24">
    <a-descriptions title="Información" layout="vertical">
      <a-descriptions-item label="Id">{{accountPlantel.id}}</a-descriptions-item>
      <a-descriptions-item label="Plantel">{{accountPlantel.plantel.name}}</a-descriptions-item>
      <a-descriptions-item label="Cuenta">{{accountPlantel.account.name}}</a-descriptions-item>
      <a-descriptions-item label="F. Inicio">{{accountPlantel.fecha_inicio}}</a-descriptions-item>
      <a-descriptions-item label="Saldo Ingreso">{{accountPlantel.saldo_ingresos}}</a-descriptions-item>
      <a-descriptions-item label="Saldo Egreso">{{accountPlantel.saldo_egresos}}</a-descriptions-item>
    </a-descriptions>
  </a-col>
</a-row>

<a-row>
    <a-form :model="formCorte" @submit.prevent="submitF" autocomplete="off" layout="vertical">
        <a-row :gutter="20">
            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                <a-form-item compact label="Fecha Inicio" name="fecha_inicio" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                    <a-date-picker v-model:value="formCorte.fecha_inicio" :bordered="true" />
                    <div v-if="errors.fecha_inicio">
                        <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha_inicio"></div>
                    </div>
                </a-form-item>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                <a-form-item compact label="Fecha Fin" name="fecha_fin" :rules="[{ required: true, message: 'Por favor captura la información solicitada!' }]">
                    <a-date-picker v-model:value="formCorte.fecha_fin" :bordered="true" />
                    <div v-if="errors.fecha_fin">
                        <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.fecha_fin"></div>
                    </div>
                </a-form-item>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                <a-form-item label="Saldo Ingresos" name="saldo_ingresos">
                    <a-input v-model:value="formCorte.saldo_ingresos"> </a-input>
                    <div v-if="errors.saldo_ingresos">
                        <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.saldo_ingresos"></div>
                    </div>
                </a-form-item>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                <a-form-item label="Saldo Egresos" name="saldo_egresos">
                    <a-input v-model:value="formCorte.saldo_egresos"> </a-input>
                    <div v-if="errors.saldo_egresos">
                        <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.saldo_egresos"></div>
                    </div>
                </a-form-item>
            </a-col>
            <a-col :xs="24" :sm="24" :md="12" :lg="8" :xl="6">
                <a-form-item label="Diferencia" name="diferencia">
                    <a-input v-model:value="formCorte.diferencia"> </a-input>
                    <div v-if="errors.diferencia">
                        <div role="alert" class="ant-form-item-explain-error" style="" v-text="errors.diferencia"></div>
                    </div>
                </a-form-item>
            </a-col>

        </a-row>

        <a-form-item>
            <a-button type="primary" html-type="submit" :disabled="processing">Guardar</a-button>
        </a-form-item>
    </a-form>
</a-row>

<a-row :gutter="20">
    <a-col :md="12">
        <div class="ant-table-wrapper ant-table-striped">
            <div class="ant-spin-nested-loading">
                <div class="ant-spin-container">
                    <div class="ant-table ant-table-small ant-table-bordered">
                        <!---->
                        <div class="ant-table-container">
                            <div class="ant-table-content">
                                <table style="table-layout: auto;" class="ant-table-striped">
                                    <thead>
                                        <th>Caja</th>
                                        <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Monto: {{ Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(suma_ingresos) }}</th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="linea in cash_boxes" :key="linea.cash_box_id">
                                            <td><a :href="`${ linea.url_consultar_ingreso }`" target="_blank">{{linea.cash_box_id}}</a></td>
                                            <td>{{ linea.fecha }}</td>
                                            <td >{{ linea.producto }}</td>
                                            <td align="right">{{ Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(linea.monto) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a-col>
    <a-col :md="12">
        <div class="ant-table-wrapper ant-table-striped">
            <div class="ant-spin-nested-loading">
                <div class="ant-spin-container">
                    <div class="ant-table ant-table-small ant-table-bordered">
                        <!---->
                        <div class="ant-table-container">
                            <div class="ant-table-content">
                                <table style="table-layout: auto;" class="ant-table-striped">
                                    <thead>
                                        <th>Egreso</th>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Monto:{{ Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(suma_egresos) }} </th>
                                    </thead>
                                    <tbody>
                                        <tr v-for="linea in expenses" :key="linea.expense_id">
                                            <td><a :href="`${ linea.url_consultar_egreso }`" target="_blank">{{linea.expense_id}}</a></td>
                                            <td>{{ linea.fecha }}</td>
                                            <td>{{ linea.egreso }}</td>
                                            <td align="right">{{ Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(linea.monto)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </a-col>
</a-row>

</template>

<script>
import Layout from "../../shared/Layout";
import {
    Link
} from "@inertiajs/inertia-vue3";
import {
    Inertia
} from "@inertiajs/inertia";
import {
    reactive,
    ref
} from "vue";
import dayjs from "dayjs";



export default {
  layout: Layout,

  components: {
      Link
  },



  props: ["accountPlantel", "errors", "diferencia", "cash_boxes", "expenses", "suma_egresos", "suma_ingresos"],

  setup(props) {

let formCorte = reactive({
    account_plantel_id: props.accountPlantel.id,
    fecha_inicio: dayjs(props.accountPlantel.fecha_inicio, 'YYYY-MM-DD'),
    fecha_fin: dayjs(),
    saldo_ingresos: props.accountPlantel.saldo_ingresos,
    saldo_egresos: props.accountPlantel.saldo_egresos,
    diferencia: props.diferencia,
    cash_boxes: [],
    expenses:[],
});

let processing = ref(false);

let submitF = () => {
    for (let posicion in props.cash_boxes) {
        formCorte.cash_boxes.push({
            cash_box_id: props.cash_boxes[posicion]['cash_box_id'],
        });
    }

    for (let posicion in props.expenses) {
        formCorte.expenses.push({
            expense_id: props.expenses[posicion]['expense_id'],
        });
    }

    Inertia.post("/cortes/store", formCorte, {
        onStart: () => {
            processing.value = true;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

return {
    submitF,
    formCorte,
}
}

};
</script>
