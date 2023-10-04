<template>
<a-row>
    <a-col :span="12">
        <a-page-header style="border: 1px solid rgb(235, 237, 240)" title="Cortes" sub-title="Ver" />
    </a-col>
    <a-col :span="12">
        <Link href="/cortes" class="ant-btn ant-btn-primary ant-btn-round ant-btn-sm" as="button">Regresar</Link>
    </a-col>
</a-row>
<a-row>
  <a-col :span="24">
    <a-descriptions title="Información" layout="vertical">
      <a-descriptions-item label="Id">{{corte.id}}</a-descriptions-item>
      <a-descriptions-item label="Cuenta">{{ account.name }}</a-descriptions-item>
      <a-descriptions-item label="Plantel">{{ plantel.name }}</a-descriptions-item>
      <a-descriptions-item label="Fecha Inicio">{{corte.fecha_inicio}}</a-descriptions-item>
      <a-descriptions-item label="Fecha Fin">{{corte.fecha_fin}}</a-descriptions-item>
      <a-descriptions-item label="S. Ingresos">{{corte.saldo_ingresos}}</a-descriptions-item>
      <a-descriptions-item label="S. Egresos">{{corte.saldo_egresos}}</a-descriptions-item>
    </a-descriptions>
  </a-col>
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

export default {
  layout: Layout,

  components: {
      Link
  },

  props: ["corte","cash_boxes", "expenses", "suma_egresos", "suma_ingresos", "plantel", 'account'],

};
</script>
